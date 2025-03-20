<?php

namespace App\Models;

class ProductDTOFactory
{
    public function createFromProduct(Product $product): ProductDTO
    {
        return new ProductDTO(
            $product->id,
            $product->name,
            $product->price,
            $product->category,
            $product->attributes,
            $product->createdAt->format('c')
        );
    }

    public function createCollection(array $products): array
    {
        return array_map([$this, 'createFromProduct'], $products);
    }
}