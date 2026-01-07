/**
 * Login simple sin backend (solo para prototipo)
 */

function login(event) {
    event.preventDefault();
    
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const messageDiv = document.getElementById('message');

    // Usuarios predefinidos (en producción, usarías una API segura)
    if ((username === 'admin' && password === '1234') || (username === 'usuario' && password === '1234')) {
        // Guardar sesión
        localStorage.setItem('isLoggedIn', 'true');
        localStorage.setItem('username', username);
        localStorage.setItem('role', username === 'admin' ? 'admin' : 'user');
        
        // Mostrar mensaje y redirigir
        messageDiv.textContent = 'Iniciando sesión...';
        messageDiv.style.color = 'green';
        
        // Redirigir según rol
        const targetPage = (username === 'admin') ? 'dashboard.html' : 'catalogo.html';
        setTimeout(() => {
            window.location.href = targetPage;
        }, 600);
    } else {
        messageDiv.textContent = 'Usuario o contraseña incorrectos';
        messageDiv.style.color = 'red';
    }
}