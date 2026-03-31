<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="login-container">
    <div class="login-box">
        <div class="login-left">
            <h2 class="login-title">Log In</h2>
            <form action="/login" method="POST">
                <input type="email" name="email" class="login-input" placeholder="    Email" required>
                <input type="password" name="password" class="login-input" placeholder="    Password" required>
                <button type="submit" class="login-button">Sign In</button>
            </form>
            <p class="social-text">or sign in with</p>
            <div class="social-icons">
                <a href="#" class="social-icon"><img src="/assets/images/facebook-icon.png" alt="Facebook"></a>
                <a href="#" class="social-icon"><img src="/assets/images/google-icon.png" alt="Google"></a>
                <a href="#" class="social-icon"><img src="/assets/images/linkedin-icon.png" alt="LinkedIn"></a>
            </div>
        </div>
        <div class="login-right">
            <h2 class="welcome-title">Welcome back!</h2>
            <p class="welcome-text">
                We are so happy to have you here. It's great to see you again. We hope you had a safe and enjoyable time away.
            </p>
            <a href="/register" class="register-button">No account yet? Register.</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
