import { isAuthenticated } from '../auth.js';

export const app = {
    init() {
        const currentPage = location.pathname.split('/').pop();

        const protectedPages = [
            'catalogo.html',
            'ver-libro.html',
            'libros.html',
            'estadistica.html'
        ];

        // 🔐 Si NO está logueado y entra a página protegida
        if (protectedPages.includes(currentPage) && !isAuthenticated()) {
            window.location.href = '/BIBLIOTECA-VIRTUAL/frontend/login.html';
            return;
        }

        // 🚀 Si YA está logueado y entra al login
        if (currentPage === 'login.html' && isAuthenticated()) {
            window.location.href = '/BIBLIOTECA-VIRTUAL/frontend/catalogo.html';
        }
    }
};
