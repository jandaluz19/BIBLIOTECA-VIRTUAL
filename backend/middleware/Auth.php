<?php
/**
 * Middleware de Autenticación
 * Verifica tokens y permisos de usuario
 */

class Auth {
    
    /**
     * Verificar si el usuario está autenticado
     */
    public static function check() {
        $token = self::getToken();
        
        if (!$token) {
            errorResponse('Token no proporcionado', 401);
        }
        
        $usuario = self::validateToken($token);
        
        if (!$usuario) {
            errorResponse('Token inválido o expirado', 401);
        }
        
        return $usuario;
    }
    
    /**
     * Verificar si el usuario tiene un rol específico
     */
    public static function checkRole($requiredRoles) {
        $usuario = self::check();
        
        if (!is_array($requiredRoles)) {
            $requiredRoles = [$requiredRoles];
        }
        
        if (!in_array($usuario['tipo'], $requiredRoles)) {
            errorResponse('No tienes permisos para realizar esta acción', 403);
        }
        
        return $usuario;
    }
    
    /**
     * Verificar si es administrador
     */
    public static function isAdmin() {
        return self::checkRole('admin');
    }
    
    /**
     * Verificar si es bibliotecario o admin
     */
    public static function isBibliotecario() {
        return self::checkRole(['admin', 'bibliotecario']);
    }
    
    /**
     * Obtener token desde headers
     */
    private static function getToken() {
        $headers = getallheaders();
        
        // Buscar en Authorization header (Bearer token)
        if (isset($headers['Authorization'])) {
            $matches = [];
            if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }
        
        // Buscar en X-Auth-Token header
        if (isset($headers['X-Auth-Token'])) {
            return $headers['X-Auth-Token'];
        }
        
        // Buscar en query string (no recomendado para producción)
        if (isset($_GET['token'])) {
            return $_GET['token'];
        }
        
        return null;
    }
    
    /**
     * Validar token y obtener usuario
     * NOTA: Implementación simple con sesiones
     * En producción usar JWT (JSON Web Tokens)
     */
    private static function validateToken($token) {
        // Implementación simple con base de datos
        // En producción, usar JWT con firma y expiración
        
        try {
            $db = Database::getInstance();
            
            // Buscar token en tabla de sesiones (deberías crear esta tabla)
            $query = "SELECT u.* 
                      FROM usuarios u
                      INNER JOIN sesiones s ON u.id = s.usuario_id
                      WHERE s.token = :token 
                      AND s.expira_en > NOW()
                      AND u.activo = TRUE";
            
            $result = $db->select($query, [':token' => $token]);
            
            if (!empty($result)) {
                return $result[0];
            }
            
            return null;
            
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Generar token JWT (implementación básica)
     * En producción usar biblioteca como firebase/php-jwt
     */
    public static function generateJWT($userId, $tipo) {
        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        
        $payload = base64_encode(json_encode([
            'user_id' => $userId,
            'tipo' => $tipo,
            'exp' => time() + JWT_EXPIRATION,
            'iat' => time()
        ]));
        
        $signature = hash_hmac('sha256', "$header.$payload", JWT_SECRET_KEY, true);
        $signature = base64_encode($signature);
        
        return "$header.$payload.$signature";
    }
    
    /**
     * Verificar token JWT (implementación básica)
     */
    public static function verifyJWT($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return false;
        }
        
        list($header, $payload, $signature) = $parts;
        
        // Verificar firma
        $validSignature = hash_hmac('sha256', "$header.$payload", JWT_SECRET_KEY, true);
        $validSignature = base64_encode($validSignature);
        
        if ($signature !== $validSignature) {
            return false;
        }
        
        // Decodificar payload
        $payloadData = json_decode(base64_decode($payload), true);
        
        // Verificar expiración
        if ($payloadData['exp'] < time()) {
            return false;
        }
        
        return $payloadData;
    }
    
    /**
     * Obtener usuario actual desde token
     */
    public static function getCurrentUser() {
        $token = self::getToken();
        
        if (!$token) {
            return null;
        }
        
        return self::validateToken($token);
    }
    
    /**
     * Crear sesión para usuario
     */
    public static function createSession($userId) {
        try {
            $token = generateToken(64);
            $expiraEn = date('Y-m-d H:i:s', time() + JWT_EXPIRATION);
            
            $db = Database::getInstance();
            
            // Eliminar sesiones antiguas del usuario
            $db->execute(
                "DELETE FROM sesiones WHERE usuario_id = :usuario_id OR expira_en < NOW()",
                [':usuario_id' => $userId]
            );
            
            // Crear nueva sesión
            $query = "INSERT INTO sesiones (usuario_id, token, expira_en) 
                      VALUES (:usuario_id, :token, :expira_en)";
            
            $db->execute($query, [
                ':usuario_id' => $userId,
                ':token' => $token,
                ':expira_en' => $expiraEn
            ]);
            
            return $token;
            
        } catch (Exception $e) {
            logActivity("Error al crear sesión: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
    
    /**
     * Cerrar sesión
     */
    public static function logout() {
        $token = self::getToken();
        
        if ($token) {
            try {
                $db = Database::getInstance();
                $db->execute("DELETE FROM sesiones WHERE token = :token", [':token' => $token]);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        
        return false;
    }
}