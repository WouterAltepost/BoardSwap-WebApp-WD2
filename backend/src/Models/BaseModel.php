<?php

namespace App\Models;

use PDO;
use PDOException;
use RuntimeException;

class BaseModel
{
    protected static ?PDO $pdo = null;

    public function __construct()
    {
        if (!self::$pdo) {
            $host     = getenv('DB_HOST') ?: 'localhost';
            $db       = getenv('DB_NAME') ?: 'developmentdb';
            $user     = getenv('DB_USER') ?: 'developer';
            $pass     = getenv('DB_PASSWORD') ?: 'secret123';
            $charset  = getenv('DB_CHARSET') ?: 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                throw new RuntimeException("Database connection failed: " . $e->getMessage());
            }
        }
    }
}
