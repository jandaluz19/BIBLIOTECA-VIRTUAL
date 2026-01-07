<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once 'conexion.php'; // Ajusta la ruta si tu conexion.php está en otra carpeta

// Forzar errores de mysqli como excepciones
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Método no permitido'
        ]);
        exit;
    }

    // Recibir datos
    $nombre   = trim($_POST['nombre'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $celular  = trim($_POST['celular'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirmPassword'] ?? '';

    // Validaciones
    if ($nombre === '' || $correo === '' || $password === '' || $confirm === '') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Todos los campos obligatorios deben completarse'
        ]);
        exit;
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Correo inválido'
        ]);
        exit;
    }

    if ($password !== $confirm) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Las contraseñas no coinciden'
        ]);
        exit;
    }

    // Verificar si el correo ya existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'El correo ya está registrado'
        ]);
        exit;
    }

    // Encriptar contraseña
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, celular, password, tipo, activo, creado_en) VALUES (?, ?, ?, ?, 'usuario', 1, NOW())");
    $stmt->bind_param("ssss", $nombre, $correo, $celular, $hash);
    $stmt->execute();

    echo json_encode([
        'status' => 'success',
        'message' => 'Registro exitoso'
    ]);
    exit;

} catch (Throwable $e) {
    error_log('[REGISTER] ' . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno en el servidor'
    ]);
    exit;
}
