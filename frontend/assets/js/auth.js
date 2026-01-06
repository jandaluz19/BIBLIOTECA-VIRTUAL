const API_URL = '/BIBLIOTECA-VIRTUAL/backend/api';

export async function login(email, password) {
    const response = await fetch(`${API_URL}/usuarios/login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email, password })
    });

    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || 'Error al iniciar sesión');
    }

    // 🔐 Guardar sesión
    localStorage.setItem('token', result.data.token);
    localStorage.setItem('usuario', JSON.stringify(result.data.usuario));

    return result.data.usuario;
}

export function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('usuario');
    window.location.href = 'login.html';
}

export function isAuthenticated() {
    return !!localStorage.getItem('token');
}

export function getUser() {
    const user = localStorage.getItem('usuario');
    return user ? JSON.parse(user) : null;
}

export function isAdmin() {
    const user = getUser();
    return user && user.tipo === 'admin';
}
