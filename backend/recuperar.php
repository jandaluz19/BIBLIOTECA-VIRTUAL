<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $sql = "SELECT * FROM usuarios WHERE email = ? AND activo = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Generar token único
        $token = bin2hex(random_bytes(32));
        $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Guardar token en tabla auxiliar
        $sqlToken = "INSERT INTO recuperaciones (usuario_id, token, expira_en) VALUES (?,?,?)";
        $stmtToken = $conn->prepare($sqlToken);
        $stmtToken->bind_param("iss", $usuario['id'], $token, $expira);
        $stmtToken->execute();

        // Enviar correo (ejemplo simple)
        $enlace = "http://tusitio.com/reset.php?token=" . $token;
        mail($email, "Recuperar contraseña", "Haz clic en el siguiente enlace para restablecer tu contraseña: $enlace");

        echo "Correo de recuperación enviado";
    } else {
        echo "Usuario no encontrado";
    }
}
?>
