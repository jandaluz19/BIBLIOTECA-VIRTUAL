/**
 * Vista de Estadísticas
 * Renderizado de estadísticas y gráficos
 */

import { formatNumber } from '../utils/helpers.js';

class StatsView {
    /**
     * Actualizar estadísticas generales
     */
    actualizarStats(stats) {
        this.actualizarStat('totalLibros', stats.total_libros || 0);
        this.actualizarStat('totalCategorias', stats.total_categorias || 0);
        this.actualizarStat('librosDisponibles', this.calcularDisponibles(stats));
        this.actualizarStat('promedioCalificacion', 
            parseFloat(stats.calificacion_promedio || 0).toFixed(1)
        );
    }

    /**
     * Actualizar un stat individual con animación
     */
    actualizarStat(elementId, valor) {
        const element = document.getElementById(elementId);
        if (!element) return;

        const valorActual = parseInt(element.textContent) || 0;
        const valorNuevo = parseInt(valor) || parseFloat(valor);

        this.animateValue(element, valorActual, valorNuevo, 1000);
    }

    /**
     * Animar cambio de valor
     */
    animateValue(element, start, end, duration) {
        const startTime = performance.now();
        const isDecimal = !Number.isInteger(end);

        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            const easeOutQuad = progress * (2 - progress);
            const current = start + (end - start) * easeOutQuad;

            if (isDecimal) {
                element.textContent = current.toFixed(1);
            } else {
                element.textContent = Math.floor(current);
            }

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }

    /**
     * Calcular libros disponibles
     */
    calcularDisponibles(stats) {
        if (stats.libros_disponibles !== undefined) {
            return stats.libros_disponibles;
        }
        
        const total = stats.total_libros || 0;
        const prestados = stats.prestamos_activos || 0;
        return total - prestados;
    }

    /**
     * Renderizar gráfico de categorías
     */
    renderizarGraficoCategorias(estadisticas) {
        const container = document.getElementById('chartCategorias');
        if (!container) return;

        if (!estadisticas || estadisticas.length === 0) {
            container.innerHTML = '<p style="color: #6b7280; text-align: center;">No hay datos disponibles</p>';
            return;
        }

        // Crear gráfico de barras simple con HTML/CSS
        const maxLibros = Math.max(...estadisticas.map(e => e.total_libros));

        const html = estadisticas.map(est => {
            const porcentaje = (est.total_libros / maxLibros) * 100;
            const color = est.color || '#2563eb';

            return `
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-weight: 600; color: #374151;">
                            ${est.icono || '📚'} ${est.nombre}
                        </span>
                        <span style="color: #6b7280;">${est.total_libros} libros</span>
                    </div>
                    <div style="background: #e5e7eb; border-radius: 9999px; height: 10px; overflow: hidden;">
                        <div style="
                            background: ${color};
                            height: 100%;
                            width: ${porcentaje}%;
                            transition: width 0.5s ease-out;
                        "></div>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = html;
    }

    /**
     * Renderizar top libros más prestados
     */
    renderizarMasPrestados(libros) {
        const container = document.getElementById('chartMasPrestados');
        if (!container) return;

        if (!libros || libros.length === 0) {
            container.innerHTML = '<p style="color: #6b7280; text-align: center;">No hay datos disponibles</p>';
            return;
        }

        const maxPrestamos = Math.max(...libros.map(l => l.veces_prestado || 0));

        const html = libros.map((libro, index) => {
            const prestamos = libro.veces_prestado || 0;
            const porcentaje = maxPrestamos > 0 ? (prestamos / maxPrestamos) * 100 : 0;
            const color = this.getColorByIndex(index);

            return `
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-weight: 600; color: #374151; flex: 1;">
                            ${index + 1}. ${libro.titulo}
                        </span>
                        <span style="color: #6b7280;">${prestamos} préstamos</span>
                    </div>
                    <div style="background: #e5e7eb; border-radius: 9999px; height: 8px; overflow: hidden;">
                        <div style="
                            background: ${color};
                            height: 100%;
                            width: ${porcentaje}%;
                            transition: width 0.5s ease-out;
                        "></div>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = html;
    }

    /**
     * Obtener color por índice
     */
    getColorByIndex(index) {
        const colors = [
            '#3b82f6', // Azul
            '#10b981', // Verde
            '#f59e0b', // Amarillo
            '#ef4444', // Rojo
            '#8b5cf6', // Púrpura
            '#ec4899', // Rosa
            '#14b8a6', // Teal
            '#f97316', // Naranja
            '#6366f1', // Índigo
            '#a855f7'  // Violeta
        ];
        return colors[index % colors.length];
    }

    /**
     * Renderizar tabla de estadísticas por categoría
     */
    renderizarTablaCategorias(estadisticas) {
        if (!estadisticas || estadisticas.length === 0) return;

        const html = `
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f3f4f6;">
                            <th style="padding: 1rem; text-align: left; font-weight: 600;">Categoría</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600;">Total</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600;">Disponibles</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600;">Préstamos</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600;">Calificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${estadisticas.map((est, index) => `
                            <tr style="border-bottom: 1px solid #e5e7eb; ${index % 2 === 0 ? 'background: #f9fafb;' : ''}">
                                <td style="padding: 1rem;">
                                    <span style="margin-right: 0.5rem;">${est.icono || '📚'}</span>
                                    <strong>${est.nombre}</strong>
                                </td>
                                <td style="padding: 1rem; text-align: center;">${est.total_libros || 0}</td>
                                <td style="padding: 1rem; text-align: center;">${est.libros_disponibles || 0}</td>
                                <td style="padding: 1rem; text-align: center;">${est.total_prestamos || 0}</td>
                                <td style="padding: 1rem; text-align: center;">
                                    ⭐ ${parseFloat(est.calificacion_promedio || 0).toFixed(1)}
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;

        const container = document.getElementById('tablaEstadisticas');
        if (container) {
            container.innerHTML = html;
        }
    }

    /**
     * Mostrar/ocultar sección de estadísticas
     */
    toggleSeccionEstadisticas(mostrar = true) {
        const seccion = document.getElementById('estadisticasSection');
        if (seccion) {
            seccion.style.display = mostrar ? 'block' : 'none';
        }
    }
}

// Exportar instancia única
export const statsView = new StatsView();

export default StatsView;