<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Env.php';

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

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(120) NULL,
      email VARCHAR(255) NOT NULL UNIQUE,
      role ENUM('admin', 'librarian', 'member') NOT NULL DEFAULT 'member',
      password_hash VARCHAR(255) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX idx_users_email (email),
      INDEX idx_users_role (role)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $roleColumnExistsStmt = $pdo->query("SELECT COUNT(*) AS total FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'role'");
    $roleColumn = $roleColumnExistsStmt->fetch();
    $roleExists = (int) ($roleColumn['total'] ?? 0) > 0;

    if (!$roleExists) {
        $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('admin', 'librarian', 'member') NOT NULL DEFAULT 'member' AFTER email");
        $pdo->exec("CREATE INDEX idx_users_role ON users (role)");
        echo "Users role column added" . PHP_EOL;
    }

    $arrangerColumnExistsStmt = $pdo->query("
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'library'
    AND COLUMN_NAME = 'arranger'
    LIMIT 1
");

    $arrangerExists = $arrangerColumnExistsStmt->fetch(PDO::FETCH_ASSOC) !== false;

    if (!$arrangerExists) {
        $pdo->exec("ALTER TABLE `library` ADD COLUMN arranger VARCHAR(255) NULL AFTER composer");
        echo "Library arranger column added" . PHP_EOL;
    }

    $genreColumnExistsStmt = $pdo->query("
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'library'
    AND COLUMN_NAME = 'genre'
    LIMIT 1
");

    $genreExists = $genreColumnExistsStmt->fetch(PDO::FETCH_ASSOC) !== false;

    if ($genreExists) {
        $pdo->exec("ALTER TABLE `library` MODIFY COLUMN genre VARCHAR(255) NOT NULL AFTER arranger");
        echo "Library genre column updated" . PHP_EOL;
    }

    echo "Users, Library table created/verified" . PHP_EOL;
    echo "Database adjustment complete!" . PHP_EOL;
    exit(0);
} catch (PDOException $error) {
    fwrite(STDERR, 'Adjustment failed: ' . $error->getMessage() . PHP_EOL);
    exit(1);
}
