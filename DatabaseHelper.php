<?php

class DatabaseHelper {
    private static ?PDO $pdo = null;

    public static function getPDOInstance() {

        if (self::$pdo !== null) {
            return self::$pdo;
        }

        // Load env variables
        self::loadEnv();

        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $port = $_ENV['DB_PORT'] ?? '5432';
        $db   = $_ENV['DB_NAME'] ?? '';
        $user = $_ENV['DB_USER'] ?? '';
        $pass = $_ENV['DB_PASS'] ?? '';

        $dsn = "pgsql:host=$host;port=$port;dbname=$db";

        try {
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $e) {
            die("Database connection failed.");
        }

        return self::$pdo;
    }

    private static function loadEnv() {
        $path = __DIR__ . '/.env';

        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $_ENV[$key] = trim($value);
        }
    }
}



?>
