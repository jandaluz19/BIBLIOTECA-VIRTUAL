// 📌 Endpoint real del login
const LOGIN_URL = '/BIBLIOTECA-VIRTUAL/backend/api/usuarios/login/index.php';

/**
 * Iniciar sesión
 */
export async function login(email, password) {
    const response = await fetch(LOGIN_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
    });

    // Esperamos JSON del backend
    const data = await response.json();

    // ❌ Login incorrecto
    if (!response.ok || data.status !== 'success') {
        throw new Error(data.message || 'Credenciales incorrectas');
    }

    // ✅ Login correcto → guardar sesión en localStorage
    localStorage.setItem('token', 'authenticated');
    localStorage.setItem(
        'usuario',
        JSON.stringify({
            id: data.usuario?.id,
            email: data.usuario?.email,
            tipo: data.usuario?.tipo
        })
    );

    return true;
}

/**
 * Cerrar sesión
 */
export function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('usuario');
    window.location.href = '/BIBLIOTECA-VIRTUAL/frontend/login.html';
}

/**
 * Verificar autenticación
 */
export function isAuthenticated() {
    return localStorage.getItem('token') === 'authenticated';
}

/**
 * Obtener usuario actual
 */
export function getUser() {
    const user = localStorage.getItem('usuario');
    return user ? JSON.parse(user) : null;
}
