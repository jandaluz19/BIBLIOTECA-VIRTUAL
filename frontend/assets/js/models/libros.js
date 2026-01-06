/**
 * Modelo de Libros
 * Lógica de negocio para libros
 */

import { apiService } from '../services/api.js';
import { validateLibro } from '../utils/validators.js';
import { showToast, showLoading } from '../utils/helpers.js';
import { MESSAGES } from '../utils/constants.js';

class LibrosModel {
    constructor() {
        this.libros = [];
        this.currentLibro = null;
        this.filtros = {
            q: '',
            categoria_id: '',
            orden: 'titulo',
            disponible: null
        };
    }

    /**
     * Cargar todos los libros
     */
    async cargarLibros(params = {}) {
        try {
            showLoading(true);
            const response = await apiService.getLibros(params);
            this.libros = response.data?.libros || response.data || [];
            return this.libros;
        } catch (error) {
            console.error('Error cargando libros:', error);
            showToast(MESSAGES.ERROR.CARGA_LIBROS, 'error');
            throw error;
        } finally {
            showLoading(false);
        }
    }

    /**
     * Buscar libros
     */
    async buscar(filtros = {}) {
        try {
            showLoading(true);
            this.filtros = { ...this.filtros, ...filtros };
            const response = await apiService.buscarLibros(this.filtros);
            this.libros = response.data?.libros || response.data || [];
            return {
                libros: this.libros,
                total: response.data?.total || this.libros.length
            };
        } catch (error) {
            console.error('Error buscando libros:', error);
            showToast('Error al buscar libros', 'error');
            throw error;
        } finally {
            showLoading(false);
        }
    }

    /**
     * Obtener libro por ID
     */
    async obtenerLibro(id) {
        try {
            showLoading(true);
            const response = await apiService.getLibro(id);
            this.currentLibro = response.data;
            return this.currentLibro;
        } catch (error) {
            console.error('Error obteniendo libro:', error);
            showToast('Error al cargar el libro', 'error');
            throw error;
        } finally {
            showLoading(false);
        }
    }

    /**
     * Crear nuevo libro
     */
    async crear(data) {
        try {
            // Validar datos
            const validation = validateLibro(data);
            if (!validation.isValid) {
                return {
                    success: false,
                    errors: validation.errors
                };
            }

            showLoading(true);
            const response = await apiService.crearLibro(data);
            
            if (response.success) {
                showToast(MESSAGES.SUCCESS.LIBRO_CREADO, 'success');
                // Agregar a la lista local
                this.libros.push(response.data);
            }

            return {
                success: true,
                data: response.data
            };
        } catch (error) {
            console.error('Error creando libro:', error);
            showToast(error.message || MESSAGES.ERROR.CREAR_LIBRO, 'error');
            return {
                success: false,
                error: error.message
            };
        } finally {
            showLoading(false);
        }
    }

    /**
     * Actualizar libro
     */
    async actualizar(id, data) {
        try {
            // Validar datos
            const validation = validateLibro(data);
            if (!validation.isValid) {
                return {
                    success: false,
                    errors: validation.errors
                };
            }

            showLoading(true);
            const response = await apiService.actualizarLibro(id, data);
            
            if (response.success) {
                showToast(MESSAGES.SUCCESS.LIBRO_ACTUALIZADO, 'success');
                
                // Actualizar en la lista local
                const index = this.libros.findIndex(l => l.id == id);
                if (index !== -1) {
                    this.libros[index] = response.data;
                }
            }

            return {
                success: true,
                data: response.data
            };
        } catch (error) {
            console.error('Error actualizando libro:', error);
            showToast(error.message || MESSAGES.ERROR.ACTUALIZAR_LIBRO, 'error');
            return {
                success: false,
                error: error.message
            };
        } finally {
            showLoading(false);
        }
    }

