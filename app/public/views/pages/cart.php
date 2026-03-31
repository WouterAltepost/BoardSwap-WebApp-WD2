<?php include __DIR__ . '/../partials/header.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>

    <div class="container">
        <h1>Your Cart</h1>
        <table class="table" id="cartTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="cartBody">
                <!-- Cart items will be injected here via JS -->
            </tbody>
        </table>

        <p><strong>Total: $<span id="cartTotal">0.00</span></strong></p>
        <a href="/checkout" class="btn btn-success">Proceed to Checkout</a>
    </div>
    <script src="/assets/js/cart.js"></script>
</body>

</html>