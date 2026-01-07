const LOGIN_URL = '/BIBLIOTECA-VIRTUAL/backend/login';

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

    const text = await response.text();

    // Normalizamos el texto
    const cleanText = text.toLowerCase();

    // ❌ Login incorrecto
    if (!response.ok || !cleanText.includes('exitoso')) {
        throw new Error(text || 'Credenciales incorrectas');
    }

    // ✅ Login correcto → guardar sesión
    localStorage.setItem('token', 'authenticated');
    localStorage.setItem(
        'usuario',
        JSON.stringify({ email })
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
