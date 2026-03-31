<?php
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/CartApiController.php';
require_once __DIR__ . '/../controllers/FavouriteApiController.php';

Route::add('/products', function () {
    $controller = new ProductController();
    $controller->showAllProducts();
}, 'get');

Route::add('/product/([0-9]+)', function ($id) {
    $controller = new ProductController();
    $controller->showProductDetails($id);
}, 'get');

Route::add('/product/update/([0-9]+)', function ($id) {
    $controller = new ProductController();
    $controller->editProduct($id);
}, ['get', 'post']);

Route::add('/product/delete/([0-9]+)', function ($id) {
    $controller = new ProductController();
    $controller->deleteProduct($id);
}, 'post');

Route::add('/add_product', function () {
    $controller = new ProductController();
    $controller->showAddProductForm();
}, 'get');

Route::add('/add_product', function () {
    $controller = new ProductController();
    $controller->addProduct();
}, 'post');

// API Routes for CART
Route::add('/cart', function () {
    require __DIR__ . '/../views/pages/cart.php';
}, 'get');

Route::add('/cart/get', function () {
    $controller = new CartApiController();
    $controller->getCart();
}, 'get');

Route::add('/cart/add', function () {
    $controller = new CartApiController();
    $controller->addToCart();
}, 'post');

Route::add('/cart/remove', function () {
    $controller = new CartApiController();
    $controller->removeFromCart();
}, 'post');

Route::add('/cart/update', function () {
    $controller = new CartApiController();
    $controller->updateCartQuantity();
}, 'post');

// API Routes for FAVOURITES
Route::add('/favourites/add', function () {
    $controller = new FavouriteApiController();
    $controller->addToFavourites();
}, 'post');

Route::add('/favourites/remove/([0-9]+)', function ($id) {
    $controller = new FavouriteApiController();
    $controller->removeFavourite($id);
}, ['delete']);

Route::add('/favourites/get', function () {
    FavouriteApiController::getFavourites();
}, 'get');

Route::add('/favourites', function () {
    require_once __DIR__ . '/../views/pages/favourites.php';
});
