<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Middleware\AuthMiddleware;

class CartController
{
    public function handleRequest(string $method, string $uri): void
    {
        // Match /api/cart/{id}
        if (preg_match('#^/api/cart/(\d+)$#', $uri, $matches)) {
            $id = (int) $matches[1];

            switch ($method) {
                case 'PUT':
                    $this->update($id);
                    return;
                case 'DELETE':
                    $this->destroy($id);
                    return;
                default:
                    http_response_code(405);
                    echo json_encode(["error" => "Method not allowed"], JSON_UNESCAPED_SLASHES);
                    return;
            }
        }

        // Match /api/cart
        if ($uri === '/api/cart') {
            switch ($method) {
                case 'GET':
                    $this->index();
                    return;
                case 'POST':
                    $this->store();
                    return;
                case 'DELETE':
                    $this->clear();
                    return;
                default:
                    http_response_code(405);
                    echo json_encode(["error" => "Method not allowed"], JSON_UNESCAPED_SLASHES);
                    return;
            }
        }

        http_response_code(404);
        echo json_encode(["error" => "Route not found"], JSON_UNESCAPED_SLASHES);
    }

    private function index(): void
    {
        $auth = AuthMiddleware::authenticate();
        $userId = (int) $auth['sub'];

        $cartModel = new CartModel();
        echo json_encode($cartModel->getByUserId($userId), JSON_UNESCAPED_SLASHES);
    }

    private function store(): void
    {
        $auth = AuthMiddleware::authenticate();
        $userId = (int) $auth['sub'];

        $data = json_decode(file_get_contents('php://input'), true);

        $errors = [];
        if (empty($data['product_id']) || (int) $data['product_id'] < 1) {
            $errors['product_id'] = 'A valid product_id is required';
        }
        if (empty($data['quantity']) || (int) $data['quantity'] < 1) {
            $errors['quantity'] = 'Quantity must be a positive integer';
        }

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(["errors" => $errors], JSON_UNESCAPED_SLASHES);
            return;
        }

        $cartModel = new CartModel();
        $cart = $cartModel->addItem($userId, (int) $data['product_id'], (int) $data['quantity']);

        http_response_code(201);
        echo json_encode($cart, JSON_UNESCAPED_SLASHES);
    }

    private function update(int $id): void
    {
        $auth = AuthMiddleware::authenticate();
        $userId = (int) $auth['sub'];

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['quantity']) || (int) $data['quantity'] < 1) {
            http_response_code(422);
            echo json_encode(["errors" => ["quantity" => "Quantity must be a positive integer"]], JSON_UNESCAPED_SLASHES);
            return;
        }

        $cartModel = new CartModel();
        $item = $cartModel->updateItem($id, $userId, (int) $data['quantity']);

        if (!$item) {
            http_response_code(404);
            echo json_encode(["error" => "Cart item not found"], JSON_UNESCAPED_SLASHES);
            return;
        }

        echo json_encode($item, JSON_UNESCAPED_SLASHES);
    }

    private function destroy(int $id): void
    {
        $auth = AuthMiddleware::authenticate();
        $userId = (int) $auth['sub'];

        $cartModel = new CartModel();
        if (!$cartModel->removeItem($id, $userId)) {
            http_response_code(404);
            echo json_encode(["error" => "Cart item not found"], JSON_UNESCAPED_SLASHES);
            return;
        }

        echo json_encode(["message" => "Cart item removed"], JSON_UNESCAPED_SLASHES);
    }

    private function clear(): void
    {
        $auth = AuthMiddleware::authenticate();
        $userId = (int) $auth['sub'];

        $cartModel = new CartModel();
        $cartModel->clearCart($userId);

        echo json_encode(["message" => "Cart cleared"], JSON_UNESCAPED_SLASHES);
    }
}
