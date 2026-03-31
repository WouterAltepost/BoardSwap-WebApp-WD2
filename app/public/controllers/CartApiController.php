<?php
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


class CartApiController
{
    private $cartModel;
    private $productModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
    }

    // Get cart contents as JSON
    public function getCart()
    {
        $cart = $this->cartModel->getCart();
        $cartDetails = [];

        foreach ($cart as $productId => $quantity) {
            $product = $this->productModel->getProductById($productId);
            if ($product) {
                $product['quantity'] = $quantity;
                $product['total'] = $product['price'] * $quantity;
                $cartDetails[] = $product;
            }
        }

        echo json_encode([
            'status' => 'success',
            'cart' => $cartDetails
        ]);
    }


    // Add product to cart
    public function addToCart()
    {
        $productId = $_POST['product_id'];
        $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;

        // Fetch product from the database
        $product = $this->productModel->getProductById($productId);

        if ($product) {
            // Add to the cart (session)
            $this->cartModel->addProduct($productId, $quantity);

            echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Product not found']);
        }
    }

    // Remove product from cart
    public function removeFromCart()
    {
        $productId = $_POST['product_id'];
        $this->cartModel->removeProduct($productId);

        echo json_encode(['status' => 'success', 'message' => 'Product removed from cart']);
    }

    // Update product quantity in cart
    public function updateCartQuantity()
    {
        $productId = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        if ($quantity <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid quantity']);
            return;
        }

        $this->cartModel->updateProductQuantity($productId, $quantity);

        echo json_encode(['status' => 'success', 'message' => 'Cart updated']);
    }
}
