<?php
if ($_SESSION['role'] !== 'admin') {
    echo "Unauthorized access!";
    exit();
}
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
    <title>Edit Product - <?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <div class="container">
        <h1>Edit Product: <?= htmlspecialchars($product['name']) ?></h1>

        <form action="/product/update/<?= $product['id'] ?>" method="POST">
            <label for="name">Name:</label><br>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br><br>

            <label for="price">Price:</label><br>
            <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required><br><br>

            <label for="description">Description:</label><br>
            <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea><br><br>

            <label for="image_url">Image URL:</label><br>
            <input type="text" name="image_url" value="<?= htmlspecialchars($product['image_url']) ?>" required><br><br>

            <label for="stock">Stock Quantity:</label><br>
            <input type="number" name="stock" value="<?= $product['stock'] ?>" required><br><br>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</body>

</html>