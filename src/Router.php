<?php

namespace App;

use App\Controllers\ProductController;

class Router
{
    private array $routes = [];

    public function __construct(
        private readonly ProductController $productController
    ) {
        $this->registerRoutes();
    }

    public function dispatch(Request $request): void
    {
        foreach ($this->routes as $route) {
            if ($this->matchesRoute($route, $request)) {
                $this->executeHandler($route['handler'], $request);
                return;
            }
        }
        $this->notFound();
    }

    private function registerRoutes(): void
    {
        $this->routes = [
            ['method' => 'POST', 'path' => '/products', 'handler' => fn($req) => $this->productController->create($req->getBody())],
            ['method' => 'GET', 'path' => '/products/{id}', 'handler' => fn($req, $id) => $this->productController->get($id)],
            ['method' => 'PATCH', 'path' => '/products/{id}', 'handler' => fn($req, $id) => $this->productController->update($id, $req->getBody())],
            ['method' => 'DELETE', 'path' => '/products/{id}', 'handler' => fn($req, $id) => $this->productController->delete($id)],
            ['method' => 'GET', 'path' => '/products', 'handler' => fn($req) => $this->productController->list($req->getQuery())],
        ];
    }

    private function matchesRoute(array $route, Request $request): bool
    {
        if ($route['method'] !== $request->getMethod()) return false;
        $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route['path']);
        return preg_match("#^$pattern$#", $request->getPath(), $matches) ? $matches : false;
    }

    private function executeHandler(callable $handler, Request $request): void
    {
        $matches = [];
        preg_match("#^" . preg_replace('#\{[^/]+\}#', '([^/]+)', $request->getPath()) . "$#", $request->getPath(), $matches);
        array_shift($matches);
        $handler($request, ...$matches);
    }

    private function notFound(): never
    {
        header('Content-Type: application/json', true, 404);
        echo json_encode(['error' => 'Route not found']);
        exit;
    }
}