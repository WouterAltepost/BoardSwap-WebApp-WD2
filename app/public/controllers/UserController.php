<?php
require_once __DIR__ . '/../models/UserModel.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            if ($this->userModel->register($username, $email, $password)) {
                header("Location: /login");
                exit();
            } else {
                echo "Error registering user.";
            }
        }

        require __DIR__ . '/../views/pages/register.php';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Redirect to login form if accessed via GET
            header("Location: /login");
            exit();
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->login($email, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Ensure role is stored in session

            //echo "Session Set: " . print_r($_SESSION, true); // Debugging
            header("Location: /products");
            exit();
        } else {
            echo "Invalid login credentials.";
        }
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();
        header("Location: /");
        exit();
    }
}
