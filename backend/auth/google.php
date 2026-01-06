<?php
session_start();
header("Content-Type: application/json");

$token = $_POST['credential'] ?? null;

if (!$token) {
    http_response_code(400);
    echo json_encode(["success" => false, "msg" => "Token no recibido"]);
    exit;
}

$url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $token;
$response = file_get_contents($url);
$data = json_decode($response, true);

if (!isset($data['email'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "msg" => "Token inválido"]);
    exit;
}

/* Aquí luego puedes guardar en BD */
$_SESSION['usuario'] = [
    "nombre" => $data['name'],
    "email"  => $data['email'],
    "foto"   => $data['picture']
];

echo json_encode([
    "success" => true,
    "usuario" => $_SESSION['usuario']
]);
