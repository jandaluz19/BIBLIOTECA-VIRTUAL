console.log('🔥 login.js EJECUTADO');

import { login } from './auth.js';

document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    console.log('📩 submit capturado');

    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const message = document.getElementById('message');
    const btn = document.getElementById('loginBtn');

    message.textContent = '';
    btn.disabled = true;

    try {
        await login(email, password);

        console.log('✅ login OK, redirigiendo...');
        window.location.href = '/BIBLIOTECA-VIRTUAL/frontend/catalogo.html';

    } catch (error) {
        console.error(error);
        message.textContent = error.message;
        message.style.color = 'red';
    } finally {
        btn.disabled = false;
    }
});
