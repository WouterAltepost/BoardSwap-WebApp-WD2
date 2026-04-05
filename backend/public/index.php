<?php

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load .env variables (optional — not present in production)
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        // Remove surrounding quotes
        $value = trim($value, '"\'');
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

// Parse request
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Health check endpoint
if ($uri === '/' || $uri === '/health') {
    echo json_encode(["status" => "ok"]);
    return;
}

// Route to controllers
try {
    if (str_starts_with($uri, '/api/auth')) {
        $controller = new \App\Controllers\AuthController();
        $controller->handleRequest($method, $uri);
    } elseif (str_starts_with($uri, '/api/products')) {
        $controller = new \App\Controllers\ProductController();
        $controller->handleRequest($method, $uri);
    } elseif (str_starts_with($uri, '/api/cart')) {
        $controller = new \App\Controllers\CartController();
        $controller->handleRequest($method, $uri);
    } elseif (str_starts_with($uri, '/api/users')) {
        $controller = new \App\Controllers\UserController();
        $controller->handleRequest($method, $uri);
    } elseif ($uri === '/api/surf-conditions') {
        $controller = new \App\Controllers\SurfController();
        $controller->handleRequest($method, $uri);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Route not found"], JSON_UNESCAPED_SLASHES);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(["error" => "Internal server error", "message" => $e->getMessage()], JSON_UNESCAPED_SLASHES);
}
