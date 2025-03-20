<?php

namespace App\Repositories;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function save(Product $product): void;
    public function findById(string $id): ?Product;
    public function update(string $id, array $data): void;
    public function delete(string $id): void;
    public function findAll(array $filters): array;
}