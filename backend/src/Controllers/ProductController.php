<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Middleware\AuthMiddleware;

class ProductController
{
    public function handleRequest(string $method, string $uri): void
    {
        // Match /api/products/{id}
        if (preg_match('#^/api/products/(\d+)$#', $uri, $matches)) {
            $id = (int) $matches[1];

            switch ($method) {
                case 'GET':
                    $this->show($id);
                    return;
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

        // Match /api/products
        if ($uri === '/api/products') {
            switch ($method) {
                case 'GET':
                    $this->index();
                    return;
                case 'POST':
                    $this->store();
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
        $filters = [
            'search'    => $_GET['search'] ?? null,
            'min_price' => isset($_GET['min_price']) ? (float) $_GET['min_price'] : null,
            'max_price' => isset($_GET['max_price']) ? (float) $_GET['max_price'] : null,
            'page'      => $_GET['page'] ?? 1,
            'limit'     => $_GET['limit'] ?? 12,
        ];

        $productModel = new ProductModel();
        $result = $productModel->getAll($filters);

        $page = max(1, (int) $filters['page']);
        $limit = max(1, (int) $filters['limit']);
        $totalPages = (int) ceil($result['total'] / $limit);

        echo json_encode([
            "data"       => $result['data'],
            "pagination" => [
                "total"       => $result['total'],
                "page"        => $page,
                "limit"       => $limit,
                "total_pages" => $totalPages,
            ],
        ], JSON_UNESCAPED_SLASHES);
    }

    private function show(int $id): void
    {
        $productModel = new ProductModel();
        $product = $productModel->getById($id);

        if (!$product) {
            http_response_code(404);
            echo json_encode(["error" => "Product not found"], JSON_UNESCAPED_SLASHES);
            return;
        }

        echo json_encode($product, JSON_UNESCAPED_SLASHES);
    }

    private function store(): void
    {
        $auth = AuthMiddleware::authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Admin access required"], JSON_UNESCAPED_SLASHES);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $errors = [];
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }
        if (!isset($data['price'])) {
            $errors['price'] = 'Price is required';
        }
        if (!isset($data['stock'])) {
            $errors['stock'] = 'Stock is required';
        }

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(["errors" => $errors], JSON_UNESCAPED_SLASHES);
            return;
        }

        $productModel = new ProductModel();
        $product = $productModel->create(
            $data['name'],
            (float) $data['price'],
            $data['description'] ?? '',
            $data['image_url'] ?? '',
            (int) $data['stock']
        );

        http_response_code(201);
        echo json_encode($product, JSON_UNESCAPED_SLASHES);
    }

    private function update(int $id): void
    {
        $auth = AuthMiddleware::authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Admin access required"], JSON_UNESCAPED_SLASHES);
            return;
        }

        $productModel = new ProductModel();
        $existing = $productModel->getById($id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode(["error" => "Product not found"], JSON_UNESCAPED_SLASHES);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $product = $productModel->update($id, $data);

        echo json_encode($product, JSON_UNESCAPED_SLASHES);
    }

    private function destroy(int $id): void
    {
        $auth = AuthMiddleware::authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Admin access required"], JSON_UNESCAPED_SLASHES);
            return;
        }

        $productModel = new ProductModel();
        if (!$productModel->deleteById($id)) {
            http_response_code(404);
            echo json_encode(["error" => "Product not found"], JSON_UNESCAPED_SLASHES);
            return;
        }

        echo json_encode(["message" => "Product deleted"], JSON_UNESCAPED_SLASHES);
    }
}
