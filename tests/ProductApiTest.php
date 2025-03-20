<?php

namespace App\Tests;

use App\Controllers\ProductController;
use App\Repositories\PostgresProductRepository;
use App\Views\JsonResponse;
use App\Models\ProductDTOFactory;
use App\Validation\Validator;

class ProductApiTest extends BaseTestCase
{
    private ProductController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        if ($this->pdo === null) {
            $this->markTestSkipped('Database connection not available.');
        }

        $repository = new PostgresProductRepository($this->pdo);
        $view = new JsonResponse();
        $dtoFactory = new ProductDTOFactory();
        $validator = new Validator($view);
        $this->controller = new ProductController($repository, $view, $dtoFactory, $validator);

        $this->pdo->exec('TRUNCATE TABLE products RESTART IDENTITY');
    }

    public function testCreateProductWithValidData(): void
    {
        $data = [
            'name' => 'Test Product',
            'price' => 10.99,
            'category' => 'electronics',
            'attributes' => ['brand' => 'Test']
        ];

        ob_start();
        $this->controller->create($data, false);
        $output = ob_get_clean();
        $response = json_decode($output, true);

        $this->assertEquals(201, http_response_code());
        $this->assertEquals('Test Product', $response['name']);
        $this->assertEquals(10.99, $response['price']);
        $this->assertEquals('electronics', $response['category']);
        $this->assertEquals(['brand' => 'Test'], $response['attributes']);
    }

    public function testCreateProductWithMissingName(): void
    {
        $data = [
            'price' => 10.99,
            'category' => 'electronics',
            'attributes' => ['brand' => 'Test']
        ];

        ob_start();
        $this->controller->create($data, false);
        $output = ob_get_clean();
        $response = json_decode($output, true);

        $this->assertEquals(400, http_response_code());
        $this->assertEquals('This field is required', $response['error']);
    }

    public function testCreateProductWithInvalidPrice(): void
    {
        $data = [
            'name' => 'Test Product',
            'price' => -5,
            'category' => 'electronics',
            'attributes' => ['brand' => 'Test']
        ];

        ob_start();
        $this->controller->create($data, false);
        $output = ob_get_clean();
        $response = json_decode($output, true);

        $this->assertEquals(400, http_response_code());
        $this->assertEquals('This field must be a positive number', $response['error']);
    }

    public function testGetProduct(): void
    {
        $this->pdo->exec("
            INSERT INTO products (id, name, price, category, attributes, created_at)
            VALUES ('123e4567-e89b-12d3-a456-426614174000', 'Test Product', 10.99, 'electronics', '{\"brand\": \"Test\"}', '2023-01-01 00:00:00')
        ");

        ob_start();
        $this->controller->get('123e4567-e89b-12d3-a456-426614174000', false);
        $output = ob_get_clean();
        $response = json_decode($output, true);

        $this->assertEquals(200, http_response_code());
        $this->assertEquals('Test Product', $response['name']);
        $this->assertEquals(10.99, $response['price']);
    }
}