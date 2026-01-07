<?php
session_start();
require_once 'conexion.php';

// Validar que sea admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    $_SESSION['mensaje'] = "❌ Acceso denegado";
    header("Location: admin_dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo           = trim($_POST['titulo']);
    $autor            = trim($_POST['autor']);
    $categoria_id     = (int)$_POST['categoria_id'];
    $anio_publicacion = (int)$_POST['anio_publicacion'];
    $descripcion      = trim($_POST['descripcion']);
    $url_pdf          = trim($_POST['archivo_pdf']);

    // Manejo de archivos
    $ruta_pdf   = null;
    $ruta_img   = null;
    $carpeta    = __DIR__ . "/uploads";

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0775, true);
    }

    // PDF subido
    if (!empty($_FILES['archivo_pdf_file']['name'])) {
        $nombrePdf = uniqid("pdf_") . ".pdf";
        $destinoPdf = $carpeta . "/" . $nombrePdf;
        if (move_uploaded_file($_FILES['archivo_pdf_file']['tmp_name'], $destinoPdf)) {
            $ruta_pdf = "uploads/" . $nombrePdf;
        }
    }

    // Portada subida
    if (!empty($_FILES['portada']['name'])) {
        $ext = strtolower(pathinfo($_FILES['portada']['name'], PATHINFO_EXTENSION));
        $nombreImg = uniqid("img_") . "." . $ext;
        $destinoImg = $carpeta . "/" . $nombreImg;
        if (move_uploaded_file($_FILES['portada']['tmp_name'], $destinoImg)) {
            $ruta_img = "uploads/" . $nombreImg;
        }
    }

    // Decidir fuente PDF: URL o archivo subido
    $archivo_pdf = $ruta_pdf ? $ruta_pdf : ($url_pdf !== '' ? $url_pdf : null);

    // Insertar en la tabla libros
    $sql = "INSERT INTO libros (titulo, autor, categoria_id, anio_publicacion, descripcion, archivo_pdf, portada)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param(
            "ssissss",
            $titulo,
            $autor,
            $categoria_id,
            $anio_publicacion,
            $descripcion,
            $archivo_pdf,
            $ruta_img
        );

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "📚 Libro agregado con éxito";
        } else {
            $_SESSION['mensaje'] = "❌ Error al agregar el libro: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['mensaje'] = "❌ Error al preparar consulta: " . $conn->error;
    }

    $conn->close();
    header("Location: admin_dashboard.php");
    exit;
} else {
    $_SESSION['mensaje'] = "❌ Método no permitido";
    header("Location: admin_dashboard.php");
    exit;
}
?>
