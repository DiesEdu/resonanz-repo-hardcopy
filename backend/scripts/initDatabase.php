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
        sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $host, $port),
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    echo "Connected to MySQL server" . PHP_EOL;

    $pdo->exec(sprintf(
        'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
        str_replace('`', '``', $dbName)
    ));

    echo sprintf("Database '%s' created/verified", $dbName) . PHP_EOL;

    $pdo->exec(sprintf('USE `%s`', str_replace('`', '``', $dbName)));

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `library` (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            subtitle VARCHAR(255) NOT NULL,
            composer VARCHAR(255) NOT NULL,
            genre VARCHAR(255) NOT NULL,
            difficulty ENUM('Beginner', 'Intermediate', 'Advanced', 'Expert') NOT NULL DEFAULT 'Beginner',
            pages INT(4) NOT NULL,
            description LONGTEXT NOT NULL,
            location_file LONGTEXT NOT NULL,
            score_type ENUM('Full Score', 'Orchestra Collections', 'Single') NOT NULL DEFAULT 'Full Score',
            coverImage VARCHAR(255) NOT NULL,
            instruments JSON NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_title (title),
            INDEX idx_composer (composer),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    echo "Library table created/verified" . PHP_EOL;

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_users_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    echo "Users table created/verified" . PHP_EOL;
    echo "Database initialization complete!" . PHP_EOL;
    exit(0);
} catch (PDOException $error) {
    fwrite(STDERR, 'Initialization failed: ' . $error->getMessage() . PHP_EOL);
    exit(1);
}

