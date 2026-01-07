<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/LibrosController.php';
require_once __DIR__ . '/../helpers/response.php';

class Router {
    private $routes = [];
    private $notFoundCallback;

    public function get($path, $callback) { $this->addRoute('GET', $path, $callback); }
    private function addRoute($method, $path, $callback) {
        $this->routes[] = ['method' => $method, 'path' => $path, 'callback' => $callback];
    }

    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = preg_replace('#^/BIBLIOTECA-VIRTUAL/backend/routes/api.php#', '', $uri);
        $uri = $uri === '' ? '/' : $uri;

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            $pattern = $this->convertPathToRegex($route['path']);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                return $this->executeCallback($route['callback'], $matches);
            }
        }
        errorResponse('Endpoint no encontrado', 404);
    }

    private function convertPathToRegex($path) {
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function executeCallback($callback, $params) {
        if (is_array($callback)) {
            [$controller, $method] = $callback;
            $controllerInstance = new $controller();
            return call_user_func_array([$controllerInstance, $method], $params);
        }
    }
}

/* ===============================
   RUTAS
================================ */
$router = new Router();
$router->get('/api/libros', ['LibrosController', 'index']);
$router->get('/api/libros/{id}', ['LibrosController', 'show']);
$router->run();
