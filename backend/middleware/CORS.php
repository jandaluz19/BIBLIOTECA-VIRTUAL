<?php
/**
 * Middleware CORS
 * Maneja las cabeceras Cross-Origin Resource Sharing
 */

class CORS {
    
    /**
     * Aplicar headers CORS
     */
    public static function handle() {
        // Obtener origen de la petición
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
        
        // Orígenes permitidos (en producción, especificar dominios específicos)
        $allowedOrigins = [
            'http://localhost',
            'http://localhost:3000',
            'http://localhost:8080',
            'http://127.0.0.1',
            '*' // En desarrollo, permitir todos (cambiar en producción)
        ];
        
        // Verificar si el origen está permitido
        if (in_array($origin, $allowedOrigins) || in_array('*', $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
        }
        
        // Métodos HTTP permitidos
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
        
        // Headers permitidos
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin');
        
        // Permitir credenciales
        header('Access-Control-Allow-Credentials: true');
        
        // Tiempo de cache para preflight requests
        header('Access-Control-Max-Age: 86400'); // 24 horas
        
        // Manejar preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }
    
    /**
     * Aplicar headers de seguridad adicionales
     */
    public static function securityHeaders() {
        // Prevenir MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Prevenir clickjacking
        header('X-Frame-Options: DENY');
        
        // Habilitar protección XSS del navegador
        header('X-XSS-Protection: 1; mode=block');
        
        // Política de referrer
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Content Security Policy (ajustar según necesidades)
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'");
    }
}