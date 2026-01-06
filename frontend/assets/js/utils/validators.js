/**
 * Validadores
 * Funciones de validación de datos
 */

import { REGEX, CURRENT_YEAR, MIN_YEAR } from './constants.js';

/**
 * Validar libro
 */
export function validateLibro(data) {
    const errors = {};
    
    // Título
    if (!data.titulo || data.titulo.trim() === '') {
        errors.titulo = 'El título es requerido';
    } else if (data.titulo.length > 255) {
        errors.titulo = 'El título no puede exceder 255 caracteres';
    }
    
    // Autor
    if (!data.autor || data.autor.trim() === '') {
        errors.autor = 'El autor es requerido';
    } else if (data.autor.length > 255) {
        errors.autor = 'El autor no puede exceder 255 caracteres';
    }
    
    // Categoría
    if (!data.categoria_id) {
        errors.categoria_id = 'La categoría es requerida';
    }
    
    // Año de publicación
    if (!data.anio_publicacion) {
        errors.anio_publicacion = 'El año de publicación es requerido';
    } else {
        const year = parseInt(data.anio_publicacion);
        if (isNaN(year) || year < MIN_YEAR || year > CURRENT_YEAR) {
            errors.anio_publicacion = `El año debe estar entre ${MIN_YEAR} y ${CURRENT_YEAR}`;
        }
    }
    
    // ISBN (opcional, pero si existe debe ser válido)
    if (data.isbn && data.isbn.trim() !== '') {
        const isbnClean = data.isbn.replace(/[^0-9X]/gi, '');
        if (!REGEX.ISBN_10.test(isbnClean) && !REGEX.ISBN_13.test(isbnClean)) {
            errors.isbn = 'Formato de ISBN inválido (debe ser ISBN-10 o ISBN-13)';
        }
    }
    
    // Páginas (opcional, pero debe ser positivo)
    if (data.paginas) {
        const pages = parseInt(data.paginas);
        if (isNaN(pages) || pages <= 0) {
            errors.paginas = 'El número de páginas debe ser mayor a 0';
        }
    }
    
    // Stock
    if (data.stock !== undefined) {
        const stock = parseInt(data.stock);
        if (isNaN(stock) || stock < 0) {
            errors.stock = 'El stock no puede ser negativo';
        }
    }
    
    return {
        isValid: Object.keys(errors).length === 0,
        errors
    };
}

/**
 * Validar categoría
 */
export function validateCategoria(data) {
    const errors = {};
    
    // Nombre
    if (!data.nombre || data.nombre.trim() === '') {
        errors.nombre = 'El nombre es requerido';
    } else if (data.nombre.length > 100) {
        errors.nombre = 'El nombre no puede exceder 100 caracteres';
    }
    
    // Color (opcional, pero debe ser hexadecimal válido)
    if (data.color && !/^#[0-9A-F]{6}$/i.test(data.color)) {
        errors.color = 'El color debe ser un valor hexadecimal válido (ej: #FF5733)';
    }
    
    return {
        isValid: Object.keys(errors).length === 0,
        errors
    };
}

/**
 * Validar usuario
 */
export function validateUsuario(data) {
    const errors = {};
    
    // Nombre
    if (!data.nombre || data.nombre.trim() === '') {
        errors.nombre = 'El nombre es requerido';
    } else if (data.nombre.length < 3) {
        errors.nombre = 'El nombre debe tener al menos 3 caracteres';
    }
    
    // Email
    if (!data.email || data.email.trim() === '') {
        errors.email = 'El email es requerido';
    } else if (!REGEX.EMAIL.test(data.email)) {
        errors.email = 'Formato de email inválido';
    }
    
    // Password (solo para creación)
    if (data.password !== undefined) {
        if (!data.password || data.password.trim() === '') {
            errors.password = 'La contraseña es requerida';
        } else if (data.password.length < 8) {
            errors.password = 'La contraseña debe tener al menos 8 caracteres';
        }
    }
    
    // Teléfono (opcional)
    if (data.telefono && !REGEX.PHONE.test(data.telefono.replace(/\s/g, ''))) {
        errors.telefono = 'Formato de teléfono inválido';
    }
    
    return {
        isValid: Object.keys(errors).length === 0,
        errors
    };
}

