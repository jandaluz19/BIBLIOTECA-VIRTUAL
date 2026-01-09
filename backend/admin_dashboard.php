<?php
session_start();
require_once 'conexion.php';


if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: ../frontend/login.html");
    exit;
}

// aseguramos la carpeta de destino
function asegurarDirectorios() {
    $bases = [
        __DIR__ . '/uploads',
        __DIR__ . '/uploads/pdfs',
        __DIR__ . '/uploads/covers',
    ];
    foreach ($bases as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
    }
}
asegurarDirectorios();

function guardarPortada($file, $portadaActual = null) {
    if (empty($file['name'])) {
        return $portadaActual ?: 'uploads/covers/default.jpg';
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ext, $permitidas)) {
        return $portadaActual ?: 'uploads/covers/default.jpg';
    }

    // Eliminar portada
    if ($portadaActual && $portadaActual !== 'uploads/covers/default.jpg') {
        $rutaVieja = __DIR__ . '/' . $portadaActual;
        if (is_file($rutaVieja)) {
            @unlink($rutaVieja);
        }
    }

    $nombre = 'cover_' . uniqid() . '.' . $ext;
    $destino = __DIR__ . '/uploads/covers/' . $nombre;

    if (move_uploaded_file($file['tmp_name'], $destino)) {
        return 'uploads/covers/' . $nombre;
    }

    return $portadaActual ?: 'uploads/covers/default.jpg';
}

//se gauarda lirbos subidos
function guardarPdf($file, $pdfActual = null) {
    if (empty($file['name'])) {
        return $pdfActual; 
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        return $pdfActual;
    }
    $nombre = 'pdf_' . uniqid() . '.' . $ext;
    $destino = __DIR__ . '/uploads/pdfs/' . $nombre;

    if (move_uploaded_file($file['tmp_name'], $destino)) {
        
        if ($pdfActual && str_starts_with($pdfActual, 'uploads/pdfs/')) {
            $rutaVieja = __DIR__ . '/' . $pdfActual;
            if (is_file($rutaVieja)) {
                @unlink($rutaVieja);
            }
        }
        return 'uploads/pdfs/' . $nombre;
    }
    return $pdfActual;
}

$mensaje = "";
$editando = false;
$libroEditar = null;

if (isset($_GET['edit'])) {
    $editando = true;
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM libros WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($row = $resultado->fetch_assoc()) {
        $libroEditar = $row;
    }
    $stmt->close();
}

// se agrega libross
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $categoria_id = (int)($_POST['categoria_id'] ?? 1);
    $anio_publicacion = (int)($_POST['anio_publicacion'] ?? date('Y'));
    $descripcion = trim($_POST['descripcion'] ?? '');

    // Subidas
    $archivo_pdf = guardarPdf($_FILES['archivo_pdf'] ?? []);
    $portada_url = guardarPortada($_FILES['portada'] ?? []);

    if (empty($titulo) || empty($autor) || empty($archivo_pdf)) {
        $mensaje = "❌ Todos los campos obligatorios deben llenarse (título, autor y PDF).";
    } else {
        $sql = "INSERT INTO libros (titulo, autor, categoria_id, anio_publicacion, descripcion, archivo_pdf, portada_url, disponible)
                VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissss", $titulo, $autor, $categoria_id, $anio_publicacion, $descripcion, $archivo_pdf, $portada_url);

        if ($stmt->execute()) {
            $mensaje = "📚 Libro agregado con éxito";
        } else {
            $mensaje = "❌ Error al guardar: " . $stmt->error;
        }
        $stmt->close();
    }
}

// EDITAR LIBRO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id = (int)($_POST['id'] ?? 0);
  
    $stmt = $conn->prepare("SELECT archivo_pdf, portada_url FROM libros WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $actual = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $categoria_id = (int)($_POST['categoria_id'] ?? 1);
    $anio_publicacion = (int)($_POST['anio_publicacion'] ?? date('Y'));
    $descripcion = trim($_POST['descripcion'] ?? '');

    $archivo_pdf = guardarPdf($_FILES['archivo_pdf'] ?? [], $actual['archivo_pdf'] ?? null);
    $portada_url = guardarPortada($_FILES['portada'] ?? [], $actual['portada_url'] ?? null);

    if ($id <= 0 || empty($titulo) || empty($autor)) {
        $mensaje = "❌ Datos incompletos.";
    } else {
        $sql = "UPDATE libros SET titulo = ?, autor = ?, categoria_id = ?, anio_publicacion = ?, descripcion = ?, archivo_pdf = ?, portada_url = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissssi", $titulo, $autor, $categoria_id, $anio_publicacion, $descripcion, $archivo_pdf, $portada_url, $id);

        if ($stmt->execute()) {
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $mensaje = "❌ Error al actualizar: " . $stmt->error;
        }
        $stmt->close();
    }
}

// ELIMINAR LIBRO
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt = $conn->prepare("SELECT archivo_pdf, portada_url FROM libros WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $files = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($files) {
        if (!empty($files['archivo_pdf']) && str_starts_with($files['archivo_pdf'], 'uploads/pdfs/')) {
            $rutaPdf = __DIR__ . '/' . $files['archivo_pdf'];
            if (is_file($rutaPdf)) @unlink($rutaPdf);
        }
        if (!empty($files['portada_url']) && str_starts_with($files['portada_url'], 'uploads/covers/')) {
            $rutaImg = __DIR__ . '/' . $files['portada_url'];
            if (is_file($rutaImg)) @unlink($rutaImg);
        }
    }

    $sql = "DELETE FROM libros WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_dashboard.php");
    exit;
}

