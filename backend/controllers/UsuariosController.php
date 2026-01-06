<?php
/**
 * Controlador de Usuarios
 */

class UsuariosController {
    private $model;

    public function __construct() {
        $this->model = new Usuario();
    }

    public function index() {
        $usuarios = $this->model->getAll();
        jsonResponse($usuarios, 200);
    }

    public function show($id) {
        $usuario = $this->model->getById($id);
        if (!$usuario) {
            errorResponse('Usuario no encontrado', 404);
        }
        jsonResponse($usuario, 200);
    }

    public function create() {
        validateMethod('POST');

        $data = array_map('sanitize', getJsonInput());
        $errors = $this->model->validate($data);

        if (!empty($errors)) {
            errorResponse('Datos inválidos', 400, $errors);
        }

        $id = $this->model->create($data);
        jsonResponse($this->model->getById($id), 201, 'Usuario creado');
    }

    public function login() {
        validateMethod('POST');

        $data = getJsonInput();
        if (empty($data['email']) || empty($data['password'])) {
            errorResponse('Email y contraseña requeridos', 400);
        }

        $usuario = $this->model->authenticate($data['email'], $data['password']);
        if (!$usuario) {
            errorResponse('Credenciales inválidas', 401);
        }

        jsonResponse([
            'usuario' => $usuario,
            'token' => generateToken(32)
        ], 200, 'Login exitoso');
    }

    /**
     * ===============================
     * RECUPERAR CONTRASEÑA
     * ===============================
     */

    // 1️⃣ Solicitar recuperación
    public function recuperarPassword() {
        validateMethod('POST');

        $data = getJsonInput();
        if (empty($data['email'])) {
            errorResponse('Email requerido', 400);
        }

        $usuario = $this->model->getByEmail($data['email']);
        if (!$usuario) {
            errorResponse('Email no registrado', 404);
        }

        $token = generateToken(40);
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->model->createPasswordReset(
            $data['email'],
            $token,
            $expires
        );

        $link = "http://localhost/BIBLIOTECA-VIRTUAL/frontend/reset-password.html?token=$token";

        jsonResponse([
            'mensaje' => 'Enlace generado',
            'link' => $link,
            'expira' => $expires
        ], 200);
    }

    // 2️⃣ Confirmar nueva contraseña
    public function resetPassword() {
        validateMethod('POST');

        $data = getJsonInput();
        if (empty($data['token']) || empty($data['password'])) {
            errorResponse('Token y contraseña requeridos', 400);
        }

        if (strlen($data['password']) < PASSWORD_MIN_LENGTH) {
            errorResponse('Contraseña muy corta', 400);
        }

        $reset = $this->model->getPasswordResetByToken($data['token']);
        if (!$reset) {
            errorResponse('Token inválido o expirado', 400);
        }

        $this->model->updatePasswordByEmail(
            $reset['email'],
            $data['password']
        );

        $this->model->deletePasswordReset($data['token']);

        jsonResponse(null, 200, 'Contraseña actualizada');
    }
}
