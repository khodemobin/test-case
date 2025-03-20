<?php

namespace App\Tests;

use App\Models\CreateProductRequestDTO;

class CreateProductRequestDTOTest extends BaseTestCase
{
    public function testDTOConstructionWithValidData(): void
    {
        $data = [
            'name' => 'Test Product',
            'price' => 10.99,
            'category' => 'electronics',
            'attributes' => ['brand' => 'Test']
        ];
        $dto = new CreateProductRequestDTO($data);

        $this->assertEquals('Test Product', $dto->name);
        $this->assertEquals(10.99, $dto->price);
        $this->assertEquals('electronics', $dto->category);
        $this->assertEquals(['brand' => 'Test'], $dto->attributes);
    }

    public function testDTOConstructionWithMissingData(): void
    {
        $data = [];
        $dto = new CreateProductRequestDTO($data);

        $this->assertNull($dto->name);
        $this->assertNull($dto->price);
        $this->assertNull($dto->category);
        $this->assertNull($dto->attributes);
    }
}