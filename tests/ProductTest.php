<?php

namespace App\Tests;

use App\Models\Product;
use DateTime;

class ProductTest extends BaseTestCase
{
    public function testProductCreation(): void
    {
        $product = new Product('123', 'Test Product', 10.99, 'electronics', ['brand' => 'Test'], new DateTime());
        $this->assertEquals('123', $product->id);
        $this->assertEquals('Test Product', $product->name);
    }
}