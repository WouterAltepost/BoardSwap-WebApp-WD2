<?php
require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel
{
    public function register($username, $email, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'user')";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword
        ]);
    }

    public function login($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // Debugging
        if (!$user) {
            echo "User not found in database";
            return false;
        }

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        } else {
            echo "Password incorrect"; // Add this for debugging
        }

        return false;
    }


    public function isAdmin($userId)
    {
        $stmt = $this->pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        return $user && $user['role'] === 'admin';
    }
}
