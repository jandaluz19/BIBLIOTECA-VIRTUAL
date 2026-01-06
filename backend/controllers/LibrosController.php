<?php
/**
 * Controlador de Libros
 * Maneja todas las operaciones CRUD y búsquedas de libros
 */

class LibrosController {
    private $model;
    
    public function __construct() {
        $this->model = new Libro();
    }

    /* =========================
       PÚBLICO (USUARIOS)
    ========================== */

    public function index() {
        try {
            $limit = $_GET['limit'] ?? ITEMS_PER_PAGE;
            $offset = $_GET['offset'] ?? 0;

            $libros = $this->model->getAll($limit, $offset);
            $total = $this->model->countAll();

            jsonResponse(compact('libros','total','limit','offset'), 200);
        } catch (Exception $e) {
            errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id) {
        $libro = $this->model->getById($id);
        if (!$libro) errorResponse('Libro no encontrado', 404);
        jsonResponse($libro);
    }

    public function buscar() {
        $params = $_GET;
        jsonResponse($this->model->search($params));
    }

    public function masPrestados() {
        jsonResponse($this->model->getMasPrestados(10));
    }

    public function mejorCalificados() {
        jsonResponse($this->model->getMejorCalificados(10));
    }

    public function recientes() {
        jsonResponse($this->model->getRecientes(10));
    }

    public function disponibilidad($id) {
        jsonResponse([
            'libro_id' => $id,
            'disponible' => $this->model->checkDisponibilidad($id)
        ]);
    }

    /* =========================
       ADMINISTRADOR
    ========================== */

    /**
     * POST: Crear libro (ADMIN)
     */
    public function create() {
        validateMethod('POST');

        $user = getAuthenticatedUser();
        requireAdmin($user);

        $data = array_map('sanitize', getJsonInput());

        $errors = $this->model->validate($data);
        if ($errors) errorResponse('Datos inválidos', 400, $errors);

        $id = $this->model->create($data);
        logActivity("ADMIN {$user['email']} creó libro ID $id");

        jsonResponse($this->model->getById($id), 201);
    }

    /**
     * PUT: Actualizar libro (ADMIN)
     */
    public function update($id) {
        validateMethod('PUT');

        $user = getAuthenticatedUser();
        requireAdmin($user);

        if (!$this->model->getById($id)) {
            errorResponse('Libro no encontrado', 404);
        }

        $data = array_map('sanitize', getJsonInput());
        $this->model->update($id, $data);

        logActivity("ADMIN {$user['email']} actualizó libro ID $id");
        jsonResponse($this->model->getById($id));
    }

    /**
     * DELETE: Eliminar libro (ADMIN)
     */
    public function delete($id) {
        validateMethod('DELETE');

        $user = getAuthenticatedUser();
        requireAdmin($user);

        if (!$this->model->getById($id)) {
            errorResponse('Libro no encontrado', 404);
        }

        $this->model->delete($id);
        logActivity("ADMIN {$user['email']} eliminó libro ID $id");

        jsonResponse(null, 200, 'Libro eliminado');
    }

    /**
     * GET: Estadísticas (ADMIN)
     */
    public function estadisticas() {
        $user = getAuthenticatedUser();
        requireAdmin($user);

        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->query("CALL obtener_estadisticas()");
        jsonResponse($stmt->fetch());
    }
}
