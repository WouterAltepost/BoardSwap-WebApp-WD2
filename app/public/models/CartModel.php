<?php
require_once __DIR__ . '/BaseModel.php';

class CartModel extends BaseModel
{
    public function __construct()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // Add product to cart
    public function addProduct($productId, $quantity)
    {
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity; // Increase quantity if product is already in the cart
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
    }

    // Remove product from cart
    public function removeProduct($productId)
    {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
    }

    // Update product quantity in cart
    public function updateProductQuantity($productId, $quantity)
    {
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] = $quantity;
        }
    }

    // Get all cart products
    public function getCart()
    {
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    }
}
