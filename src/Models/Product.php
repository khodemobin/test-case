<?php

namespace App\Models;

use DateTime;

readonly class Product
{
    public function __construct(
        public string $id,
        public string $name,
        public float $price,
        public string $category,
        public array $attributes,
        public DateTime $createdAt
    ) {}
}