<?php
session_start();
require_once 'conexion.php';

// ... tu lógica de eliminación aquí ...

if ($stmt->execute()) {
    $_SESSION['mensaje'] = "🗑️ Libro eliminado con éxito";
} else {
    $_SESSION['mensaje'] = "❌ Error al eliminar el libro";
}

header("Location: admin_dashboard.php");
exit;
?>
