async function login() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    const response = await fetch('/BIBLIOTECA-VIRTUAL/backend/api/usuarios/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
    });

    const data = await response.json();

    if (!response.ok) {
        alert(data.message || 'Credenciales incorrectas');
        return;
    }

    // ✅ GUARDAR SESIÓN
    localStorage.setItem('token', data.data.token);
    localStorage.setItem('usuario', JSON.stringify(data.data.usuario));

    // ✅ REDIRIGIR
    window.location.href = 'catalogo.html';
}
