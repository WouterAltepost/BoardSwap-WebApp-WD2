<?php

namespace App\Models;

class CartModel extends BaseModel
{
    public function getByUserId(int $userId): array
    {
        $stmt = self::$pdo->prepare(
            "SELECT ci.id, ci.user_id, ci.product_id, ci.quantity,
                    p.name, p.price, p.image_url
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.user_id = :user_id"
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function addItem(int $userId, int $productId, int $quantity): array
    {
        $stmt = self::$pdo->prepare(
            "SELECT id, quantity FROM cart_items WHERE user_id = :user_id AND product_id = :product_id"
        );
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $update = self::$pdo->prepare("UPDATE cart_items SET quantity = :quantity WHERE id = :id");
            $update->execute(['quantity' => $newQty, 'id' => $existing['id']]);
        } else {
            $insert = self::$pdo->prepare(
                "INSERT INTO cart_items (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)"
            );
            $insert->execute([
                'user_id'    => $userId,
                'product_id' => $productId,
                'quantity'   => $quantity,
            ]);
        }

        return $this->getByUserId($userId);
    }

    public function updateItem(int $cartItemId, int $userId, int $quantity): ?array
    {
        $stmt = self::$pdo->prepare(
            "UPDATE cart_items SET quantity = :quantity WHERE id = :id AND user_id = :user_id"
        );
        $stmt->execute([
            'quantity' => $quantity,
            'id'       => $cartItemId,
            'user_id'  => $userId,
        ]);

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $item = self::$pdo->prepare(
            "SELECT ci.id, ci.user_id, ci.product_id, ci.quantity,
                    p.name, p.price, p.image_url
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.id = :id"
        );
        $item->execute(['id' => $cartItemId]);
        return $item->fetch() ?: null;
    }

    public function removeItem(int $cartItemId, int $userId): bool
    {
        $stmt = self::$pdo->prepare("DELETE FROM cart_items WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $cartItemId, 'user_id' => $userId]);
        return $stmt->rowCount() > 0;
    }

    public function clearCart(int $userId): void
    {
        $stmt = self::$pdo->prepare("DELETE FROM cart_items WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
    }
}
