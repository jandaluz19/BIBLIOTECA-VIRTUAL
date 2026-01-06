<?php
/**
 * Punto de Entrada de la API
 * Biblioteca Virtual - Backend
 */

// Definir constante de raíz
define('APP_ROOT', __DIR__);

// Cargar configuración
require_once APP_ROOT . '/config/config.php';

// Aplicar CORS
CORS::handle();
CORS::securityHeaders();

// Log de petición (solo en desarrollo)
if (DEBUG_MODE) {
    logActivity(
        sprintf(
            "Request: %s %s from %s",
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REQUEST_URI'],
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        )
    );
}

// Manejar errores no capturados
set_exception_handler(function($exception) {
    logActivity("Uncaught Exception: " . $exception->getMessage(), 'ERROR');
    errorResponse(
        DEBUG_MODE ? $exception->getMessage() : 'Error interno del servidor',
        500
    );
});

set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Ejecutar rutas
try {
    require_once APP_ROOT . '/routes/api.php';
} catch (Exception $e) {
    logActivity("Router Error: " . $e->getMessage(), 'ERROR');
    errorResponse($e->getMessage(), 500);
}