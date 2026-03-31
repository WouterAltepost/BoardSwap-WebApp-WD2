<?php require __DIR__ . '/../partials/header.php'; ?>

<body>
    <div class="container">
        <h1 class="page-title">Available Surfboards</h1>
        <p class="page-subtitle">
            A team based in Amsterdam with partners across the world, working with the best shapers and brands to offer and ship high-quality surfboards anywhere.
        </p>

        <div class="row">
            <?php foreach ($products as $product) : ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card">
                        <img src="<?= htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']); ?></h5>
                            <p class="card-price">$<?= htmlspecialchars($product['price']); ?></p>

                            <div class="button-group">
                                <a href="/product/<?= $product['id']; ?>" class="btn btn-outline">View Details</a>
                                <button class="btn btn-outline add-to-cart-btn" data-id="<?= $product['id']; ?>">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="/assets/js/products.js"></script>
</body>

