<?php
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class ProductController
{
    private $productModel;
    private $userModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();
    }

    public function showAllProducts()
    {
        $products = $this->productModel->getAllProducts();
        require __DIR__ . '/../views/pages/products.php';
    }

    public function showProductDetails($id)
    {
        // Ensure ID is an integer
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            echo "<h2>Invalid product ID.</h2>";
            exit();
        }

        // Fetch product from model
        $product = $this->productModel->getProductById($id);

        // Debugging Output (Remove this after testing)
        if (!$product) {
            echo "Product not found for ID: $id";
            exit();
        }

        // Pass product data to the view
        require __DIR__ . '/../views/pages/product_detailed.php';
    }

    public function deleteProduct($id)
    {
        if ($_SESSION['role'] !== 'admin') {
            echo "Unauthorized access.";
            exit();
        }

        if ($this->productModel->deleteProduct($id)) {
            header("Location: /products");
            exit();
        } else {
            echo "Error deleting product.";
        }
    }

    public function getProductDetails($id)
    {
        return $this->productModel->getProductById($id);
    }

    // Method to display the Edit Form and handle POST requests
    public function editProduct($id)
    {
        if ($_SESSION['role'] !== 'admin') {
            echo "Unauthorized access!";
            exit();
        }

        // Fetch product details from the database
        $product = $this->productModel->getProductById($id);

        if (!$product) {
            echo "Product not found!";
            exit();
        }

        // If the form is submitted (POST request)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get values from the form
            $name = $_POST['name'];
            $price = $_POST['price'];
            $description = $_POST['description'];
            $image_url = $_POST['image_url'];
            $stock = $_POST['stock'];

            // Update the product in the database
            $this->productModel->updateProduct($id, $name, $price, $description, $image_url, $stock);

            // Redirect back to the product details page
            header("Location: /product/{$id}");
            exit();
        }

        // Show the edit product form
        require __DIR__ . '/../views/pages/edit_product.php';
    }

    // Show Add Product Form
    public function showAddProductForm()
    {
        require __DIR__ . '/../views/pages/add_product.php';
    }

    // Handle Add Product Form Submission
    public function addProduct()
    {
        // Check if user is admin
        if ($_SESSION['role'] !== 'admin') {
            echo "Unauthorized access.";
            exit();
        }

        // Get form data
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $image_url = $_POST['image_url'] ?? '/assets/images/default-surfboard.jpg'; // Default image if not provided
        $stock = $_POST['stock'];

        // Add to database
        $this->productModel->addProduct($name, $price, $description, $image_url, $stock);

        // Redirect to products page after successful insertion
        header('Location: /products');
        exit();
    }

   /* public function addToCart($id)
    {
        // Ensure the product exists
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            echo "Product not found!";
            exit();
        }

        // If product exists, add it to the session cart
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]++;  // Increase quantity if the product is already in the cart
        } else {
            $_SESSION['cart'][$id] = 1;  // Otherwise, add it to the cart with a quantity of 1
        }

        // Redirect to the cart page
        header('Location: /cart');
        exit();
    }


    // Remove Product from Cart
    public function removeFromCart($id)
    {
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }

        header("Location: /cart");
        exit();
    }

    // Update Product Quantity in Cart
    public function updateCartQuantity($id, $quantity)
    {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = $quantity; // Update quantity
        }

        header("Location: /cart");
        exit();
    }

    // In ProductController.php
    public function showCart()
    {
        // Fetch the cart content from the session
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

        // Fetch product details for each item in the cart
        $cartItems = [];
        foreach ($cart as $productId => $quantity) {
            $product = $this->productModel->getProductById($productId);
            if ($product) {
                $product['quantity'] = $quantity;
                $cartItems[] = $product;
            }
        }

        // Pass the cart items to the view
        require __DIR__ . '/../views/pages/cart.php';
    }
    */
}
