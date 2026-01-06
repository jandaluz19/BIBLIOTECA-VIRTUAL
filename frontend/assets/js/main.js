/**
 * Archivo Principal de Inicialización
 * Punto de entrada de la aplicación
 */

import { app } from './controllers/app.js';
import { apiService } from './services/api.js';
import { showToast } from './utils/helpers.js';

document.addEventListener('DOMContentLoaded', async () => {
    try {
        console.log('🚀 Iniciando Biblioteca Virtual...');

        // 🔌 Verificar API (opcional)
        try {
            const health = await apiService.healthCheck();
            if (health.status === 'healthy') {
                console.log('✅ API conectada');
            }
        } catch {
            showToast('Modo offline: algunas funciones no estarán disponibles', 'warning');
        }

        // 🚀 Inicializar lógica principal (login / redirecciones)
        app.init();

        console.log('✨ Biblioteca Virtual lista');

    } catch (error) {
        console.error('❌ Error al inicializar:', error);
        showToast('Error al inicializar la aplicación', 'error');
    }
});

/**
 * Manejo global de errores
 */
window.addEventListener('error', (event) => {
    console.error('Error global:', event.error);
});

window.addEventListener('unhandledrejection', (event) => {
    console.error('Promise rejection:', event.reason);
});

/**
 * Estado de red
 */
window.addEventListener('offline', () => {
    showToast('Sin conexión a internet', 'warning');
});

window.addEventListener('online', () => {
    showToast('Conexión restaurada', 'success');
});

console.log(`
╔═══════════════════════════════════════╗
║   📚 BIBLIOTECA VIRTUAL v1.0.0        ║
║   Estadística e Informática           ║
║   © 2025 - Sistema de Gestión         ║
╚═══════════════════════════════════════╝
`);
