<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token    = $_POST['token'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "SELECT * FROM recuperaciones WHERE token = ? AND expira_en > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $rec = $result->fetch_assoc();
        $usuario_id = $rec['usuario_id'];

        // Actualizar contraseña
        $sqlUpdate = "UPDATE usuarios SET password = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("si", $password, $usuario_id);
        $stmtUpdate->execute();

        // Eliminar token usado
        $sqlDel = "DELETE FROM recuperaciones WHERE id = ?";
        $stmtDel = $conn->prepare($sqlDel);
        $stmtDel->bind_param("i", $rec['id']);
        $stmtDel->execute();

        echo "Contraseña restablecida correctamente";
    } else {
        echo "Token inválido o expirado";
    }
}
?>
