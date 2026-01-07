<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once 'conexion.php';
require_once 'vendor/autoload.php'; // Necesitas instalar google/apiclient con Composer

$input = json_decode(file_get_contents("php://input"), true);
$token = $input['token'] ?? '';

if (!$token) {
    echo json_encode(['status' => 'error', 'message' => 'Token no recibido']);
    exit;
}

try {
    // Configura tu CLIENT_ID de Google
    $client = new Google_Client(['client_id' => '892296470916-v6i0gamfpur8ircts0nrd9kvp99qhr0b.apps.googleusercontent.com']);
    $payload = $client->verifyIdToken($token);

    if ($payload) {
        $correo = $payload['email'];
        $nombre = $payload['name'];

        // Buscar usuario en BD
        $stmt = $conn->prepare("SELECT id, nombre, email, tipo FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
        } else {
            // Registrar nuevo usuario
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, tipo, activo, creado_en) VALUES (?, ?, 'usuario', 1, NOW())");
            $stmt->bind_param("ss", $nombre, $correo);
            $stmt->execute();
            $usuario = [
                'id'     => $conn->insert_id,
                'nombre' => $nombre,
                'email'  => $correo,
                'tipo'   => 'usuario'
            ];
        }

        // Guardar sesión
        $_SESSION['usuario_id']   = $usuario['id'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];

        echo json_encode(['status' => 'success', 'message' => 'Login con Google exitoso', 'usuario' => $usuario]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Token inválido']);
    }

} catch (Throwable $e) {
    error_log('[GOOGLE LOGIN] ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Error interno en el servidor']);
}
