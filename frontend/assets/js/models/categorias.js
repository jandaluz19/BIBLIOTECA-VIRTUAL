/**
 * Modelo de Categorías
 * Lógica de negocio para categorías
 */

import { apiService } from '../services/api.js';
import { validateCategoria } from '../utils/validators.js';
import { showToast, showLoading } from '../utils/helpers.js';
import { MESSAGES, CATEGORY_ICONS, CATEGORY_COLORS } from '../utils/constants.js';

class CategoriasModel {
    constructor() {
        this.categorias = [];
        this.estadisticas = [];
    }

    /**
     * Cargar todas las categorías
     */
    async cargar() {
        try {
            showLoading(true);
            const response = await apiService.getCategorias();
            this.categorias = response.data || response || [];
            return this.categorias;
        } catch (error) {
            console.error('Error cargando categorías:', error);
            showToast(MESSAGES.ERROR.CARGA_CATEGORIAS, 'error');
            throw error;
        } finally {
            showLoading(false);
        }
    }

    /**
     * Obtener categoría por ID
     */
    async obtener(id) {
        try {
            const response = await apiService.getCategoria(id);
            return response.data;
        } catch (error) {
            console.error('Error obteniendo categoría:', error);
            showToast('Error al cargar la categoría', 'error');
            throw error;
        }
    }

    /**
     * Crear nueva categoría
     */
    async crear(data) {
        try {
            // Validar datos
            const validation = validateCategoria(data);
            if (!validation.isValid) {
                return {
                    success: false,
                    errors: validation.errors
                };
            }

            showLoading(true);
            const response = await apiService.crearCategoria(data);
            
            if (response.success) {
                showToast(MESSAGES.SUCCESS.CATEGORIA_CREADA, 'success');
                this.categorias.push(response.data);
            }

            return {
                success: true,
                data: response.data
            };
        } catch (error) {
            console.error('Error creando categoría:', error);
            showToast(error.message || 'Error al crear la categoría', 'error');
            return {
                success: false,
                error: error.message
            };
        } finally {
            showLoading(false);
        }
    }

    /**
     * Actualizar categoría
     */
    async actualizar(id, data) {
        try {
            // Validar datos
            const validation = validateCategoria(data);
            if (!validation.isValid) {
                return {
                    success: false,
                    errors: validation.errors
                };
            }

            showLoading(true);
            const response = await apiService.actualizarCategoria(id, data);
            
            if (response.success) {
                showToast(MESSAGES.SUCCESS.CATEGORIA_ACTUALIZADA, 'success');
                
                // Actualizar en la lista local
                const index = this.categorias.findIndex(c => c.id == id);
                if (index !== -1) {
                    this.categorias[index] = response.data;
                }
            }

            return {
                success: true,
                data: response.data
            };
        } catch (error) {
            console.error('Error actualizando categoría:', error);
            showToast(error.message || 'Error al actualizar la categoría', 'error');
            return {
                success: false,
                error: error.message
            };
        } finally {
            showLoading(false);
        }
    }

    /**
     * Eliminar categoría
     */
    async eliminar(id) {
        try {
            if (!confirm('¿Estás seguro de eliminar esta categoría?')) {
                return { success: false, cancelled: true };
            }

            showLoading(true);
            const response = await apiService.eliminarCategoria(id);
            
            if (response.success) {
                showToast(MESSAGES.SUCCESS.CATEGORIA_ELIMINADA, 'success');
                this.categorias = this.categorias.filter(c => c.id != id);
            }

            return { success: true };
        } catch (error) {
            console.error('Error eliminando categoría:', error);
            showToast(error.message || 'Error al eliminar la categoría', 'error');
            return {
                success: false,
                error: error.message
            };
        } finally {
            showLoading(false);
        }
    }

    /**
     * Cargar estadísticas de categorías
     */
    async cargarEstadisticas() {
        try {
            const response = await apiService.getEstadisticasCategorias();
            this.estadisticas = response.data || [];
            return this.estadisticas;
        } catch (error) {
            console.error('Error cargando estadísticas:', error);
            return [];
        }
    }

    /**
     * Obtener icono por categoría
     */
    getIcono(nombreCategoria) {
        return CATEGORY_ICONS[nombreCategoria] || CATEGORY_ICONS.default;
    }

    /**
     * Obtener color por categoría
     */
    getColor(nombreCategoria) {
        return CATEGORY_COLORS[nombreCategoria] || CATEGORY_COLORS.default;
    }

    /**
     * Buscar categoría por nombre
     */
    buscarPorNombre(nombre) {
        if (!nombre) return this.categorias;
        
        const lowerNombre = nombre.toLowerCase();
        return this.categorias.filter(cat => 
            cat.nombre.toLowerCase().includes(lowerNombre)
        );
    }

    /**
     * Obtener categorías activas
     */
    getActivas() {
        return this.categorias.filter(cat => cat.activo);
    }

    /**
     * Ordenar categorías por nombre
     */
    ordenarPorNombre(orden = 'asc') {
        return [...this.categorias].sort((a, b) => {
            const comparison = a.nombre.localeCompare(b.nombre);
            return orden === 'asc' ? comparison : -comparison;
        });
    }

    /**
     * Obtener total de categorías
     */
    getTotal() {
        return this.categorias.length;
    }

    /**
     * Limpiar datos
     */
    limpiar() {
        this.categorias = [];
        this.estadisticas = [];
    }
}

// Exportar instancia única
export const categoriasModel = new CategoriasModel();

export default CategoriasModel;