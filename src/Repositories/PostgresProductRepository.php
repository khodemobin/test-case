<?php

namespace App\Repositories;

use App\Models\Product;
use PDO;
use DateTime;

class PostgresProductRepository implements ProductRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function save(Product $product): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO products (id, name, price, category, attributes, created_at)
            VALUES (:id, :name, :price, :category, :attributes, :created_at)
        ');
        $stmt->execute([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'category' => $product->category,
            'attributes' => json_encode($product->attributes),
            'created_at' => $product->createdAt->format('Y-m-d H:i:s')
        ]);
    }

    public function findById(string $id): ?Product
    {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->createProductFromRow($row) : null;
    }

    public function update(string $id, array $data): void
    {
        $setParts = $this->buildUpdateSetClause($data);
        if (empty($setParts)) return;
        $params = array_merge($data, ['id' => $id]);
        $stmt = $this->pdo->prepare("UPDATE products SET {$setParts} WHERE id = :id");
        $stmt->execute($params);
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function findAll(array $filters): array
    {
        [$whereClause, $params] = $this->buildFilterClause($filters);
        $sql = "SELECT * FROM products{$whereClause}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return array_map([$this, 'createProductFromRow'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @throws \DateMalformedStringException
     * @throws \JsonException
     */
    private function createProductFromRow(array $row): Product
    {
        return new Product(
            $row['id'],
            $row['name'],
            (float)$row['price'],
            $row['category'],
            json_decode($row['attributes'], true, 512, JSON_THROW_ON_ERROR),
            new DateTime($row['created_at'])
        );
    }

    private function buildUpdateSetClause(array $data): string
    {
        $set = [];
        if (isset($data['name'])) {
            $set[] = 'name = :name';
        }
        if (isset($data['price'])) {
            $set[] = 'price = :price';
        }
        if (isset($data['category'])) {
            $set[] = 'category = :category';
        }
        if (isset($data['attributes'])) {
            $set[] = 'attributes = :attributes';
        }
        return implode(', ', $set);
    }

    private function buildFilterClause(array $filters): array
    {
        $where = [];
        $params = [];
        if (isset($filters['category'])) {
            $where[] = 'category = :category';
            $params['category'] = $filters['category'];
        }
        if (isset($filters['price_min'])) {
            $where[] = 'price >= :price_min';
            $params['price_min'] = (float)$filters['price_min'];
        }
        if (isset($filters['price_max'])) {
            $where[] = 'price <= :price_max';
            $params['price_max'] = (float)$filters['price_max'];
        }
        $whereClause = empty($where) ? '' : ' WHERE ' . implode(' AND ', $where);
        return [$whereClause, $params];
    }
}