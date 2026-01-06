<?php
/**
 * Modelo Libro
 * Gestión de libros en la biblioteca
 */

class Libro {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtener todos los libros con información completa
     */
    public function getAll($limit = null, $offset = 0) {
        $query = "SELECT * FROM vista_libros_completa ORDER BY titulo";
        
        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        }
        
        return $this->db->select($query);
    }
    
    /**
     * Obtener libro por ID
     */
    public function getById($id) {
        $query = "SELECT * FROM vista_libros_completa WHERE id = :id";
        $result = $this->db->select($query, [':id' => $id]);
        return $result[0] ?? null;
    }
    
    /**
     * Buscar libros por múltiples criterios
     */
    public function search($params) {
        $conditions = [];
        $bindings = [];
        
        // Búsqueda por texto
        if (!empty($params['q'])) {
            $conditions[] = "(titulo LIKE :q OR autor LIKE :q OR descripcion LIKE :q)";
            $bindings[':q'] = '%' . $params['q'] . '%';
        }
        
        // Filtro por categoría
        if (!empty($params['categoria_id'])) {
            $conditions[] = "categoria_id = :categoria_id";
            $bindings[':categoria_id'] = $params['categoria_id'];
        }
        
        // Filtro por disponibilidad
        if (isset($params['disponible'])) {
            $conditions[] = "disponible = :disponible";
            $bindings[':disponible'] = $params['disponible'];
        }
        
        // Filtro por año
        if (!empty($params['anio'])) {
            $conditions[] = "anio_publicacion = :anio";
            $bindings[':anio'] = $params['anio'];
        }
        
        // Filtro por autor
        if (!empty($params['autor'])) {
            $conditions[] = "autor LIKE :autor";
            $bindings[':autor'] = '%' . $params['autor'] . '%';
        }
        
        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        
        // Ordenamiento
        $orderBy = 'titulo ASC';
        if (!empty($params['orden'])) {
            switch ($params['orden']) {
                case 'titulo':
                    $orderBy = 'titulo ASC';
                    break;
                case 'autor':
                    $orderBy = 'autor ASC';
                    break;
                case 'anio':
                    $orderBy = 'anio_publicacion DESC';
                    break;
                case 'calificacion':
                    $orderBy = 'calificacion DESC';
                    break;
                case 'mas_prestados':
                    $orderBy = 'veces_prestado DESC';
                    break;
            }
        }
        
        $query = "SELECT * FROM vista_libros_completa $whereClause ORDER BY $orderBy";
        
        // Paginación
        if (isset($params['limit'])) {
            $limit = min((int)$params['limit'], MAX_ITEMS_PER_PAGE);
            $offset = (int)($params['offset'] ?? 0);
            $query .= " LIMIT $limit OFFSET $offset";
        }
        
        return $this->db->select($query, $bindings);
    }
    
    /**
     * Contar resultados de búsqueda
     */
    public function countSearch($params) {
        $conditions = [];
        $bindings = [];
        
        if (!empty($params['q'])) {
            $conditions[] = "(titulo LIKE :q OR autor LIKE :q OR descripcion LIKE :q)";
            $bindings[':q'] = '%' . $params['q'] . '%';
        }
        
        if (!empty($params['categoria_id'])) {
            $conditions[] = "categoria_id = :categoria_id";
            $bindings[':categoria_id'] = $params['categoria_id'];
        }
        
        if (isset($params['disponible'])) {
            $conditions[] = "disponible = :disponible";
            $bindings[':disponible'] = $params['disponible'];
        }
        
        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $query = "SELECT COUNT(*) as total FROM libros $whereClause";
        
        $result = $this->db->select($query, $bindings);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Crear nuevo libro
     */
    public function create($data) {
        $query = "INSERT INTO libros (
            titulo, autor, categoria_id, anio_publicacion, 
            isbn, editorial, descripcion, paginas, idioma, 
            portada_url, archivo_pdf, disponible, stock
        ) VALUES (
            :titulo, :autor, :categoria_id, :anio_publicacion,
            :isbn, :editorial, :descripcion, :paginas, :idioma,
            :portada_url, :archivo_pdf, :disponible, :stock
        )";
        
        $params = [
            ':titulo' => $data['titulo'],
            ':autor' => $data['autor'],
            ':categoria_id' => $data['categoria_id'],
            ':anio_publicacion' => $data['anio_publicacion'],
            ':isbn' => $data['isbn'] ?? null,
            ':editorial' => $data['editorial'] ?? null,
            ':descripcion' => $data['descripcion'] ?? null,
            ':paginas' => $data['paginas'] ?? null,
            ':idioma' => $data['idioma'] ?? 'Español',
            ':portada_url' => $data['portada_url'] ?? null,
            ':archivo_pdf' => $data['archivo_pdf'] ?? null,
            ':disponible' => $data['disponible'] ?? true,
            ':stock' => $data['stock'] ?? 1
        ];
        
        return $this->db->execute($query, $params);
    }
    
    /**
     * Actualizar libro
     */
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        $allowedFields = [
            'titulo', 'autor', 'categoria_id', 'anio_publicacion',
            'isbn', 'editorial', 'descripcion', 'paginas', 'idioma',
            'portada_url', 'archivo_pdf', 'disponible', 'stock'
        ];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $query = "UPDATE libros SET " . implode(', ', $fields) . " WHERE id = :id";
        return $this->db->execute($query, $params);
    }
    
    /**
     * Eliminar libro (soft delete - marcar como no disponible)
     */
    public function delete($id) {
        $query = "UPDATE libros SET disponible = FALSE WHERE id = :id";
        return $this->db->execute($query, [':id' => $id]);
    }
    
    /**
     * Eliminar libro permanentemente
     */
    public function hardDelete($id) {
        // Verificar que no tenga préstamos activos
        $checkQuery = "SELECT COUNT(*) as count FROM prestamos 
                       WHERE libro_id = :id AND estado = 'activo'";
        $result = $this->db->select($checkQuery, [':id' => $id]);
        
        if ($result[0]['count'] > 0) {
            throw new Exception("No se puede eliminar: el libro tiene préstamos activos");
        }
        
        $query = "DELETE FROM libros WHERE id = :id";
        return $this->db->execute($query, [':id' => $id]);
    }
    
    /**
     * Obtener libros más prestados
     */
    public function getMasPrestados($limit = 10) {
        $query = "SELECT * FROM vista_libros_completa 
                  ORDER BY veces_prestado DESC 
                  LIMIT :limit";
        
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener libros mejor calificados
     */
    public function getMejorCalificados($limit = 10) {
        $query = "SELECT * FROM vista_libros_completa 
                  WHERE calificacion > 0
                  ORDER BY calificacion DESC 
                  LIMIT :limit";
        
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener libros recientes
     */
    public function getRecientes($limit = 10) {
        $query = "SELECT * FROM vista_libros_completa 
                  ORDER BY created_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Verificar disponibilidad
     */
    public function checkDisponibilidad($id) {
        $query = "SELECT disponible, stock FROM libros WHERE id = :id";
        $result = $this->db->select($query, [':id' => $id]);
        
        if (empty($result)) {
            return false;
        }
        
        return $result[0]['disponible'] && $result[0]['stock'] > 0;
    }
    
    /**
     * Validar datos del libro
     */
    public function validate($data, $isUpdate = false) {
        $errors = [];
        
        if (!$isUpdate || isset($data['titulo'])) {
            if (empty($data['titulo'])) {
                $errors['titulo'] = 'El título es requerido';
            } elseif (strlen($data['titulo']) > 255) {
                $errors['titulo'] = 'El título no puede exceder 255 caracteres';
            }
        }
        
        if (!$isUpdate || isset($data['autor'])) {
            if (empty($data['autor'])) {
                $errors['autor'] = 'El autor es requerido';
            }
        }
        
        if (!$isUpdate || isset($data['categoria_id'])) {
            if (empty($data['categoria_id'])) {
                $errors['categoria_id'] = 'La categoría es requerida';
            }
        }
        
        if (!$isUpdate || isset($data['anio_publicacion'])) {
            if (empty($data['anio_publicacion'])) {
                $errors['anio_publicacion'] = 'El año de publicación es requerido';
            } elseif (!is_numeric($data['anio_publicacion']) || 
                      $data['anio_publicacion'] < 1900 || 
                      $data['anio_publicacion'] > date('Y')) {
                $errors['anio_publicacion'] = 'Año de publicación inválido';
            }
        }
        
        if (isset($data['isbn']) && !empty($data['isbn'])) {
            // Validar formato ISBN básico
            $isbn = preg_replace('/[^0-9X]/', '', $data['isbn']);
            if (strlen($isbn) !== 10 && strlen($isbn) !== 13) {
                $errors['isbn'] = 'Formato de ISBN inválido';
            }
        }
        
        return $errors;
    }
}