<?php

namespace App\Models;

use App\Validation\Attributes\Required;
use App\Validation\Attributes\StringType;
use App\Validation\Attributes\PositiveNumber;

class CreateProductRequestDTO
{
    #[Required]
    #[StringType]
    public ?string $name;

    #[Required]
    #[PositiveNumber]
    public mixed $price;

    public ?string $category;

    public ?array $attributes;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? null;
        $this->price = $data['price'] ?? null;
        $this->category = $data['category'] ?? null;
        $this->attributes = $data['attributes'] ?? null;
    }
}