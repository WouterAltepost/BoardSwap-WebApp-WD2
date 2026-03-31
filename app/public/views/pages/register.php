<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="registration-container">
    <div class="form-container">
        <div class="left-section">
            <h2 class="form-title">Register</h2>
            <form action="/register" method="POST">
                <input type="text" name="username" placeholder="    Username" required>
                <input type="email" name="email" placeholder="    Email" required>
                <input type="password" name="password" placeholder="    Password" required>
                <input type="password" name="confirm_password" placeholder="    Confirm Password" required>
                <button type="submit" class="btn-primary">Sign Up</button>
            </form>
            <p class="or-text">or sign up with</p>
            <div class="social-icons">
                <img src="assets/images/facebook-icon.png" alt="Facebook">
                <img src="assets/images/google-icon.png" alt="Instagram">
                <img src="assets/images/linkedin-icon.png" alt="LinkedIn">
            </div>
        </div>
        <div class="right-section">
            <h2 class="welcome-title">Welcome Aboard!</h2>
            <p class="welcome-text">
                Create an account and start browsing surfboards today.
                Connect with other surfers and enjoy the experience!
            </p>
            <a href="/login" class="btn-secondary">Already have an account? Log in</a>
        </div>
    </div>
</div>