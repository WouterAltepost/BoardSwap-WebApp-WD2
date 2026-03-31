<?php require __DIR__ . '/../partials/header.php'; ?>

<?php if ($_SESSION['role'] !== 'admin') {
    echo "<h2>Unauthorized access</h2>";
    exit();
} ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <div class="container">
        <h1>Add New Product</h1>
        <form action="/add_product" method="POST">
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" required><br><br>

            <label for="price">Price:</label><br>
            <input type="number" id="price" name="price" required><br><br>

            <label for="description">Description:</label><br>
            <textarea id="description" name="description" required></textarea><br><br>

            <label for="image_url">Image URL:</label><br>
            <input type="text" id="image_url" name="image_url"><br><br>

            <label for="stock">Stock Quantity:</label><br>
            <input type="number" id="stock" name="stock" required><br><br>

            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>
</body>

</html>