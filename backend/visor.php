<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../frontend/login.html");
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT archivo_pdf, titulo FROM libros WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$libro) {
    die(" Libro no encontrado");
}

$pdf = __DIR__ . '/' . $libro['archivo_pdf'];
if (!is_file($pdf)) {
    die(" PDF no encontrado");
}

header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"" . basename($pdf) . "\"");
readfile($pdf);
exit;
