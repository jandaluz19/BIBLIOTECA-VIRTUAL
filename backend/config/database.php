<?php
/**
 * Configuración de Base de Datos
 * Biblioteca Virtual - Conexión PDO con MySQL
 */

class Database {
    // Configuración de conexión
    private const HOST = 'localhost';
    private const DB_NAME = 'biblioteca_virtual';
    private const USERNAME = 'root';
    private const PASSWORD = '';
    private const CHARSET = 'utf8mb4';
    
    private static $instance = null;
    private $connection = null;
    
    /**
     * Constructor privado para patrón Singleton
     */
    private function __construct() {
        try {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                self::HOST,
                self::DB_NAME,
                self::CHARSET
            );
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . self::CHARSET
            ];
            
            $this->connection = new PDO($dsn, self::USERNAME, self::PASSWORD, $options);
            
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }
    
    /**
     * Obtener instancia única de la conexión (Singleton)
     * @return Database
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obtener conexión PDO
     * @return PDO
     */
    public function getConnection(): PDO {
        return $this->connection;
    }
    
    /**
     * Ejecutar consulta SELECT
     * @param string $query Consulta SQL
     * @param array $params Parámetros preparados
     * @return array Resultados
     */
    public function select(string $query, array $params = []): array {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->handleError($e);
            return [];
        }
    }
    
    /**
     * Ejecutar consulta INSERT/UPDATE/DELETE
     * @param string $query Consulta SQL
     * @param array $params Parámetros preparados
     * @return bool|int ID insertado o número de filas afectadas
     */
    public function execute(string $query, array $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $success = $stmt->execute($params);
            
            // Si es INSERT, retornar último ID insertado
            if (stripos($query, 'INSERT') === 0) {
                return $this->connection->lastInsertId();
            }
            
            // Para UPDATE/DELETE, retornar número de filas afectadas
            return $stmt->rowCount();
            
        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }
    
    /**
     * Iniciar transacción
     */
    public function beginTransaction(): bool {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Confirmar transacción
     */
    public function commit(): bool {
        return $this->connection->commit();
    }
    
    /**
     * Revertir transacción
     */
    public function rollBack(): bool {
        return $this->connection->rollBack();
    }
    
    /**
     * Verificar si hay transacción activa
     */
    public function inTransaction(): bool {
        return $this->connection->inTransaction();
    }
    
    /**
     * Manejar errores de base de datos
     * @param PDOException $e
     */
    private function handleError(PDOException $e): void {
        error_log("Database Error: " . $e->getMessage());
        
        // En producción, mostrar mensaje genérico
        if (getenv('ENVIRONMENT') === 'production') {
            throw new Exception("Error de base de datos. Contacte al administrador.");
        } else {
            // En desarrollo, mostrar error completo
            throw new Exception("Database Error: " . $e->getMessage());
        }
    }
    
    /**
     * Prevenir clonación
     */
    private function __clone() {}
    
    /**
     * Prevenir deserialización
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
    
    /**
     * Cerrar conexión al destruir
     */
    public function __destruct() {
        $this->connection = null;
    }
}

// Función helper para obtener conexión rápidamente
function getDB(): Database {
    return Database::getInstance();
}