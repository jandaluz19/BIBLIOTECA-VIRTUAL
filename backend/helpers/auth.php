<?php

function requireAdmin($user) {
    if (!$user || $user['tipo'] !== 'admin') {
        errorResponse('Acceso denegado: solo administradores', 403);
    }
function getAuthenticatedUser() {
    return $_SESSION['usuario'] ?? null;
}

}
