<?php
$servername = "localhost";
$username   = "root";                  
$password   = "";                      
$dbname     = "biblioteca_virtual";   

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión con el servidor: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