/**
 * Validar email
 */
export function isValidEmail(email) {
    return REGEX.EMAIL.test(email);
}

/**
 * Validar ISBN
 */
export function isValidISBN(isbn) {
    const clean = isbn.replace(/[^0-9X]/gi, '');
    return REGEX.ISBN_10.test(clean) || REGEX.ISBN_13.test(clean);
}

/**
 * Validar año
 */
export function isValidYear(year) {
    const y = parseInt(year);
    return !isNaN(y) && y >= MIN_YEAR && y <= CURRENT_YEAR;
}

/**
 * Validar teléfono
 */
export function isValidPhone(phone) {
    return REGEX.PHONE.test(phone.replace(/\s/g, ''));
}

/**
 * Validar URL
 */
export function isValidURL(url) {
    try {
        new URL(url);
        return true;
    } catch {
        return false;
    }
}

/**
 * Validar número positivo
 */
export function isPositiveNumber(value) {
    const num = parseFloat(value);
    return !isNaN(num) && num > 0;
}

/**
 * Validar rango de números
 */
export function isInRange(value, min, max) {
    const num = parseFloat(value);
    return !isNaN(num) && num >= min && num <= max;
}

/**
 * Validar string no vacío
 */
export function isNotEmpty(value) {
    return value !== null && value !== undefined && value.trim() !== '';
}

/**
 * Validar longitud de string
 */
export function hasValidLength(value, min, max) {
    if (!value) return false;
    const length = value.length;
    return length >= min && length <= max;
}

/**
 * Validar formato de color hexadecimal
 */
export function isValidHexColor(color) {
    return /^#[0-9A-F]{6}$/i.test(color);
}

/**
 * Sanitizar input para prevenir XSS
 */
export function sanitizeInput(input) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#x27;',
        "/": '&#x2F;',
    };
    const reg = /[&<>"'/]/ig;
    return input.replace(reg, (match) => map[match]);
}

/**
 * Validar archivo (tipo y tamaño)
 */
export function validateFile(file, allowedTypes, maxSize) {
    const errors = [];
    
    // Validar tipo
    if (allowedTypes && !allowedTypes.includes(file.type)) {
        errors.push(`Tipo de archivo no permitido. Permitidos: ${allowedTypes.join(', ')}`);
    }
    
    // Validar tamaño (en bytes)
    if (maxSize && file.size > maxSize) {
        const maxSizeMB = (maxSize / (1024 * 1024)).toFixed(2);
        errors.push(`El archivo excede el tamaño máximo de ${maxSizeMB}MB`);
    }
    
    return {
        isValid: errors.length === 0,
        errors
    };
}

/**
 * Validar formulario completo
 */
export function validateForm(formElement) {
    const inputs = formElement.querySelectorAll('input[required], select[required], textarea[required]');
    const errors = [];
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            errors.push(`El campo ${input.name || input.id} es requerido`);
        }
    });
    
    return {
        isValid: errors.length === 0,
        errors
    };
}

/**
 * Mostrar errores de validación en el DOM
 */
export function showValidationErrors(errors, formId) {
    // Limpiar errores previos
    const form = document.getElementById(formId);
    if (!form) return;
    
    form.querySelectorAll('.error-message').forEach(el => el.remove());
    form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
    
    // Mostrar nuevos errores
    Object.keys(errors).forEach(fieldName => {
        const field = form.querySelector(`#${fieldName}`);
        if (field) {
            field.classList.add('error');
            
            const errorMsg = document.createElement('span');
            errorMsg.className = 'error-message';
            errorMsg.textContent = errors[fieldName];
            errorMsg.style.color = '#ef4444';
            errorMsg.style.fontSize = '0.875rem';
            errorMsg.style.marginTop = '0.25rem';
            
            field.parentElement.appendChild(errorMsg);
        }
    });
}

/**
 * Limpiar errores de validación
 */
export function clearValidationErrors(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    form.querySelectorAll('.error-message').forEach(el => el.remove());
    form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
}