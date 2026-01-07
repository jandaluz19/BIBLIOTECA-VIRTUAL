<?php
header('Content-Type: application/json');
session_start();

require_once '../../../conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if (!$email || !$password) {
    echo json_encode([
        'success' => false,
        'message' => 'Datos incompletos'
    ]);
    exit;
}

// 👇 EJEMPLO SIMPLE
echo json_encode([
    'success' => true,
    'data' => [
        'token' => 'token_de_prueba',
        'usuario' => [
            'email' => $email
        ]
    ]
]);
