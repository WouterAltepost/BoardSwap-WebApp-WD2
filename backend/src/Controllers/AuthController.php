<?php

namespace App\Controllers;

use App\Models\UserModel;
use Firebase\JWT\JWT;

class AuthController
{
    public function handleRequest(string $method, string $uri): void
    {
        if ($uri === '/api/auth/register') {
            if ($method !== 'POST') {
                http_response_code(405);
                echo json_encode(["error" => "Method not allowed"], JSON_UNESCAPED_SLASHES);
                return;
            }
            $this->register();
        } elseif ($uri === '/api/auth/login') {
            if ($method !== 'POST') {
                http_response_code(405);
                echo json_encode(["error" => "Method not allowed"], JSON_UNESCAPED_SLASHES);
                return;
            }
            $this->login();
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Route not found"], JSON_UNESCAPED_SLASHES);
        }
    }

    private function register(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $errors = [];
        if (empty($data['username'])) {
            $errors['username'] = 'Username is required';
        }
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        }

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(["errors" => $errors], JSON_UNESCAPED_SLASHES);
            return;
        }

        $userModel = new UserModel();

        if ($userModel->findByEmail($data['email'])) {
            http_response_code(409);
            echo json_encode(["error" => "Email already exists"], JSON_UNESCAPED_SLASHES);
            return;
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $id = $userModel->create($data['username'], $data['email'], $hashedPassword);

        http_response_code(201);
        echo json_encode([
            "message" => "User registered",
            "user" => [
                "id"       => $id,
                "username" => $data['username'],
                "email"    => $data['email'],
                "role"     => "user",
            ],
        ], JSON_UNESCAPED_SLASHES);
    }

    private function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $errors = [];
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        }
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        }

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(["errors" => $errors], JSON_UNESCAPED_SLASHES);
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            http_response_code(401);
            echo json_encode(["error" => "Invalid email or password"], JSON_UNESCAPED_SLASHES);
            return;
        }

        $now = time();
        $payload = [
            'sub'   => $user['id'],
            'email' => $user['email'],
            'role'  => $user['role'],
            'iat'   => $now,
            'exp'   => $now + 3600,
        ];

        $token = JWT::encode($payload, getenv('JWT_SECRET') ?: 'fallback_secret', 'HS256');

        echo json_encode([
            "token" => $token,
            "user" => [
                "id"       => $user['id'],
                "username" => $user['username'],
                "email"    => $user['email'],
                "role"     => $user['role'],
            ],
        ], JSON_UNESCAPED_SLASHES);
    }
}
