<?php

namespace App\Models;

class ProductModel extends BaseModel
{
    public function getAll(array $filters = []): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['search'])) {
            $conditions[] = "(name LIKE :search OR description LIKE :search_desc)";
            $params['search'] = '%' . $filters['search'] . '%';
            $params['search_desc'] = '%' . $filters['search'] . '%';
        }
        if (isset($filters['min_price'])) {
            $conditions[] = "price >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }
        if (isset($filters['max_price'])) {
            $conditions[] = "price <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }

        $where = '';
        if (!empty($conditions)) {
            $where = 'WHERE ' . implode(' AND ', $conditions);
        }

        // Count total matching rows
        $countStmt = self::$pdo->prepare("SELECT COUNT(*) FROM products $where");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Pagination
        $page = max(1, (int) ($filters['page'] ?? 1));
        $limit = max(1, (int) ($filters['limit'] ?? 10));
        $offset = ($page - 1) * $limit;

        $stmt = self::$pdo->prepare("SELECT * FROM products $where ORDER BY id DESC LIMIT :limit OFFSET :offset");
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'  => $stmt->fetchAll(),
            'total' => $total,
        ];
    }

    public function getById(int $id): ?array
    {
        $stmt = self::$pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    public function create(string $name, float $price, string $description, string $imageUrl, int $stock): array
    {
        $stmt = self::$pdo->prepare(
            "INSERT INTO products (name, price, description, image_url, stock) VALUES (:name, :price, :description, :image_url, :stock)"
        );
        $stmt->execute([
            'name'        => $name,
            'price'       => $price,
            'description' => $description,
            'image_url'   => $imageUrl,
            'stock'       => $stock,
        ]);

        return $this->getById((int) self::$pdo->lastInsertId());
    }

    public function update(int $id, array $fields): ?array
    {
        $allowed = ['name', 'price', 'description', 'image_url', 'stock'];
        $sets = [];
        $params = ['id' => $id];

        foreach ($fields as $key => $value) {
            if (in_array($key, $allowed, true)) {
                $sets[] = "$key = :$key";
                $params[$key] = $value;
            }
        }

        if (empty($sets)) {
            return $this->getById($id);
        }

        $sql = "UPDATE products SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($params);

        return $this->getById($id);
    }

    public function deleteById(int $id): bool
    {
        $stmt = self::$pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
