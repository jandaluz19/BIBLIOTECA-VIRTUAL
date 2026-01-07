<?php
require_once '../conexion.php';
header('Content-Type: application/json');

// Consulta de libros disponibles
$result = $conn->query("SELECT id, titulo, autor, archivo_pdf, portada_url 
                        FROM libros 
                        WHERE disponible = 1 
                        ORDER BY titulo ASC");

$libros = [];
while ($row = $result->fetch_assoc()) {
    // Si no hay portada, usar placeholder
    if (empty($row['portada_url'])) {
        $row['portada_url'] = 'https://via.placeholder.com/150x220?text=Sin+Portada';
    }

    // Ajustar rutas para que sean accesibles desde frontend
    if (!empty($row['archivo_pdf'])) {
        $row['archivo_pdf'] = 'backend/' . $row['archivo_pdf'];
    }
    if (!empty($row['portada_url']) && strpos($row['portada_url'], 'uploads/') === 0) {
        $row['portada_url'] = 'backend/' . $row['portada_url'];
    }

    $libros[] = $row;
}

// Devolver JSON
echo json_encode($libros, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