    /**
     * Eliminar libro
     */
    async eliminar(id) {
        try {
            if (!confirm('¿Estás seguro de eliminar este libro?')) {
                return { success: false, cancelled: true };
            }

            showLoading(true);
            const response = await apiService.eliminarLibro(id);
            
            if (response.success) {
                showToast(MESSAGES.SUCCESS.LIBRO_ELIMINADO, 'success');
                
                // Remover de la lista local
                this.libros = this.libros.filter(l => l.id != id);
            }

            return { success: true };
        } catch (error) {
            console.error('Error eliminando libro:', error);
            showToast(error.message || MESSAGES.ERROR.ELIMINAR_LIBRO, 'error');
            return {
                success: false,
                error: error.message
            };
        } finally {
            showLoading(false);
        }
    }

    /**
     * Obtener libros más prestados
     */
    async masPrestados(limit = 10) {
        try {
            const response = await apiService.getLibrosMasPrestados(limit);
            return response.data || [];
        } catch (error) {
            console.error('Error obteniendo libros más prestados:', error);
            return [];
        }
    }

    /**
     * Obtener libros mejor calificados
     */
    async mejorCalificados(limit = 10) {
        try {
            const response = await apiService.getLibrosMejorCalificados(limit);
            return response.data || [];
        } catch (error) {
            console.error('Error obteniendo libros mejor calificados:', error);
            return [];
        }
    }

    /**
     * Obtener libros recientes
     */
    async recientes(limit = 10) {
        try {
            const response = await apiService.getLibrosRecientes(limit);
            return response.data || [];
        } catch (error) {
            console.error('Error obteniendo libros recientes:', error);
            return [];
        }
    }

    /**
     * Filtrar libros localmente
     */
    filtrarLocal(query) {
        if (!query || query.trim() === '') {
            return this.libros;
        }

        const lowerQuery = query.toLowerCase();
        return this.libros.filter(libro => {
            return (
                libro.titulo?.toLowerCase().includes(lowerQuery) ||
                libro.autor?.toLowerCase().includes(lowerQuery) ||
                libro.descripcion?.toLowerCase().includes(lowerQuery)
            );
        });
    }

    /**
     * Ordenar libros
     */
    ordenar(campo = 'titulo', orden = 'asc') {
        return [...this.libros].sort((a, b) => {
            let aVal = a[campo];
            let bVal = b[campo];

            // Convertir a minúsculas si son strings
            if (typeof aVal === 'string') aVal = aVal.toLowerCase();
            if (typeof bVal === 'string') bVal = bVal.toLowerCase();

            if (aVal < bVal) return orden === 'asc' ? -1 : 1;
            if (aVal > bVal) return orden === 'asc' ? 1 : -1;
            return 0;
        });
    }

    /**
     * Agrupar libros por categoría
     */
    agruparPorCategoria() {
        return this.libros.reduce((grupos, libro) => {
            const cat = libro.categoria || 'Sin categoría';
            if (!grupos[cat]) {
                grupos[cat] = [];
            }
            grupos[cat].push(libro);
            return grupos;
        }, {});
    }

    /**
     * Obtener estadísticas
     */
    obtenerEstadisticas() {
        return {
            total: this.libros.length,
            disponibles: this.libros.filter(l => l.disponible).length,
            noDisponibles: this.libros.filter(l => !l.disponible).length,
            promedioCalificacion: this.calcularPromedioCalificacion()
        };
    }

    /**
     * Calcular promedio de calificación
     */
    calcularPromedioCalificacion() {
        const librosCalificados = this.libros.filter(l => l.calificacion > 0);
        if (librosCalificados.length === 0) return 0;
        
        const suma = librosCalificados.reduce((acc, l) => acc + parseFloat(l.calificacion), 0);
        return (suma / librosCalificados.length).toFixed(1);
    }

    /**
     * Resetear filtros
     */
    resetearFiltros() {
        this.filtros = {
            q: '',
            categoria_id: '',
            orden: 'titulo',
            disponible: null
        };
    }

    /**
     * Limpiar datos
     */
    limpiar() {
        this.libros = [];
        this.currentLibro = null;
        this.resetearFiltros();
    }
}

// Exportar instancia única
export const librosModel = new LibrosModel();

export default LibrosModel;