// LISTAR LIBROS
$libros = $conn->query("SELECT * FROM libros ORDER BY titulo ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin | Biblioteca Virtual</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f4f4f9; margin: 0; }
    header { background: #2a5298; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
    header a { color: white; text-decoration: none; font-weight: bold; }
    .contenedor { padding: 1rem; }
    .notificacion { background: #4caf50; color: white; padding: 12px; margin: 10px 0; border-radius: 6px; text-align: center; }
    .notificacion.error { background: #f44336; }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background: #2a5298; color: white; }
    form { margin-top: 1.5rem; background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    input, textarea, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
    button { background: #2a5298; color: white; border: none; padding: 12px; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; }
    button:hover { background: #1e3a8a; }
    img { max-height: 80px; margin-top: 5px; border: 1px solid #eee; border-radius: 4px; }
    .acciones a { margin-right: 8px; }
  </style>
</head>
<body>
<header>
  <h1>📊 Dashboard Admin</h1>
  <a href="logout.php">🚪 Cerrar sesión</a>
</header>

<div class="contenedor">
  <?php if (!empty($mensaje)): ?>
    <div class="notificacion <?= strpos($mensaje, '❌') !== false ? 'error' : '' ?>">
      <?= htmlspecialchars($mensaje) ?>
    </div>
  <?php endif; ?>

  <h2>📚 Todos los libros</h2>
  <table>
    <thead>
      <tr>
        <th>Portada</th>
        <th>Título</th>
        <th>Autor</th>
        <th>Categoría</th>
        <th>Año</th>
        <th class="acciones">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($libros && $libros->num_rows > 0): ?>
        <?php while ($libro = $libros->fetch_assoc()): ?>
          <tr>
            <td>
              <?php if (!empty($libro['portada_url'])): ?>
                <img src="<?= htmlspecialchars($libro['portada_url']) ?>" alt="Portada">
              <?php else: ?>
                <span>Sin portada</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($libro['titulo']) ?></td>
            <td><?= htmlspecialchars($libro['autor']) ?></td>
            <td><?= ($libro['categoria_id'] == 1 ? 'Estadística' : 'Informática') ?></td>
            <td><?= (int)$libro['anio_publicacion'] ?></td>
            <td class="acciones">
              <a href="admin_dashboard.php?edit=<?= (int)$libro['id'] ?>" style="color:#2a5298;">Editar</a>
              <a href="admin_dashboard.php?delete=<?= (int)$libro['id'] ?>" onclick="return confirm('¿Eliminar este libro?')" style="color:#d32f2f;">Eliminar</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6">No hay libros registrados.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <h2><?= $editando ? '✏️ Editar libro' : '➕ Agregar nuevo libro' ?></h2>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="accion" value="<?= $editando ? 'editar' : 'agregar' ?>">
    <?php if ($editando && $libroEditar): ?>
      <input type="hidden" name="id" value="<?= (int)$libroEditar['id'] ?>">
    <?php endif; ?>

    <label>Título</label>
    <input type="text" name="titulo" placeholder="Título del libro" required
           value="<?= $editando ? htmlspecialchars($libroEditar['titulo']) : '' ?>">

    <label>Autor</label>
    <input type="text" name="autor" placeholder="Autor" required
           value="<?= $editando ? htmlspecialchars($libroEditar['autor']) : '' ?>">

    <label>Categoría</label>
    <select name="categoria_id" required>
      <option value="1" <?= ($editando && (int)$libroEditar['categoria_id'] === 1) ? 'selected' : '' ?>>Estadística</option>
      <option value="2" <?= ($editando && (int)$libroEditar['categoria_id'] === 2) ? 'selected' : '' ?>>Informática</option>
    </select>

    <label>Año de publicación</label>
    <input type="number" name="anio_publicacion" placeholder="Año" min="1000" max="<?= date('Y') ?>" required
           value="<?= $editando ? (int)$libroEditar['anio_publicacion'] : '' ?>">

    <label>Descripción</label>
    <textarea name="descripcion" placeholder="Descripción" rows="3"><?= $editando ? htmlspecialchars($libroEditar['descripcion']) : '' ?></textarea>

    <label>Archivo PDF</label>
    <?php if ($editando && !empty($libroEditar['archivo_pdf'])): ?>
      <p style="font-size:0.9rem;color:#444;">Actual: <a href="<?= htmlspecialchars($libroEditar['archivo_pdf']) ?>" target="_blank">Ver PDF</a></p>
    <?php endif; ?>
    <input type="file" name="archivo_pdf" accept="application/pdf" <?= $editando ? '' : 'required' ?>>

    <label>Portada (imagen opcional)</label>
    <?php if ($editando && !empty($libroEditar['portada_url'])): ?>
      <div>
        <img src="<?= htmlspecialchars($libroEditar['portada_url']) ?>" alt="Portada actual">
      </div>
    <?php endif; ?>
    <input type="file" name="portada" accept="image/*">

    <button type="submit"><?= $editando ? 'Actualizar libro' : 'Agregar libro' ?></button>
  </form>
</div>
</body>
</html>
