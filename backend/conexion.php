<?php
$servername = "localhost";
$username   = "root";        // tu usuario MySQL
$password   = "";            // tu contraseña MySQL
$dbname     = "biblioteca";  // tu base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
