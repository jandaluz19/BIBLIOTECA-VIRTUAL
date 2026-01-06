<?php
/**
 * Controlador de Categorías
 * Maneja operaciones CRUD de categorías
 */

class CategoriasController {
    private $model;
    
    public function __construct() {
        $this->model = new Categoria();
    }
    
    /**
     * GET: Obtener todas las categorías
     * GET: /api/categorias
     */
    public function index() {
        try {
            $activeOnly = isset($_GET['activas']) ? (bool)$_GET['activas'] : true;
            $categorias = $this->model->getAll($activeOnly);
            
            jsonResponse($categorias, 200, 'Categorías obtenidas exitosamente');
            
        } catch (Exception $e) {
            errorResponse($e->getMessage(), 500);
        }
    }
    
    /**
     * GET: Obtener categoría por ID
     * GET: /api/categorias/{id}
     */
    public function show($id) {
        try {
            $categoria = $this->model->getById($id);
            
            if (!$categoria) {
                errorResponse('Categoría no encontrada', 404);
            }
            
            jsonResponse($categoria, 200, 'Categoría obtenida exitosamente');
            
        } catch (Exception $e) {
            errorResponse($e->getMessage(), 500);
        }
    }
    
    /**
     * POST: Crear nueva categoría
     * POST: /api/categorias
     */
    public function create() {
        try {
            validateMethod('POST');
            
            $data = getJsonInput();
            $data = array_map('sanitize', $data);
            
            // Validar datos
            $errors = $this->model->validate($data);
            if (!empty($errors)) {
                errorResponse('Datos inválidos', 400, $errors);
            }
            
            $categoriaId = $this->model->create($data);
            
            if ($categoriaId) {
                $categoria = $this->model->getById($categoriaId);
                logActivity("Categoría creada: {$data['nombre']} (ID: $categoriaId)");
                jsonResponse($categoria, 201, 'Categoría creada exitosamente');
            } else {
                errorResponse('Error al crear la categoría', 500);
            }
            
        } catch (Exception $e) {
            errorResponse($e->getMessage(), 500);
        }
    }
    
    /**
     * PUT: Actualizar categoría
     * PUT: /api/categorias/{id}
     */
    public function update($id) {
        try {
            validateMethod('PUT');
            
            $categoria = $this->model->getById($id);
            if (!$categoria) {
                errorResponse('Categoría no encontrada', 404);
            }
            
            $data = getJsonInput();
            $data = array_map('sanitize', $data);
            $data['id'] = $id; // Para validación de nombre único
            
            // Validar datos
            $errors = $this->model->validate($data, true);
            if (!empty($errors)) {
                errorResponse('Datos inválidos', 400, $errors);
            }
            
            $success = $this->model->update($id, $data);
            
            if ($success) {
                $categoriaActualizada = $this->model->getById($id);
                logActivity("Categoría actualizada: {$categoriaActualizada['nombre']} (ID: $id)");
                jsonResponse($categoriaActualizada, 200, 'Categoría actualizada exitosamente');
            } else {
                errorResponse('No se realizaron cambios', 400);
            }
            
        } catch (Exception $e) {
            errorResponse($e->getMessage(), 500);
        }
    }
    
    /**
     * DELETE: Eliminar categoría
     * DELETE: /api/categorias/{id}
     */
    public function delete($id) {
        try {
            validateMethod('DELETE');
            
            $categoria = $this->model->getById($id);
            if (!$categoria) {
                errorResponse('Categoría no encontrada', 404);
            }
            
            // Verificar si tiene libros asociados
            $totalLibros = $this->model->countLibros($id);
            if ($totalLibros > 0) {
                errorResponse(
                    "No se puede eliminar: la categoría tiene $totalLibros libro(s) asociado(s)", 
                    400
                );
            }
            
            $success = $this->model->delete($id);
            
            if ($success) {
                logActivity("Categoría eliminada: {$categoria['nombre']} (ID: $id)");
                jsonResponse(null, 200, 'Categoría eliminada exitosamente');
            } else {
                errorResponse('Error al eliminar la categoría', 500);
            }
            
        } catch (Exception $e) {
            errorResponse($e->getMessage(), 500);
        }
    }
    
    /**
     * GET: Obtener estadísticas de todas las categorías
     * GET: /api/categorias/estadisticas
     */
    public function estadisticas() {
        try {
            $estadisticas = $this->model->getEstadisticas();
            jsonResponse($estadisticas, 200, 'Estadísticas obtenidas');
            
        } catch (Exception $e) {
            errorResponse($e->getMessage(), 500);
        }
    }
    
    /**
     * GET: Obtener estadística de una categoría
     * GET: /api/categorias/{id}/estadisticas
     */
    public function estadisticasById($id) {
        try {
            $estadistica = $this->model->getEstadisticaById($id);
            
            if (!$estadistica) {
                errorResponse('Categoría no encontrada', 404);
            }
            
            jsonResponse($estadistica, 200, 'Estadística obtenida');
            
        } catch (Exception $e) {
            errorResponse($e->getMessage(), 500);
        }
    }
    
    /**
     * GET: Categorías más populares
     * GET: /api/categorias/populares
     */
    public function populares() {
        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
            $categorias = $this->model->getMasPopulares($limit);
            
            jsonResponse($categorias, 200, 'Categorías populares obtenidas');
            
        } catch (Exception $e) {
            errorResponse($e->getMessage(), 500);
        }
    }
}