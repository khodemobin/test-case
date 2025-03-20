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
                $this->executeHandlerWithParameters($route['handler'], $request, $route['path']);
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

    private function matchesRoute(array $route, Request $request): bool|array
    {
        if ($route['method'] !== $request->getMethod()) return false;
        $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route['path']);
        return preg_match("#^$pattern$#", $request->getPath(), $matches) ? $matches : false;
    }

    private function executeHandlerWithParameters(callable $handler, Request $request, string $path): void
    {
        $matches = [];
        // Generate the pattern from the route path
        $pattern = "#^" . preg_replace('#\{[^/]+\}#', '([^/]+)', $path) . "$#";
        // Perform the matching
        if (preg_match($pattern, $request->getPath(), $matches)) {
            array_shift($matches);

            // Check the number of arguments expected by the handler
            $reflection = new \ReflectionFunction($handler);
            $expectedParams = $reflection->getNumberOfParameters();

            // Call the handler with the correct number of arguments
            if ($expectedParams === 1) {
                $handler($request);
            } else {
                $handler($request, ...$matches);
            }
        } else {
            // Debugging information
            error_log("Pattern: $pattern");
            error_log("Request Path: " . $request->getPath());
            error_log("Matches: " . print_r($matches, true));

            // Handle the case where the route does not match
            $this->notFound();
        }
    }

    private function notFound(): never
    {
        header('Content-Type: application/json', true, 404);
        echo json_encode(['error' => 'Route not found']);
        exit;
    }
}