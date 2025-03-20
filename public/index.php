<?php

require __DIR__ . './../vendor/autoload.php';

use App\Router;
use App\Request;
use App\Controllers\ProductController;
use App\Repositories\PostgresProductRepository;
use App\Views\JsonResponse;
use App\Models\ProductDTOFactory;
use App\Validation\Validator;
use Dotenv\Dotenv;

if (!file_exists(__DIR__ . '/../.env')) {
    die('Error: .env file not found in ' . __DIR__ . '/../');
}

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$dbHost = $_ENV['DB_HOST'] ?: 'localhost';
$dbName = $_ENV['DB_NAME'] ?: 'product_db';
$dbUser = $_ENV['DB_USER'] ?: 'your_username';
$dbPassword = $_ENV['DB_PASSWORD'] ?: 'your_password';
$dbPort = $_ENV['DB_PORT'] ?: '5432';

// Create PDO instance with environment variables
$dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";
$pdo = new PDO($dsn, $dbUser, $dbPassword, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

// Dependency injection
$repository = new PostgresProductRepository($pdo);
$view = new JsonResponse();
$dtoFactory = new ProductDTOFactory();
$validator = new Validator($view);
$controller = new ProductController($repository, $view, $dtoFactory, $validator);
$router = new Router($controller);

$request = new Request();
$router->dispatch($request);