<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Env.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/LibraryRepository.php';

use App\Database;
use App\Env;
use App\LibraryRepository;

Env::load(__DIR__ . '/../.env');

$configuredOrigins = Env::get('CORS_ORIGIN', '*') ?? '*';
$requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigins = array_values(array_filter(array_map('trim', explode(',', $configuredOrigins))));
$allowAllOrigins = in_array('*', $allowedOrigins, true);
$isOriginAllowed = $requestOrigin !== '' && in_array($requestOrigin, $allowedOrigins, true);
$corsOriginHeader = '*';

if ($isOriginAllowed) {
    $corsOriginHeader = $requestOrigin;
} elseif (!$allowAllOrigins && count($allowedOrigins) > 0) {
    $corsOriginHeader = $allowedOrigins[0];
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . $corsOriginHeader);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Vary: Origin');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function jsonResponse(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function getRequestBody(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $decoded = json_decode($raw, true);

    return is_array($decoded) ? $decoded : [];
}

function validatePayload(array $body, bool $isUpdate = false): array
{
    $errors = [];
    $allowedDifficulty = ['Beginner', 'Intermediate', 'Advanced', 'Expert'];

    if (!$isUpdate || array_key_exists('title', $body)) {
        $title = isset($body['title']) ? trim((string) $body['title']) : '';
        if ($title === '') {
            $errors[] = ['field' => 'title', 'msg' => $isUpdate ? 'Title cannot be empty' : 'Title is required'];
        }
    }

    if (!$isUpdate || array_key_exists('difficulty', $body)) {
        $difficulty = $body['difficulty'] ?? null;
        if (!in_array($difficulty, $allowedDifficulty, true)) {
            $errors[] = [
                'field' => 'difficulty',
                'msg' => 'Difficulty must be one of Beginner, Intermediate, Advanced, Expert',
            ];
        }
    }

    return $errors;
}

function handlePdoError(PDOException $error): void
{
    $message = $error->getMessage();

    if (str_contains($message, '1062')) {
        jsonResponse(409, [
            'success' => false,
            'message' => 'Duplicate entry found',
            'error' => $message,
        ]);
    }

    if (str_contains($message, '1452')) {
        jsonResponse(400, [
            'success' => false,
            'message' => 'Referenced record not found',
            'error' => $message,
        ]);
    }

    if (str_contains($message, '1054')) {
        jsonResponse(400, [
            'success' => false,
            'message' => 'Invalid field in request',
            'error' => $message,
        ]);
    }

    $isDev = (Env::get('NODE_ENV', 'development') ?? 'development') === 'development';
    $payload = [
        'success' => false,
        'message' => $message !== '' ? $message : 'Internal Server Error',
    ];

    if ($isDev) {
        $payload['stack'] = $error->getTraceAsString();
    }

    jsonResponse(500, $payload);
}

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

try {
    Database::testConnection();
    $repository = new LibraryRepository(Database::connect());

    if ($method === 'GET' && $uriPath === '/') {
        jsonResponse(200, [
            'success' => true,
            'message' => 'Sheet Music Library API',
            'version' => '1.0.0',
            'endpoints' => [
                'library' => '/api/library',
                'health' => '/api/health',
            ],
        ]);
    }

    if ($method === 'GET' && $uriPath === '/api/health') {
        jsonResponse(200, [
            'success' => true,
            'message' => 'Server is Running',
            'timestamp' => gmdate('c'),
            'environment' => Env::get('NODE_ENV', 'development'),
        ]);
    }

    if ($method === 'GET' && $uriPath === '/api/library') {
        $scores = $repository->getAllScores();
        jsonResponse(200, [
            'success' => true,
            'count' => count($scores),
            'data' => $scores,
        ]);
    }

    if (preg_match('#^/api/library/(\d+)$#', $uriPath, $matches) === 1) {
        $id = (int) $matches[1];

        if ($method === 'GET') {
            $score = $repository->getScoreById($id);
            if ($score === null) {
                jsonResponse(404, [
                    'success' => false,
                    'message' => 'Score not found',
                ]);
            }

            jsonResponse(200, [
                'success' => true,
                'data' => $score,
            ]);
        }

        if ($method === 'PUT') {
            $body = getRequestBody();
            unset($body['id']);

            $validationErrors = validatePayload($body, true);
            if ($validationErrors !== []) {
                jsonResponse(400, [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validationErrors,
                ]);
            }

            $existing = $repository->getScoreById($id);
            if ($existing === null) {
                jsonResponse(404, [
                    'success' => false,
                    'message' => 'Score not found',
                ]);
            }

            if ($body !== []) {
                $repository->updateScore($id, $body);
            }

            jsonResponse(200, [
                'success' => true,
                'message' => 'Score updated successfully',
            ]);
        }

        if ($method === 'DELETE') {
            $existing = $repository->getScoreById($id);
            if ($existing === null) {
                jsonResponse(404, [
                    'success' => false,
                    'message' => 'Score not found',
                ]);
            }

            $repository->deleteScore($id);
            jsonResponse(200, [
                'success' => true,
                'message' => 'Score deleted successfully',
            ]);
        }
    }

    if ($method === 'POST' && $uriPath === '/api/library') {
        $body = getRequestBody();
        unset($body['id']);

        $validationErrors = validatePayload($body, false);
        if ($validationErrors !== []) {
            jsonResponse(400, [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validationErrors,
            ]);
        }

        $newId = $repository->addScore($body);
        $body['id'] = $newId;

        jsonResponse(201, [
            'success' => true,
            'message' => 'Score added successfully',
            'data' => $body,
        ]);
    }

    jsonResponse(404, [
        'success' => false,
        'message' => 'Not Found - ' . $uriPath,
    ]);
} catch (PDOException $error) {
    handlePdoError($error);
} catch (Throwable $error) {
    $isDev = (Env::get('NODE_ENV', 'development') ?? 'development') === 'development';
    $payload = [
        'success' => false,
        'message' => $error->getMessage() !== '' ? $error->getMessage() : 'Internal Server Error',
    ];

    if ($isDev) {
        $payload['stack'] = $error->getTraceAsString();
    }

    jsonResponse(500, $payload);
}

