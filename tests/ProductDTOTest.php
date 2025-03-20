<?php

namespace App\Tests;

use App\Models\ProductDTO;

class ProductDTOTest extends BaseTestCase
{
    public function testJsonSerialization(): void
    {
        $dto = new ProductDTO('123', 'Test', 10.99, 'electronics', ['brand' => 'Test'], '2023-01-01T00:00:00Z');
        $expected = [
            'id' => '123',
            'name' => 'Test',
            'price' => 10.99,
            'category' => 'electronics',
            'attributes' => ['brand' => 'Test'],
            'createdAt' => '2023-01-01T00:00:00Z'
        ];
        $this->assertEquals($expected, $dto->jsonSerialize());
    }
}