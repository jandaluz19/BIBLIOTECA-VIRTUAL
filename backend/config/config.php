<?php
/**
 * Configuración General de la Aplicación
 * Biblioteca Virtual
 */

// Prevenir acceso directo
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// ========================================
// CONFIGURACIÓN DE ENTORNO
// ========================================

define('ENVIRONMENT', 'development'); // development | production
define('DEBUG_MODE', ENVIRONMENT === 'development');

// ========================================
// CONFIGURACIÓN DE APLICACIÓN
// ========================================

define('APP_NAME', 'Biblioteca Virtual');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/BIBLIOTECA-VIRTUAL');
define('API_URL', APP_URL . '/backend');

// ========================================
// CONFIGURACIÓN DE BASE DE DATOS
// ========================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'biblioteca_virtual');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ========================================
// CONFIGURACIÓN DE SEGURIDAD
// ========================================

define('JWT_SECRET_KEY', 'tu_clave_secreta_super_segura_cambiar_en_produccion');
define('JWT_EXPIRATION', 3600); // 1 hora en segundos
define('PASSWORD_MIN_LENGTH', 8);
define('SESSION_LIFETIME', 1800); // 30 minutos

// ========================================
// CONFIGURACIÓN DE ARCHIVOS
// ========================================

define('UPLOAD_DIR', APP_ROOT . '/uploads');
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['pdf', 'jpg', 'jpeg', 'png', 'gif']);

// Crear directorio de uploads si no existe
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
    mkdir(UPLOAD_DIR . '/covers', 0755, true);
    mkdir(UPLOAD_DIR . '/pdfs', 0755, true);
    mkdir(UPLOAD_DIR . '/avatars', 0755, true);
}

// ========================================
// CONFIGURACIÓN DE PAGINACIÓN
// ========================================

define('ITEMS_PER_PAGE', 12);
define('MAX_ITEMS_PER_PAGE', 100);

// ========================================
// CONFIGURACIÓN DE PRÉSTAMOS
// ========================================

define('DIAS_PRESTAMO_DEFAULT', 14);
define('MULTA_POR_DIA', 2.00); // En la moneda local
define('MAX_LIBROS_SIMULTANEOUS', 3);

// ========================================
// CONFIGURACIÓN DE TIMEZONE
// ========================================

date_default_timezone_set('America/Lima');

// ========================================
// CONFIGURACIÓN DE ERRORES
// ========================================

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', APP_ROOT . '/logs/error.log');
}

// ========================================
// HEADERS DE SEGURIDAD Y CORS
// ========================================

// Permitir CORS desde el frontend
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400'); // 24 horas

// Headers de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Content-Type: application/json; charset=UTF-8');

// Manejar peticiones OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ========================================
// AUTOLOAD DE CLASES
// ========================================

spl_autoload_register(function ($class) {
    $directories = [
        APP_ROOT . '/models/',
        APP_ROOT . '/controllers/',
        APP_ROOT . '/middleware/',
        APP_ROOT . '/config/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ========================================
// FUNCIONES HELPER GLOBALES
// ========================================

/**
 * Respuesta JSON estandarizada
 */
function jsonResponse($data, $statusCode = 200, $message = null) {
    http_response_code($statusCode);
    
    $response = [
        'success' => $statusCode >= 200 && $statusCode < 300,
        'status' => $statusCode,
        'data' => $data
    ];
    
    if ($message !== null) {
        $response['message'] = $message;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

/**
 * Respuesta de error estandarizada
 */
function errorResponse($message, $statusCode = 400, $errors = null) {
    http_response_code($statusCode);
    
    $response = [
        'success' => false,
        'status' => $statusCode,
        'message' => $message
    ];
    
    if ($errors !== null) {
        $response['errors'] = $errors;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

/**
 * Sanitizar entrada de usuario
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validar email
 */
function isValidEmail($email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generar token aleatorio
 */
function generateToken($length = 32): string {
    return bin2hex(random_bytes($length));
}

/**
 * Hashear contraseña
 */
function hashPassword($password): string {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verificar contraseña
 */
function verifyPassword($password, $hash): bool {
    return password_verify($password, $hash);
}

/**
 * Log de actividad
 */
function logActivity($message, $level = 'INFO') {
    $logFile = APP_ROOT . '/logs/app.log';
    $logDir = dirname($logFile);
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

/**
 * Obtener input JSON del body
 */
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}

/**
 * Validar método HTTP
 */
function validateMethod($allowedMethods) {
    if (!is_array($allowedMethods)) {
        $allowedMethods = [$allowedMethods];
    }
    
    if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods)) {
        errorResponse('Método no permitido', 405);
    }
}

// ========================================
// INICIALIZACIÓN
// ========================================

// Cargar archivo de base de datos
require_once APP_ROOT . '/config/database.php';

// Log de inicio
if (DEBUG_MODE) {
    logActivity('Application initialized');
}