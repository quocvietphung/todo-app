<?php

namespace Core;

class Router {
    private array $routes = [];

    public function add(string $path, callable $handler, string $method = 'GET'): void {
        $this->routes[] = compact('path', 'handler', 'method');
    }

    public function dispatch(): void {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($uri === $route['path'] && $method === $route['method']) {
                echo call_user_func($route['handler']);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }
}