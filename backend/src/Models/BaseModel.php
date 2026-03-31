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
            $host = $_ENV['DB_HOST'];
            $db = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASSWORD'];

            $dsn = "mysql:host=$host;dbname=$db;charset=utf8";
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
