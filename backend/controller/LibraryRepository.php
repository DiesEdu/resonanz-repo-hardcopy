<?php

declare(strict_types=1);

namespace App;

use PDO;

final class LibraryRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAllScores(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM `library`');
        $rows = $stmt->fetchAll();

        return array_map([$this, 'normalizeRow'], $rows);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getScoreById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `library` WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->normalizeRow($row);
    }

    public function addScore(array $data): int
    {
        if (array_key_exists('instruments', $data)) {
            $data['instruments'] = $this->encodeInstruments($data['instruments']);
        }

        $fields = array_keys($data);
        $columns = implode(', ', array_map(static fn(string $field): string => sprintf('`%s`', $field), $fields));
        $placeholders = implode(', ', array_map(static fn(string $field): string => sprintf(':%s', $field), $fields));

        $sql = sprintf('INSERT INTO `library` (%s) VALUES (%s)', $columns, $placeholders);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function updateScore(int $id, array $data): bool
    {
        if (array_key_exists('instruments', $data)) {
            $data['instruments'] = $this->encodeInstruments($data['instruments']);
        }

        $assignments = implode(', ', array_map(static fn(string $field): string => sprintf('`%s` = :%s', $field, $field), array_keys($data)));
        $sql = sprintf('UPDATE `library` SET %s WHERE id = :id', $assignments);

        $payload = $data;
        $payload['id'] = $id;

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($payload);
    }

    public function deleteScore(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM `library` WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function normalizeRow(array $row): array
    {
        if (array_key_exists('instruments', $row)) {
            $decoded = json_decode((string) $row['instruments'], true);
            $row['instruments'] = is_array($decoded) ? $decoded : [];
        }

        return $row;
    }

    private function encodeInstruments(mixed $instruments): string
    {
        if (is_string($instruments)) {
            return $instruments;
        }

        $encoded = json_encode($instruments, JSON_UNESCAPED_UNICODE);

        return $encoded !== false ? $encoded : '[]';
    }
}
