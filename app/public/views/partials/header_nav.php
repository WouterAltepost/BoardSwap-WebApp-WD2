<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['REQUEST_URI']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user'; // Ensure role is always set
?>

<nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">BoardSwap</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/') ? 'active' : '' ?>" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/products') ? 'active' : '' ?>" href="/products">Products</a>
                </li>

                <?php if (isset($_SESSION['user_id'])) : ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/cart') ? 'active' : '' ?>" href="/cart">Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/favourites') ? 'active' : '' ?>" href="/favourites">Favourites</a>
                    </li>
                    <?php if ($role === 'admin') : ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/add') ? 'active' : '' ?>" href="/add_product">Add Products</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Logout</a>
                    </li>
                <?php else : ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/login') ? 'active' : '' ?>" href="/login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/register') ? 'active' : '' ?>" href="/register">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>