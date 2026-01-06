import { isAuthenticated, getUser } from '../auth.js';

export const app = {
    init() {
        const currentPage = location.pathname.split('/').pop();

        const protectedPages = [
            'catalogo.html',
            'ver-libro.html',
            'admin.html'
        ];

        const publicPages = [
            'login.html',
            'index.html'
        ];

        const logged = isAuthenticated();

        // 🔒 Protección de páginas privadas
        if (protectedPages.includes(currentPage) && !logged) {
            window.location.href = 'login.html';
            return;
        }

        // 🚀 Redirección automática si ya está logueado
        if (logged && publicPages.includes(currentPage)) {
            window.location.href = 'catalogo.html';
            return;
        }

        // 👤 Mostrar info del usuario si existe
        const user = getUser();
        if (user) {
            console.log('Usuario activo:', user.nombre);
        }
    }
};
