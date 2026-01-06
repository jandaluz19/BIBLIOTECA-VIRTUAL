/**
 * Constantes Globales
 * Configuración y valores constantes de la aplicación
 */

// URL base de la API
export const API_BASE_URL = 'http://localhost/BIBLIOTECA-VIRTUAL/backend';

// Endpoints de la API
export const API_ENDPOINTS = {
    // Libros
    LIBROS: '/api/libros',
    LIBRO_BY_ID: (id) => `/api/libros/${id}`,
    LIBROS_BUSCAR: '/api/libros/buscar',
    LIBROS_MAS_PRESTADOS: '/api/libros/mas-prestados',
    LIBROS_MEJOR_CALIFICADOS: '/api/libros/mejor-calificados',
    LIBROS_RECIENTES: '/api/libros/recientes',
    LIBROS_ESTADISTICAS: '/api/libros/estadisticas',
    LIBRO_DISPONIBILIDAD: (id) => `/api/libros/${id}/disponibilidad`,
    
    // Categorías
    CATEGORIAS: '/api/categorias',
    CATEGORIA_BY_ID: (id) => `/api/categorias/${id}`,
    CATEGORIAS_ESTADISTICAS: '/api/categorias/estadisticas',
    CATEGORIAS_POPULARES: '/api/categorias/populares',
    
    // Usuarios
    USUARIOS: '/api/usuarios',
    USUARIO_BY_ID: (id) => `/api/usuarios/${id}`,
    USUARIOS_LOGIN: '/api/usuarios/login',
    USUARIOS_BUSCAR: '/api/usuarios/buscar',
    
    // Health check
    HEALTH: '/api/health'
};

// Configuración de paginación
export const PAGINATION = {
    ITEMS_PER_PAGE: 12,
    MAX_PAGES_SHOWN: 5
};

// Mensajes de la aplicación
export const MESSAGES = {
    SUCCESS: {
        LIBRO_CREADO: 'Libro creado exitosamente',
        LIBRO_ACTUALIZADO: 'Libro actualizado exitosamente',
        LIBRO_ELIMINADO: 'Libro eliminado exitosamente',
        CATEGORIA_CREADA: 'Categoría creada exitosamente',
        CATEGORIA_ACTUALIZADA: 'Categoría actualizada exitosamente',
        CATEGORIA_ELIMINADA: 'Categoría eliminada exitosamente'
    },
    ERROR: {
        CARGA_LIBROS: 'Error al cargar los libros',
        CARGA_CATEGORIAS: 'Error al cargar las categorías',
        CREAR_LIBRO: 'Error al crear el libro',
        ACTUALIZAR_LIBRO: 'Error al actualizar el libro',
        ELIMINAR_LIBRO: 'Error al eliminar el libro',
        CONEXION: 'Error de conexión con el servidor',
        DATOS_INVALIDOS: 'Por favor verifica los datos ingresados'
    },
    INFO: {
        SIN_RESULTADOS: 'No se encontraron resultados',
        CARGANDO: 'Cargando...'
    }
};

// Iconos por categoría (emoji)
export const CATEGORY_ICONS = {
    'Estadística Descriptiva': '📊',
    'Estadística Inferencial': '📈',
    'Probabilidad': '🎲',
    'Programación': '💻',
    'Bases de Datos': '🗄️',
    'Algoritmos': '🔢',
    'Inteligencia Artificial': '🤖',
    'Desarrollo Web': '🌐',
    'Análisis de Datos': '📉',
    'Matemáticas Aplicadas': '🔬',
    'default': '📚'
};

// Colores por categoría
export const CATEGORY_COLORS = {
    'Estadística Descriptiva': '#3b82f6',
    'Estadística Inferencial': '#8b5cf6',
    'Probabilidad': '#ec4899',
    'Programación': '#10b981',
    'Bases de Datos': '#f59e0b',
    'Algoritmos': '#ef4444',
    'Inteligencia Artificial': '#6366f1',
    'Desarrollo Web': '#14b8a6',
    'Análisis de Datos': '#a855f7',
    'Matemáticas Aplicadas': '#06b6d4',
    'default': '#2563eb'
};

// Tipos de ordenamiento
export const SORT_OPTIONS = {
    TITULO: 'titulo',
    AUTOR: 'autor',
    ANIO: 'anio',
    CALIFICACION: 'calificacion',
    MAS_PRESTADOS: 'mas_prestados'
};

// Duración de notificaciones (ms)
export const TOAST_DURATION = 3000;

// Idiomas disponibles
export const IDIOMAS = [
    'Español',
    'Inglés',
    'Portugués',
    'Francés',
    'Alemán',
    'Italiano',
    'Chino',
    'Japonés'
];

// Año actual y rango válido
export const CURRENT_YEAR = new Date().getFullYear();
export const MIN_YEAR = 1900;

// Expresiones regulares
export const REGEX = {
    ISBN_10: /^[0-9]{9}[0-9X]$/,
    ISBN_13: /^[0-9]{13}$/,
    EMAIL: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    PHONE: /^[0-9]{9,15}$/
};

// Storage keys (LocalStorage)
export const STORAGE_KEYS = {
    USER: 'biblioteca_user',
    TOKEN: 'biblioteca_token',
    FILTERS: 'biblioteca_filters',
    THEME: 'biblioteca_theme'
};

// Configuración de debounce (ms)
export const DEBOUNCE_DELAY = 300;

// Estados de disponibilidad
export const DISPONIBILIDAD = {
    DISPONIBLE: true,
    NO_DISPONIBLE: false
};

// Roles de usuario
export const USER_ROLES = {
    ADMIN: 'admin',
    BIBLIOTECARIO: 'bibliotecario',
    USUARIO: 'usuario'
};

// Configuración de la aplicación
export const APP_CONFIG = {
    NAME: 'Biblioteca Virtual',
    VERSION: '1.0.0',
    DESCRIPTION: 'Sistema de gestión de biblioteca de Estadística e Informática'
};