<?php
session_start();
require_once 'conexion.php';

// ... tu lógica de actualización aquí ...

if ($stmt->execute()) {
    $_SESSION['mensaje'] = "✏️ Libro editado con éxito";
} else {
    $_SESSION['mensaje'] = "❌ Error al editar el libro";
}

header("Location: admin_dashboard.php");
exit;
?>
