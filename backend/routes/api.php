<?php
/**
 * Rutas de la API
 * Sistema de enrutamiento RESTful
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/LibrosController.php';
require_once __DIR__ . '/../controllers/CategoriasController.php';
require_once __DIR__ . '/../controllers/UsuariosController.php';



/* ===============================
   CLASE ROUTER
================================ */

class Router {
    private $routes = [];
    private $notFoundCallback;

    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }

    public function put($path, $callback) {
        $this->addRoute('PUT', $path, $callback);
    }

    public function delete($path, $callback) {
        $this->addRoute('DELETE', $path, $callback);
    }

    private function addRoute($method, $path, $callback) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }

    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Quitar prefijo del proyecto
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

        if ($this->notFoundCallback) {
            call_user_func($this->notFoundCallback);
        } else {
            errorResponse('Ruta no encontrada', 404);
        }
    }

    private function convertPathToRegex($path) {
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function executeCallback($callback, $params) {
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }

        if (is_array($callback)) {
            [$controller, $method] = $callback;
            $controllerInstance = new $controller();
            return call_user_func_array([$controllerInstance, $method], $params);
        }
    }

    public function notFound($callback) {
        $this->notFoundCallback = $callback;
    }
}

/* ===============================
   DEFINICIÓN DE RUTAS
================================ */

$router = new Router();

/* ---- LIBROS ---- */
$router->get('/api/libros', ['LibrosController', 'index']);
$router->get('/api/libros/{id}', ['LibrosController', 'show']);
$router->post('/api/libros', ['LibrosController', 'create']);
$router->put('/api/libros/{id}', ['LibrosController', 'update']);
$router->delete('/api/libros/{id}', ['LibrosController', 'delete']);

/* ---- CATEGORÍAS ---- */
$router->get('/api/categorias', ['CategoriasController', 'index']);
$router->get('/api/categorias/{id}', ['CategoriasController', 'show']);
$router->post('/api/categorias', ['CategoriasController', 'create']);
$router->put('/api/categorias/{id}', ['CategoriasController', 'update']);
$router->delete('/api/categorias/{id}', ['CategoriasController', 'delete']);

/* ---- USUARIOS ---- */
$router->get('/api/usuarios', ['UsuariosController', 'index']);
$router->get('/api/usuarios/{id}', ['UsuariosController', 'show']);
$router->post('/api/usuarios', ['UsuariosController', 'create']);
$router->put('/api/usuarios/{id}', ['UsuariosController', 'update']);
$router->delete('/api/usuarios/{id}', ['UsuariosController', 'delete']);
$router->post('/api/usuarios/recuperar-password', [UsuariosController::class, 'recuperarPassword']);
$router->post('/api/usuarios/reset-password', [UsuariosController::class, 'resetPassword']);

// PUBLICO
$router->get('/api/libros', ['LibrosController', 'index']);
$router->get('/api/libros/{id}', ['LibrosController', 'show']);
$router->get('/api/libros/buscar', ['LibrosController', 'buscar']);

// ADMIN
$router->post('/api/admin/libros', ['LibrosController', 'create']);
$router->put('/api/admin/libros/{id}', ['LibrosController', 'update']);
$router->delete('/api/admin/libros/{id}', ['LibrosController', 'delete']);
$router->get('/api/admin/libros/estadisticas', ['LibrosController', 'estadisticas']);



/* ---- HEALTH CHECK ---- */
$router->get('/api', function () {
    jsonResponse([
        'mensaje' => 'API Biblioteca Virtual funcionando correctamente',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

$router->get('/api/health', function () {
    try {
        $db = Database::connect();
        $db->query("SELECT 1");
        jsonResponse([
            'status' => 'healthy',
            'database' => 'connected'
        ]);
    } catch (Exception $e) {
        jsonResponse([
            'status' => 'unhealthy',
            'error' => $e->getMessage()
        ], 500);
    }
});

/* ---- 404 ---- */
$router->notFound(function () {
    errorResponse('Endpoint no encontrado', 404);
});

/* ===============================
   EJECUTAR ROUTER
================================ */

$router->run();
