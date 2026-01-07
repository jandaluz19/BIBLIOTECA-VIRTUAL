<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';

class LibrosController {
    public function index() {
        global $conn;

        $sql = "SELECT id, titulo, autor, archivo_pdf, portada_url 
                FROM libros 
                WHERE disponible = 1 
                ORDER BY titulo ASC";
        $result = $conn->query($sql);

        $libros = [];
        while ($row = $result->fetch_assoc()) {
            // Portada por defecto
            if (empty($row['portada_url'])) {
                $row['portada_url'] = 'https://via.placeholder.com/150x220?text=Sin+Portada';
            } else {
                $row['portada_url'] = 'backend/' . $row['portada_url'];
            }

            // PDF ruta completa
            if (!empty($row['archivo_pdf'])) {
                $row['archivo_pdf'] = 'backend/' . $row['archivo_pdf'];
            }

            $libros[] = $row;
        }

        jsonResponse($libros);
    }

    public function show($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM libros WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $libro = $stmt->get_result()->fetch_assoc();

        if (!$libro) {
            errorResponse("Libro no encontrado", 404);
        }

        jsonResponse($libro);
    }
}
