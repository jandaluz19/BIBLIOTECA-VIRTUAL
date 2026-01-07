<?php
include 'conexion.php'; // tu archivo de conexión a MySQL
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = trim($_POST['nombre']);
    $email    = trim($_POST['correo']);
    $telefono = trim($_POST['celular']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirmPassword'];

    // Validaciones básicas
    if (empty($nombre) || empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Todos los campos obligatorios deben completarse"]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Correo electrónico inválido"]);
        exit;
    }

    if ($password !== $confirm) {
        echo json_encode(["status" => "error", "message" => "Las contraseñas no coinciden"]);
        exit;
    }

    // Verificar si el correo ya existe
    $sqlCheck = "SELECT id FROM usuarios WHERE email = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $email);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "El correo ya está registrado"]);
        exit;
    }

    // Encriptar contraseña
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar usuario
    $sql = "INSERT INTO usuarios (nombre, email, password, telefono, tipo, activo) VALUES (?,?,?,?, 'usuario', 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $email, $hash, $telefono);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Registro exitoso"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al registrar usuario"]);
    }
}
?>
