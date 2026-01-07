<?php
session_start();
require_once 'conexion.php';

// Forzar cabecera JSON para que el frontend reciba la respuesta correcta
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo   = trim($_POST['correo']);
    $password = trim($_POST['password']);

    // Buscar usuario por correo
    $stmt = $conn->prepare("SELECT id, nombre, email, password, tipo 
                            FROM usuarios 
                            WHERE email=? AND activo=1 LIMIT 1");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($usuario = $result->fetch_assoc()) {
        // ⚠️ Si tus contraseñas están en texto plano, usa comparación directa:
        // if ($password === $usuario['password']) {
        if (password_verify($password, $usuario['password'])) {
            // Guardar sesión
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];

            // Respuesta JSON según rol
            if ($_SESSION['usuario_tipo'] === 'admin') {
                echo json_encode([
                    "status"   => "success",
                    "redirect" => "../backend/admin_dashboard.php" // 👈 ruta corregida
                ]);
            } else {
                echo json_encode([
                    "status"   => "success",
                    "redirect" => "../backend/catalogo.php" // 👈 ruta corregida
                ]);
            }
        } else {
            echo json_encode([
                "status"  => "error",
                "message" => "Contraseña incorrecta"
            ]);
        }
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "Usuario no encontrado o inactivo"
        ]);
    }
} else {
    echo json_encode([
        "status"  => "error",
        "message" => "Método no permitido"
    ]);
}
?>
