/**
 * Funciones Helper
 * Utilidades generales de la aplicación
 */

import { TOAST_DURATION, DEBOUNCE_DELAY } from './constants.js';

/**
 * Sanitizar texto para prevenir XSS
 */
export function sanitize(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Formatear fecha
 */
export function formatDate(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('es-ES', options);
}

/**
 * Formatear fecha corta
 */
export function formatDateShort(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES');
}

/**
 * Truncar texto
 */
export function truncate(text, maxLength = 100) {
    if (!text || text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

/**
 * Capitalizar primera letra
 */
export function capitalize(text) {
    if (!text) return '';
    return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
}

/**
 * Convertir a slug (URL friendly)
 */
export function slugify(text) {
    return text
        .toString()
        .toLowerCase()
        .trim()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-');
}

/**
 * Debounce function
 */
export function debounce(func, delay = DEBOUNCE_DELAY) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

/**
 * Throttle function
 */
export function throttle(func, limit) {
    let inThrottle;
    return function (...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/**
 * Copiar al portapapeles
 */
export async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        return true;
    } catch (err) {
        // Fallback para navegadores antiguos
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        const success = document.execCommand('copy');
        document.body.removeChild(textarea);
        return success;
    }
}

/**
 * Generar ID único
 */
export function generateId() {
    return Date.now().toString(36) + Math.random().toString(36).substr(2);
}

/**
 * Parsear query string
 */
export function parseQueryString(queryString) {
    const params = {};
    const pairs = (queryString[0] === '?' ? queryString.substr(1) : queryString).split('&');
    
    for (let i = 0; i < pairs.length; i++) {
        const pair = pairs[i].split('=');
        params[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1] || '');
    }
    
    return params;
}

/**
 * Crear query string desde objeto
 */
export function createQueryString(params) {
    return Object.keys(params)
        .filter(key => params[key] !== null && params[key] !== undefined && params[key] !== '')
        .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
        .join('&');
}

/**
 * Scroll suave a elemento
 */
export function scrollToElement(element, offset = 0) {
    const elementPosition = element.getBoundingClientRect().top + window.pageYOffset;
    const offsetPosition = elementPosition - offset;
    
    window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
    });
}

/**
 * Verificar si elemento está visible en viewport
 */
export function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

/**
 * Formatear número con separadores
 */
export function formatNumber(number) {
    return new Intl.NumberFormat('es-ES').format(number);
}

/**
 * Obtener color aleatorio de una paleta
 */
export function getRandomColor(colors = []) {
    if (colors.length === 0) {
        colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
    }
    return colors[Math.floor(Math.random() * colors.length)];
}

/**
 * Descargar archivo
 */
export function downloadFile(data, filename, mimeType) {
    const blob = new Blob([data], { type: mimeType });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    link.click();
    window.URL.revokeObjectURL(url);
}

/**
 * Mostrar/ocultar loading spinner
 */
export function showLoading(show = true) {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.classList.toggle('active', show);
    }
}

/**
 * Mostrar toast notification
 */
export function showToast(message, type = 'info', duration = TOAST_DURATION) {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    
    toast.innerHTML = `
        <span class="toast-icon">${icons[type] || icons.info}</span>
        <span class="toast-message">${sanitize(message)}</span>
    `;
    
    container.appendChild(toast);
    
    // Auto remove
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, duration);
    
    // Click to close
    toast.addEventListener('click', () => toast.remove());
}

/**
 * Confirmar acción
 */
export function confirm(message) {
    return window.confirm(message);
}

/**
 * Delay/Sleep function
 */
export function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

/**
 * Obtener contraste de color
 */
export function getContrastColor(hexColor) {
    // Convertir hex a RGB
    const r = parseInt(hexColor.slice(1, 3), 16);
    const g = parseInt(hexColor.slice(3, 5), 16);
    const b = parseInt(hexColor.slice(5, 7), 16);
    
    // Calcular luminancia
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    
    return luminance > 0.5 ? '#000000' : '#ffffff';
}

/**
 * Generar gradiente aleatorio
 */
export function generateGradient(color1, color2) {
    return `linear-gradient(135deg, ${color1} 0%, ${color2} 100%)`;
}

/**
 * Validar formato de fecha
 */
export function isValidDate(dateString) {
    const date = new Date(dateString);
    return date instanceof Date && !isNaN(date);
}

/**
 * Comparar dos objetos (shallow)
 */
export function isEqual(obj1, obj2) {
    const keys1 = Object.keys(obj1);
    const keys2 = Object.keys(obj2);
    
    if (keys1.length !== keys2.length) return false;
    
    return keys1.every(key => obj1[key] === obj2[key]);
}

/**
 * Clonar objeto (deep clone)
 */
export function cloneDeep(obj) {
    return JSON.parse(JSON.stringify(obj));
}

/**
 * Agrupar array por propiedad
 */
export function groupBy(array, key) {
    return array.reduce((result, item) => {
        const group = item[key];
        result[group] = result[group] || [];
        result[group].push(item);
        return result;
    }, {});
}

/**
 * Ordenar array de objetos
 */
export function sortBy(array, key, order = 'asc') {
    return [...array].sort((a, b) => {
        const aVal = a[key];
        const bVal = b[key];
        
        if (aVal < bVal) return order === 'asc' ? -1 : 1;
        if (aVal > bVal) return order === 'asc' ? 1 : -1;
        return 0;
    });
}

/**
 * Filtrar array de objetos
 */
export function filterByQuery(array, query, keys) {
    if (!query) return array;
    
    const lowerQuery = query.toLowerCase();
    
    return array.filter(item => {
        return keys.some(key => {
            const value = item[key];
            return value && value.toString().toLowerCase().includes(lowerQuery);
        });
    });
}

/**
 * Remover duplicados de array
 */
export function unique(array) {
    return [...new Set(array)];
}

/**
 * Chunk array (dividir en grupos)
 */
export function chunk(array, size) {
    const chunks = [];
    for (let i = 0; i < array.length; i += size) {
        chunks.push(array.slice(i, i + size));
    }
    return chunks;
}