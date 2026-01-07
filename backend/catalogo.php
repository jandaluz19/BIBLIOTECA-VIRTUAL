<?php
session_start();
require_once 'conexion.php';

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../frontend/login.html");
    exit;
}

// Si hay búsqueda
$search = isset($_GET['q']) ? trim($_GET['q']) : null;

if ($search) {
    $stmt = $conn->prepare("SELECT * FROM libros WHERE disponible = 1 AND titulo LIKE CONCAT('%', ?, '%') ORDER BY titulo ASC");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $libros = $stmt->get_result();
} else {
    $libros = $conn->query("SELECT * FROM libros WHERE disponible = 1 ORDER BY titulo ASC");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Catálogo | Biblioteca Virtual</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f4f4f9; margin: 0; }
    header { background: #2a5298; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
    header a { color: white; text-decoration: none; font-weight: bold; }
    .buscador { padding: 1rem; display: flex; gap: 0.5rem; }
    .buscador input { flex: 1; padding: 8px; border-radius: 6px; border: 1px solid #ccc; }
    .buscador button { background: #2a5298; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; }
    .catalogo { padding: 1rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem; }
    .tarjeta { background: white; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,.1); padding: 1rem; text-align: center; }
    .tarjeta img { width: 120px; height: 160px; object-fit: cover; border-radius: 6px; margin-bottom: 0.5rem; }
    .tarjeta h3 { margin: 0; color: #2a5298; font-size: 1rem; }
    .tarjeta p { font-size: 0.85rem; color: #555; }
    .tarjeta button { display: inline-block; margin-top: 0.5rem; background: #2a5298; color: white; padding: 6px 12px; border-radius: 6px; border:none; cursor:pointer; }

    /* Modal PDF */
    #modalPDF { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; }
    #modalPDF .contenido { position:relative; width:80%; height:80%; margin:5% auto; background:#fff; padding:10px; border-radius:8px; }
    #modalPDF iframe { width:100%; height:100%; border:none; }
    #modalPDF .cerrar { position:absolute; top:10px; right:10px; background:#d32f2f; color:#fff; border:none; padding:6px 12px; border-radius:4px; cursor:pointer; }
  </style>
</head>
<body>
<header>
  <h1>📚 Biblioteca Virtual</h1>
  <a href="logout.php">🚪 Cerrar sesión</a>
</header>


<div class="buscador">
  <form method="GET" action="catalogo.php" style="display:flex; gap:0.5rem; width:100%;">
    <input type="text" name="q" placeholder="Buscar libros..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
    <button type="submit">🔍</button>
  </form>
</div>


<div class="catalogo">
  <?php if ($libros && $libros->num_rows > 0): ?>
    <?php while ($libro = $libros->fetch_assoc()): ?>
      <?php
        $portada = !empty($libro['portada_url'])
          ? $libro['portada_url']
          : 'https://via.placeholder.com/120x160?text=Sin+Portada';
        $pdf = !empty($libro['archivo_pdf']) ? $libro['archivo_pdf'] : null;
      ?>
      <div class="tarjeta">
        <img src="<?php echo htmlspecialchars($portada); ?>" alt="Portada de <?php echo htmlspecialchars($libro['titulo']); ?>">
        <h3><?php echo htmlspecialchars($libro['titulo']); ?></h3>
        <p><strong>Autor:</strong> <?php echo htmlspecialchars($libro['autor']); ?></p>
        <p><?php echo htmlspecialchars(substr($libro['descripcion'] ?? '', 0, 80)); ?>...</p>
        <?php if ($pdf): ?>
          <button onclick="verPDF('<?php echo htmlspecialchars($pdf); ?>')">📖 Ver PDF</button>
        <?php else: ?>
          <span style="color:#999;">Sin PDF disponible</span>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p style="padding:1rem;">❌ No se encontraron libros con ese nombre.</p>
  <?php endif; ?>
</div>

<!-- Modal PDF -->
<div id="modalPDF">
  <div class="contenido">
    <button class="cerrar" onclick="cerrarPDF()">✖ Cerrar</button>
    <iframe id="iframePDF" src=""></iframe>
  </div>
</div>

<script>
function verPDF(rutaPDF) {
  document.getElementById("iframePDF").src = rutaPDF;
  document.getElementById("modalPDF").style.display = "block";
}
function cerrarPDF() {
  document.getElementById("modalPDF").style.display = "none";
  document.getElementById("iframePDF").src = "";
}
</script>

</body>
</html>
