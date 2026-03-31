<?php
require_once __DIR__ . '/BaseModel.php';

class ProductModel extends BaseModel
{
    public function getAllProducts()
    {
        $sql = "SELECT * FROM products";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getProductById($id)
    {
        $sql = "SELECT * FROM products WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC); // Ensure associative array

        return $product ?: null; // Return null if not found
    }

    public function updateProduct($id, $name, $price, $description, $image_url, $stock)
    {
        $sql = "UPDATE products SET name = ?, price = ?, description = ?, image_url = ?, stock = ? WHERE id = ?";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([$name, $price, $description, $image_url, $stock, $id]);
    }

    public function deleteProduct($id)
    {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function addProduct($name, $price, $description, $image_url, $stock)
    {
        $sql = "INSERT INTO products (name, price, description, image_url, stock) VALUES (:name, :price, :description, :image_url, :stock)";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':price' => $price,
            ':description' => $description,
            ':image_url' => $image_url,
            ':stock' => $stock
        ]);
    }
}
