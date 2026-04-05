<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    public static function authenticate(): array
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            http_response_code(401);
            echo json_encode(["error" => "Missing or malformed Authorization header"]);
            exit();
        }

        try {
            $decoded = JWT::decode($matches[1], new Key(getenv('JWT_SECRET') ?: 'fallback_secret', 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(["error" => "Invalid or expired token"]);
            exit();
        }
    }
}
