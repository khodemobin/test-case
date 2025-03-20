<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use PDO;

abstract class BaseTestCase extends TestCase
{
    protected PDO $pdo;

    protected function setUp(): void
    {
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

        $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";
        $this->pdo = new PDO($dsn, $dbUser, $dbPassword, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    protected function tearDown(): void
    {
        unset($this->pdo);
    }
}