/**
 * Servicio de Storage
 * Manejo de LocalStorage con métodos seguros
 */

import { STORAGE_KEYS } from '../utils/constants.js';

class StorageService {
    /**
     * Guardar dato en LocalStorage
     */
    set(key, value) {
        try {
            const serialized = JSON.stringify(value);
            localStorage.setItem(key, serialized);
            return true;
        } catch (error) {
            console.error('Error saving to localStorage:', error);
            return false;
        }
    }

    /**
     * Obtener dato de LocalStorage
     */
    get(key, defaultValue = null) {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (error) {
            console.error('Error reading from localStorage:', error);
            return defaultValue;
        }
    }

    /**
     * Eliminar dato de LocalStorage
     */
    remove(key) {
        try {
            localStorage.removeItem(key);
            return true;
        } catch (error) {
            console.error('Error removing from localStorage:', error);
            return false;
        }
    }

    /**
     * Limpiar todo el LocalStorage
     */
    clear() {
        try {
            localStorage.clear();
            return true;
        } catch (error) {
            console.error('Error clearing localStorage:', error);
            return false;
        }
    }

    /**
     * Verificar si existe una clave
     */
    has(key) {
        return localStorage.getItem(key) !== null;
    }

    /**
     * Obtener todas las claves
     */
    keys() {
        return Object.keys(localStorage);
    }

    /**
     * Obtener tamaño usado en bytes (aproximado)
     */
    getSize() {
        let size = 0;
        for (let key in localStorage) {
            if (localStorage.hasOwnProperty(key)) {
                size += localStorage[key].length + key.length;
            }
        }
        return size;
    }

    // ========================================
    // MÉTODOS ESPECÍFICOS DE LA APP
    // ========================================

    /**
     * Guardar usuario
     */
    setUser(user) {
        return this.set(STORAGE_KEYS.USER, user);
    }

    /**
     * Obtener usuario
     */
    getUser() {
        return this.get(STORAGE_KEYS.USER);
    }

    /**
     * Eliminar usuario
     */
    removeUser() {
        return this.remove(STORAGE_KEYS.USER);
    }

    /**
     * Guardar token
     */
    setToken(token) {
        return this.set(STORAGE_KEYS.TOKEN, token);
    }

    /**
     * Obtener token
     */
    getToken() {
        return this.get(STORAGE_KEYS.TOKEN);
    }

    /**
     * Eliminar token
     */
    removeToken() {
        return this.remove(STORAGE_KEYS.TOKEN);
    }

    /**
     * Verificar si hay sesión activa
     */
    isAuthenticated() {
        return this.has(STORAGE_KEYS.TOKEN) && this.has(STORAGE_KEYS.USER);
    }

    /**
     * Cerrar sesión
     */
    logout() {
        this.removeUser();
        this.removeToken();
    }

    /**
     * Guardar filtros de búsqueda
     */
    setFilters(filters) {
        return this.set(STORAGE_KEYS.FILTERS, filters);
    }

    /**
     * Obtener filtros de búsqueda
     */
    getFilters() {
        return this.get(STORAGE_KEYS.FILTERS, {});
    }

    /**
     * Guardar tema
     */
    setTheme(theme) {
        return this.set(STORAGE_KEYS.THEME, theme);
    }

    /**
     * Obtener tema
     */
    getTheme() {
        return this.get(STORAGE_KEYS.THEME, 'light');
    }
}

// Exportar instancia única (Singleton)
export const storageService = new StorageService();

// Exportar clase para testing
export default StorageService;