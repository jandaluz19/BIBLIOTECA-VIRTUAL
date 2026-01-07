<?php

ob_start();

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once '../../../conexion.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
        exit;
    }

    $nombre   = trim($_POST['nombre'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $celular  = trim($_POST['celular'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $confirm  = (string)($_POST['confirmPassword'] ?? '');

    if ($nombre === '' || $correo === '' || $password === '' || $confirm === '') {
        http_response_code(400);
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Todos los campos obligatorios deben completarse']);
        exit;
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Correo inválido']);
        exit;
    }

    if ($password !== $confirm) {
        http_response_code(400);
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Las contraseñas no coinciden']);
        exit;
    }

    // Verificar si el correo ya existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $exists = $stmt->get_result();

    if ($exists && $exists->num_rows > 0) {
        http_response_code(409);
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'El correo ya está registrado']);
        exit;
    }

    // Insertar usuario con contraseña encriptada
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, celular, password, tipo, activo, creado_en) VALUES (?, ?, ?, ?, 'usuario', 1, NOW())");
    $stmt->bind_param("ssss", $nombre, $correo, $celular, $hash);
    $stmt->execute();

    http_response_code(201);
    ob_end_clean();
    echo json_encode(['status' => 'success', 'message' => 'Registro exitoso']);
    exit;

} catch (Throwable $e) {
    error_log('[REGISTER] ' . $e->getMessage());
    if (ob_get_level()) ob_end_clean();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error interno en el servidor']);
    exit;
}
