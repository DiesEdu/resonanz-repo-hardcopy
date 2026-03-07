<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Env.php';

use App\Env;

Env::load(__DIR__ . '/../.env');

$host = Env::get('DB_HOST', 'localhost');
$port = Env::get('DB_PORT', '3306');
$dbName = Env::get('DB_NAME', 'resonanz_music_library');
$user = Env::get('DB_USER', 'root');
$password = Env::get('DB_PASSWORD', '');

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $dbName),
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    echo "Connected to MySQL server" . PHP_EOL;
    echo sprintf("Database '%s' verified", $dbName) . PHP_EOL;

    $pdo->exec("ALTER TABLE `library`
      DROP COLUMN subtitle,
      ADD COLUMN subtitle VARCHAR(255) NOT NULL AFTER title,
      DROP COLUMN score_type,
      ADD COLUMN score_type ENUM('Full Score', 'Orchestra Collections', 'Single') NOT NULL DEFAULT 'Full Score' AFTER location_file");

    echo "Library table updated! (subtitle and score_type columns added)" . PHP_EOL;
    echo "Database adjustment complete!" . PHP_EOL;
    exit(0);
} catch (PDOException $error) {
    fwrite(STDERR, 'Adjustment failed: ' . $error->getMessage() . PHP_EOL);
    exit(1);
}

