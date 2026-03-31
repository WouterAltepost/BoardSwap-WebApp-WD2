<?php

namespace App\Models;

class UserModel extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        $stmt = self::$pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = self::$pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(string $username, string $email, string $hashedPassword, string $role = 'user'): int
    {
        $stmt = self::$pdo->prepare(
            "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)"
        );
        $stmt->execute([
            'username' => $username,
            'email'    => $email,
            'password' => $hashedPassword,
            'role'     => $role,
        ]);
        return (int) self::$pdo->lastInsertId();
    }

    public function getAll(): array
    {
        $stmt = self::$pdo->query("SELECT * FROM users");
        return $stmt->fetchAll();
    }

    public function deleteById(int $id): bool
    {
        $stmt = self::$pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function updateRole(int $id, string $role): bool
    {
        $stmt = self::$pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
        $stmt->execute(['id' => $id, 'role' => $role]);
        return $stmt->rowCount() > 0;
    }
}
