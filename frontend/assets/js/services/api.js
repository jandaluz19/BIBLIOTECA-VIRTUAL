/**
 * Servicio API
 * Manejo centralizado de peticiones HTTP
 */

import { API_BASE_URL, API_ENDPOINTS } from '../utils/constants.js';
import { showToast, showLoading } from '../utils/helpers.js';

class APIService {
    constructor() {
        this.baseURL = API_BASE_URL;
        this.token = null;
    }

    /**
     * Configurar token de autenticación
     */
    setToken(token) {
        this.token = token;
    }

    /**
     * Obtener headers por defecto
     */
    getHeaders() {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        if (this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }

        return headers;
    }

    /**
     * Manejar respuesta de la API
     */
    async handleResponse(response) {
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Error en la petición');
        }

        return data;
    }

    /**
     * Petición GET
     */
    async get(endpoint, params = {}) {
        try {
            const queryString = new URLSearchParams(params).toString();
            const url = `${this.baseURL}${endpoint}${queryString ? '?' + queryString : ''}`;

            const response = await fetch(url, {
                method: 'GET',
                headers: this.getHeaders()
            });

            return await this.handleResponse(response);
        } catch (error) {
            console.error('GET Error:', error);
            throw error;
        }
    }

    /**
     * Petición POST
     */
    async post(endpoint, data = {}) {
        try {
            const response = await fetch(`${this.baseURL}${endpoint}`, {
                method: 'POST',
                headers: this.getHeaders(),
                body: JSON.stringify(data)
            });

            return await this.handleResponse(response);
        } catch (error) {
            console.error('POST Error:', error);
            throw error;
        }
    }

    /**
     * Petición PUT
     */
    async put(endpoint, data = {}) {
        try {
            const response = await fetch(`${this.baseURL}${endpoint}`, {
                method: 'PUT',
                headers: this.getHeaders(),
                body: JSON.stringify(data)
            });

            return await this.handleResponse(response);
        } catch (error) {
            console.error('PUT Error:', error);
            throw error;
        }
    }

    /**
     * Petición DELETE
     */
    async delete(endpoint) {
        try {
            const response = await fetch(`${this.baseURL}${endpoint}`, {
                method: 'DELETE',
                headers: this.getHeaders()
            });

            return await this.handleResponse(response);
        } catch (error) {
            console.error('DELETE Error:', error);
            throw error;
        }
    }

    // ========================================
    // MÉTODOS ESPECÍFICOS - LIBROS
    // ========================================

    /**
     * Obtener todos los libros
     */
    async getLibros(params = {}) {
        return await this.get(API_ENDPOINTS.LIBROS, params);
    }

    /**
     * Obtener libro por ID
     */
    async getLibro(id) {
        return await this.get(API_ENDPOINTS.LIBRO_BY_ID(id));
    }

    /**
     * Buscar libros
     */
    async buscarLibros(query) {
        return await this.post(API_ENDPOINTS.LIBROS_BUSCAR, query);
    }

    /**
     * Crear libro
     */
    async crearLibro(data) {
        return await this.post(API_ENDPOINTS.LIBROS, data);
    }

    /**
     * Actualizar libro
     */
    async actualizarLibro(id, data) {
        return await this.put(API_ENDPOINTS.LIBRO_BY_ID(id), data);
    }

    /**
     * Eliminar libro
     */
    async eliminarLibro(id) {
        return await this.delete(API_ENDPOINTS.LIBRO_BY_ID(id));
    }

    /**
     * Obtener libros más prestados
     */
    async getLibrosMasPrestados(limit = 10) {
        return await this.get(API_ENDPOINTS.LIBROS_MAS_PRESTADOS, { limit });
    }

    /**
     * Obtener libros mejor calificados
     */
    async getLibrosMejorCalificados(limit = 10) {
        return await this.get(API_ENDPOINTS.LIBROS_MEJOR_CALIFICADOS, { limit });
    }

    /**
     * Obtener libros recientes
     */
    async getLibrosRecientes(limit = 10) {
        return await this.get(API_ENDPOINTS.LIBROS_RECIENTES, { limit });
    }

    /**
     * Obtener estadísticas de libros
     */
    async getEstadisticasLibros() {
        return await this.get(API_ENDPOINTS.LIBROS_ESTADISTICAS);
    }

    // ========================================
    // MÉTODOS ESPECÍFICOS - CATEGORÍAS
    // ========================================

    /**
     * Obtener todas las categorías
     */
    async getCategorias() {
        return await this.get(API_ENDPOINTS.CATEGORIAS);
    }

    /**
     * Obtener categoría por ID
     */
    async getCategoria(id) {
        return await this.get(API_ENDPOINTS.CATEGORIA_BY_ID(id));
    }

    /**
     * Crear categoría
     */
    async crearCategoria(data) {
        return await this.post(API_ENDPOINTS.CATEGORIAS, data);
    }

    /**
     * Actualizar categoría
     */
    async actualizarCategoria(id, data) {
        return await this.put(API_ENDPOINTS.CATEGORIA_BY_ID(id), data);
    }

    /**
     * Eliminar categoría
     */
    async eliminarCategoria(id) {
        return await this.delete(API_ENDPOINTS.CATEGORIA_BY_ID(id));
    }

    /**
     * Obtener estadísticas de categorías
     */
    async getEstadisticasCategorias() {
        return await this.get(API_ENDPOINTS.CATEGORIAS_ESTADISTICAS);
    }

    /**
     * Obtener categorías más populares
     */
    async getCategoriasMasPopulares(limit = 5) {
        return await this.get(API_ENDPOINTS.CATEGORIAS_POPULARES, { limit });
    }

    // ========================================
    // MÉTODOS ESPECÍFICOS - USUARIOS
    // ========================================

    /**
     * Login de usuario
     */
    async login(email, password) {
        return await this.post(API_ENDPOINTS.USUARIOS_LOGIN, { email, password });
    }

    /**
     * Obtener todos los usuarios
     */
    async getUsuarios() {
        return await this.get(API_ENDPOINTS.USUARIOS);
    }

    /**
     * Buscar usuarios
     */
    async buscarUsuarios(query) {
        return await this.get(API_ENDPOINTS.USUARIOS_BUSCAR, { q: query });
    }

    // ========================================
    // HEALTH CHECK
    // ========================================

    /**
     * Verificar estado de la API
     */
    async healthCheck() {
        try {
            return await this.get(API_ENDPOINTS.HEALTH);
        } catch (error) {
            return { status: 'unhealthy', error: error.message };
        }
    }
}

// Exportar instancia única (Singleton)
export const apiService = new APIService();

// Exportar clase para testing
export default APIService;