<?php
/**
 * Modelo Categoria
 * Gestión de categorías de libros
 */

class Categoria {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtener todas las categorías
     */
    public function getAll($activeOnly = true) {
        $query = "SELECT * FROM categorias";
        
        if ($activeOnly) {
            $query .= " WHERE activo = TRUE";
        }
        
        $query .= " ORDER BY nombre ASC";
        
        return $this->db->select($query);
    }
    
    /**
     * Obtener categoría por ID
     */
    public function getById($id) {
        $query = "SELECT * FROM categorias WHERE id = :id";
        $result = $this->db->select($query, [':id' => $id]);
        return $result[0] ?? null;
    }
    
    /**
     * Obtener categoría por nombre
     */
    public function getByNombre($nombre) {
        $query = "SELECT * FROM categorias WHERE nombre = :nombre";
        $result = $this->db->select($query, [':nombre' => $nombre]);
        return $result[0] ?? null;
    }
    
    /**
     * Obtener estadísticas por categoría
     */
    public function getEstadisticas() {
        return $this->db->select("SELECT * FROM vista_estadisticas_categorias");
    }
    
    /**
     * Obtener estadística de una categoría específica
     */
    public function getEstadisticaById($id) {
        $query = "SELECT * FROM vista_estadisticas_categorias WHERE id = :id";
        $result = $this->db->select($query, [':id' => $id]);
        return $result[0] ?? null;
    }
    
    /**
     * Crear nueva categoría
     */
    public function create($data) {
        $query = "INSERT INTO categorias (nombre, descripcion, icono, color, activo) 
                  VALUES (:nombre, :descripcion, :icono, :color, :activo)";
        
        $params = [
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null,
            ':icono' => $data['icono'] ?? '📚',
            ':color' => $data['color'] ?? '#2563eb',
            ':activo' => $data['activo'] ?? true
        ];
        
        return $this->db->execute($query, $params);
    }
    
    /**
     * Actualizar categoría
     */
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        $allowedFields = ['nombre', 'descripcion', 'icono', 'color', 'activo'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $query = "UPDATE categorias SET " . implode(', ', $fields) . " WHERE id = :id";
        return $this->db->execute($query, $params);
    }
    
    /**
     * Eliminar categoría (soft delete)
     */
    public function delete($id) {
        $query = "UPDATE categorias SET activo = FALSE WHERE id = :id";
        return $this->db->execute($query, [':id' => $id]);
    }
    
    /**
     * Eliminar categoría permanentemente
     */
    public function hardDelete($id) {
        // Verificar que no tenga libros asociados
        $checkQuery = "SELECT COUNT(*) as count FROM libros WHERE categoria_id = :id";
        $result = $this->db->select($checkQuery, [':id' => $id]);
        
        if ($result[0]['count'] > 0) {
            throw new Exception("No se puede eliminar: la categoría tiene libros asociados");
        }
        
        $query = "DELETE FROM categorias WHERE id = :id";
        return $this->db->execute($query, [':id' => $id]);
    }
    
    /**
     * Obtener categorías más populares (con más libros)
     */
    public function getMasPopulares($limit = 5) {
        $query = "SELECT c.*, COUNT(l.id) as total_libros
                  FROM categorias c
                  LEFT JOIN libros l ON c.id = l.categoria_id
                  WHERE c.activo = TRUE
                  GROUP BY c.id
                  ORDER BY total_libros DESC
                  LIMIT :limit";
        
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Contar libros por categoría
     */
    public function countLibros($id) {
        $query = "SELECT COUNT(*) as total FROM libros WHERE categoria_id = :id";
        $result = $this->db->select($query, [':id' => $id]);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Validar datos de categoría
     */
    public function validate($data, $isUpdate = false) {
        $errors = [];
        
        if (!$isUpdate || isset($data['nombre'])) {
            if (empty($data['nombre'])) {
                $errors['nombre'] = 'El nombre es requerido';
            } elseif (strlen($data['nombre']) > 100) {
                $errors['nombre'] = 'El nombre no puede exceder 100 caracteres';
            } else {
                // Verificar que el nombre no exista
                $existing = $this->getByNombre($data['nombre']);
                if ($existing && (!$isUpdate || $existing['id'] != $data['id'])) {
                    $errors['nombre'] = 'Ya existe una categoría con ese nombre';
                }
            }
        }
        
        if (isset($data['color']) && !empty($data['color'])) {
            if (!preg_match('/^#[a-fA-F0-9]{6}$/', $data['color'])) {
                $errors['color'] = 'Formato de color inválido (debe ser hexadecimal #RRGGBB)';
            }
        }
        
        return $errors;
    }
}