<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Middleware\AuthMiddleware;

class UserController
{
    public function handleRequest(string $method, string $uri): void
    {
        $auth = AuthMiddleware::authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Admin access required"], JSON_UNESCAPED_SLASHES);
            return;
        }

        // Match /api/users/{id}/role
        if (preg_match('#^/api/users/(\d+)/role$#', $uri, $matches)) {
            if ($method !== 'PUT') {
                http_response_code(405);
                echo json_encode(["error" => "Method not allowed"], JSON_UNESCAPED_SLASHES);
                return;
            }
            $this->updateRole((int) $matches[1]);
            return;
        }

        // Match /api/users/{id}
        if (preg_match('#^/api/users/(\d+)$#', $uri, $matches)) {
            if ($method !== 'DELETE') {
                http_response_code(405);
                echo json_encode(["error" => "Method not allowed"], JSON_UNESCAPED_SLASHES);
                return;
            }
            $this->destroy((int) $matches[1], $auth);
            return;
        }

        // Match /api/users
        if ($uri === '/api/users') {
            if ($method !== 'GET') {
                http_response_code(405);
                echo json_encode(["error" => "Method not allowed"], JSON_UNESCAPED_SLASHES);
                return;
            }
            $this->index();
            return;
        }

        http_response_code(404);
        echo json_encode(["error" => "Route not found"], JSON_UNESCAPED_SLASHES);
    }

    private function index(): void
    {
        $userModel = new UserModel();
        $users = $userModel->getAll();

        $result = array_map(function ($user) {
            unset($user['password']);
            return $user;
        }, $users);

        echo json_encode($result, JSON_UNESCAPED_SLASHES);
    }

    private function updateRole(int $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['role']) || !in_array($data['role'], ['user', 'admin'], true)) {
            http_response_code(422);
            echo json_encode(["errors" => ["role" => "Role must be 'user' or 'admin'"]], JSON_UNESCAPED_SLASHES);
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->findById($id);

        if (!$user) {
            http_response_code(404);
            echo json_encode(["error" => "User not found"], JSON_UNESCAPED_SLASHES);
            return;
        }

        $userModel->updateRole($id, $data['role']);
        $updated = $userModel->findById($id);
        unset($updated['password']);

        echo json_encode($updated, JSON_UNESCAPED_SLASHES);
    }

    private function destroy(int $id, array $auth): void
    {
        if ((int) $auth['sub'] === $id) {
            http_response_code(403);
            echo json_encode(["error" => "Cannot delete your own account"], JSON_UNESCAPED_SLASHES);
            return;
        }

        $userModel = new UserModel();
        if (!$userModel->deleteById($id)) {
            http_response_code(404);
            echo json_encode(["error" => "User not found"], JSON_UNESCAPED_SLASHES);
            return;
        }

        echo json_encode(["message" => "User deleted"], JSON_UNESCAPED_SLASHES);
    }
}
