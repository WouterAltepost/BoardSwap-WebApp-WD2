<?php require __DIR__ . '/../partials/header.php'; ?>

<?php
// Ensure product exists
if (!isset($product)) {
    echo "<h2>Product not found.</h2>";
    exit();
}

// Check if user is admin
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Extract product details
$productName = htmlspecialchars($product['name'] ?? 'No Name Available');
$productPrice = number_format($product['price'] ?? 0, 2);
$productImage = !empty($product['image_url']) ? $product['image_url'] : "/assets/images/default-surfboard.jpg";
$productDescription = htmlspecialchars($product['description'] ?? 'No description available.');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $productName; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <div class="container">
        <h1><?php echo $productName; ?></h1>
        <img src="<?php echo $productImage; ?>" alt="<?php echo $productName; ?>" style="max-width: 400px;">
        <p><strong>Price:</strong> $<?php echo $productPrice; ?></p>
        <p><?php echo $productDescription; ?></p>

        <div class="button-group-dp">
            <!-- Add to Favourites Form -->
            <form id="addToFavouritesForm" style="display: inline;">
                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                <button type="submit" class="btn-outline add-to-cart">Add to Favourites</button>
            </form>

            <!-- Add to Cart Form -->
            <form id="addToCartForm" action="/cart/add" method="POST" style="display: inline;">
                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                <button type="submit" class="btn-outline add-to-cart">Add to Cart</button>
            </form>
        </div>



        <?php if ($isAdmin) : ?>
            <a href="/product/update/<?php echo $product['id']; ?>" class="btn btn-primary">Edit</a>
            <form action="/product/delete/<?php echo $product['id']; ?>" method="POST" style="display:inline;">
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
            </form>
        <?php endif; ?>
    </div>

    <script src="/assets/js/product_detailed.js"></script>
</body>

</html>