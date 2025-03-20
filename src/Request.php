<?php

namespace App;

class Request
{
    private string $method;
    private string $path;
    private array $query;
    private array $body;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->query = $_GET;
        $this->body = json_decode(file_get_contents('php://input'), true) ?? [];
    }

    public function getMethod(): string { return $this->method; }
    public function getPath(): string { return $this->path; }
    public function getQuery(): array { return $this->query; }
    public function getBody(): array { return $this->body; }
}