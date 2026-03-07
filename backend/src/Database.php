<?php

declare(strict_types=1);

namespace App;

use PDO;

final class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $host = Env::get('DB_HOST', 'localhost');
        $port = Env::get('DB_PORT', '3306');
        $dbName = Env::get('DB_NAME', 'resonanz_music_library');
        $user = Env::get('DB_USER', 'root');
        $password = Env::get('DB_PASSWORD', 'tester');

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $dbName);
        self::$connection = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return self::$connection;
    }

    public static function testConnection(): void
    {
        self::connect()->query('SELECT 1');
    }
}
