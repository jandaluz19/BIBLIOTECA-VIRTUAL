/**
 * Vista de Libros
 * Renderizado de libros en el DOM
 */

import { sanitize, truncate } from '../utils/helpers.js';
import { CATEGORY_ICONS, CATEGORY_COLORS } from '../utils/constants.js';

class BookView {
    constructor() {
        this.container = document.getElementById('librosGrid');
    }

    /**
     * Renderizar lista de libros
     */
    render(libros) {
        if (!this.container) return;

        if (!libros || libros.length === 0) {
            this.renderEmpty();
            return;
        }

        this.container.innerHTML = libros.map(libro => this.createBookCard(libro)).join('');
    }

    /**
     * Crear card de libro
     */
    createBookCard(libro) {
        const icon = CATEGORY_ICONS[libro.categoria] || CATEGORY_ICONS.default;
        const color = CATEGORY_COLORS[libro.categoria] || CATEGORY_COLORS.default;
        const disponibleClass = libro.disponible ? 'disponible' : 'no-disponible';
        const disponibleText = libro.disponible ? '✅ Disponible' : '❌ No disponible';

        return `
            <div class="book-card" data-id="${libro.id}">
                <div class="book-cover" style="background: linear-gradient(135deg, ${color} 0%, ${this.adjustColor(color, -20)} 100%)">
                    ${icon}
                </div>
                <div class="book-info">
                    <span class="book-category" style="background-color: ${color}20; color: ${color}">
                        ${sanitize(libro.categoria || 'Sin categoría')}
                    </span>
                    <h3 class="book-title">${sanitize(libro.titulo)}</h3>
                    <p class="book-author">por ${sanitize(libro.autor)}</p>
                    <p class="book-description">${sanitize(truncate(libro.descripcion || 'Sin descripción', 150))}</p>
                    <div class="book-meta">
                        <span>📅 ${libro.anio_publicacion || 'N/A'}</span>
                        <span>${libro.editorial || 'Sin editorial'}</span>
                    </div>
                    <div class="book-meta">
                        <span>⭐ ${parseFloat(libro.calificacion || 0).toFixed(1)}</span>
                        <span class="${disponibleClass}">${disponibleText}</span>
                    </div>
                    <div class="book-actions">
                        <button class="btn btn-sm btn-primary" onclick="app.editarLibro(${libro.id})">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="app.eliminarLibro(${libro.id})">
                            🗑️ Eliminar
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Renderizar mensaje vacío
     */
    renderEmpty() {
        this.container.innerHTML = `
            <div class="empty-state" style="
                grid-column: 1 / -1;
                text-align: center;
                padding: 4rem 2rem;
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            ">
                <div style="font-size: 4rem; margin-bottom: 1rem;">📚</div>
                <h3 style="color: #374151; margin-bottom: 0.5rem;">No se encontraron libros</h3>
                <p style="color: #6b7280;">Intenta ajustar los filtros de búsqueda</p>
            </div>
        `;
    }

    /**
     * Renderizar loader
     */
    renderLoader() {
        if (!this.container) return;
        
        this.container.innerHTML = `
            <div class="loader-container" style="
                grid-column: 1 / -1;
                display: flex;
                justify-content: center;
                padding: 4rem;
            ">
                <div class="spinner"></div>
            </div>
        `;
    }

    /**
     * Ajustar color (más oscuro o más claro)
     */
    adjustColor(color, amount) {
        const clamp = (num) => Math.min(Math.max(num, 0), 255);
        const num = parseInt(color.replace('#', ''), 16);
        const r = clamp((num >> 16) + amount);
        const g = clamp(((num >> 8) & 0x00FF) + amount);
        const b = clamp((num & 0x0000FF) + amount);
        return `#${((r << 16) | (g << 8) | b).toString(16).padStart(6, '0')}`;
    }

    /**
     * Limpiar vista
     */
    clear() {
        if (this.container) {
            this.container.innerHTML = '';
        }
    }
}

// Exportar instancia única
export const bookView = new BookView();

export default BookView;