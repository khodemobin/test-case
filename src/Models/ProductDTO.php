<?php

namespace App\Models;

use JsonSerializable;

readonly class ProductDTO implements JsonSerializable
{
    public function __construct(
        public string $id,
        public string $name,
        public float $price,
        public string $category,
        public array $attributes,
        public string $createdAt
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'category' => $this->category,
            'attributes' => $this->attributes,
            'createdAt' => $this->createdAt
        ];
    }
}