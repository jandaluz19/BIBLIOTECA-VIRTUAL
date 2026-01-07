<?php
include 'conexion.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitizar entradas
    $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        echo "Faltan datos";
        exit;
    }

    // Buscar usuario activo
    $sql = "SELECT * FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Error en la consulta";
        exit;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verificar contraseña
        if (password_verify($password, $usuario['password'])) {
            // Actualizar último acceso
            $update = $conn->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
            $update->bind_param("i", $usuario['id']);
            $update->execute();

            // Guardar sesión
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];

            echo "Login exitoso";
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "Usuario no encontrado o inactivo";
    }
}
?>
