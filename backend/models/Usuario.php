<?php
/**
 * Modelo Usuario
 */

class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        return $this->db->select(
            "SELECT id,nombre,email,telefono,tipo,activo,fecha_registro 
             FROM usuarios WHERE activo = 1"
        );
    }

    public function getById($id) {
        $r = $this->db->select(
            "SELECT id,nombre,email,telefono,tipo,activo 
             FROM usuarios WHERE id = :id",
            [':id' => $id]
        );
        return $r[0] ?? null;
    }

    public function getByEmail($email) {
        $r = $this->db->select(
            "SELECT * FROM usuarios WHERE email = :email",
            [':email' => $email]
        );
        return $r[0] ?? null;
    }

    public function authenticate($email, $password) {
        $user = $this->getByEmail($email);
        if (!$user || !$user['activo']) return false;

        if (verifyPassword($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return false;
    }

    public function create($data) {
        return $this->db->execute(
            "INSERT INTO usuarios (nombre,email,password,telefono,tipo,activo)
             VALUES (:n,:e,:p,:t,'usuario',1)",
            [
                ':n' => $data['nombre'],
                ':e' => $data['email'],
                ':p' => hashPassword($data['password']),
                ':t' => $data['telefono'] ?? null
            ]
        );
    }

    /**
     * ===============================
     * RECUPERAR CONTRASEÑA
     * ===============================
     */

    public function createPasswordReset($email, $token, $expires) {
        return $this->db->execute(
            "INSERT INTO password_resets (email, token, expires_at)
             VALUES (:e,:t,:x)",
            [
                ':e' => $email,
                ':t' => $token,
                ':x' => $expires
            ]
        );
    }

    public function getPasswordResetByToken($token) {
        $r = $this->db->select(
            "SELECT * FROM password_resets 
             WHERE token = :t AND expires_at > NOW()",
            [':t' => $token]
        );
        return $r[0] ?? null;
    }

    public function deletePasswordReset($token) {
        return $this->db->execute(
            "DELETE FROM password_resets WHERE token = :t",
            [':t' => $token]
        );
    }

    public function updatePasswordByEmail($email, $password) {
        return $this->db->execute(
            "UPDATE usuarios SET password = :p WHERE email = :e",
            [
                ':p' => hashPassword($password),
                ':e' => $email
            ]
        );
    }

    public function validate($data) {
        $errors = [];
        if (empty($data['nombre'])) $errors['nombre'] = 'Nombre requerido';
        if (!isValidEmail($data['email'])) $errors['email'] = 'Email inválido';
        if (strlen($data['password']) < PASSWORD_MIN_LENGTH)
            $errors['password'] = 'Contraseña corta';
        return $errors;
    }
}
