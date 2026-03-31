<?php
require_once __DIR__ . '/../lib/Route.php';
require_once __DIR__ . '/../controllers/UserController.php';

Route::add('/register', function () {
    $controller = new UserController();
    $controller->register();
}, 'get');

Route::add('/register', function () {
    $controller = new UserController();
    $controller->register();
}, 'post');

Route::add('/login', function () {
    // Load the login page for GET requests
    require __DIR__ . '/../views/pages/login.php';
}, 'get');

Route::add('/login', function () {
    // Process login on POST request
    $controller = new UserController();
    $controller->login();
}, 'post');

Route::add('/logout', function () {
    $controller = new UserController();
    $controller->logout();
}, 'get');
