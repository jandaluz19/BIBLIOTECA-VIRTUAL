/**
 * Vista de Modales
 * Manejo de modales de libros y categorías
 */

import { showValidationErrors, clearValidationErrors } from '../utils/validators.js';

class ModalView {
    constructor() {
        this.modalLibro = document.getElementById('modalLibro');
        this.modalCategoria = document.getElementById('modalCategoria');
        this.formLibro = document.getElementById('formLibro');
        this.formCategoria = document.getElementById('formCategoria');
        
        this.setupEventListeners();
    }

    /**
     * Configurar event listeners
     */
    setupEventListeners() {
        // Cerrar modales con botón X
        document.getElementById('closeModalLibro')?.addEventListener('click', () => {
            this.cerrarModalLibro();
        });

        document.getElementById('closeModalCategoria')?.addEventListener('click', () => {
            this.cerrarModalCategoria();
        });

        // Cerrar modales al hacer click fuera
        this.modalLibro?.addEventListener('click', (e) => {
            if (e.target === this.modalLibro) {
                this.cerrarModalLibro();
            }
        });

        this.modalCategoria?.addEventListener('click', (e) => {
            if (e.target === this.modalCategoria) {
                this.cerrarModalCategoria();
            }
        });

        // Botones de cancelar
        document.getElementById('btnCancelarLibro')?.addEventListener('click', () => {
            this.cerrarModalLibro();
        });

        document.getElementById('btnCancelarCategoria')?.addEventListener('click', () => {
            this.cerrarModalCategoria();
        });

        // Cerrar con tecla ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.cerrarModalLibro();
                this.cerrarModalCategoria();
            }
        });
    }

    /**
     * Abrir modal de libro (crear)
     */
    abrirModalLibro(categorias = []) {
        document.getElementById('modalLibroTitle').textContent = 'Agregar Nuevo Libro';
        document.getElementById('btnGuardarTexto').textContent = 'Guardar Libro';
        
        this.formLibro.reset();
        document.getElementById('libroId').value = '';
        
        this.cargarCategorias(categorias);
        clearValidationErrors('formLibro');
        
        this.modalLibro.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Abrir modal de libro (editar)
     */
    abrirModalLibroEditar(libro, categorias = []) {
        document.getElementById('modalLibroTitle').textContent = 'Editar Libro';
        document.getElementById('btnGuardarTexto').textContent = 'Actualizar Libro';
        
        // Llenar formulario con datos del libro
        document.getElementById('libroId').value = libro.id;
        document.getElementById('libroTitulo').value = libro.titulo;
        document.getElementById('libroAutor').value = libro.autor;
        document.getElementById('libroCategoria').value = libro.categoria_id;
        document.getElementById('libroAnio').value = libro.anio_publicacion;
        document.getElementById('libroISBN').value = libro.isbn || '';
        document.getElementById('libroEditorial').value = libro.editorial || '';
        document.getElementById('libroPaginas').value = libro.paginas || '';
        document.getElementById('libroIdioma').value = libro.idioma || 'Español';
        document.getElementById('libroDescripcion').value = libro.descripcion || '';
        document.getElementById('libroStock').value = libro.stock || 1;
        document.getElementById('libroDisponible').checked = libro.disponible;
        
        this.cargarCategorias(categorias);
        clearValidationErrors('formLibro');
        
        this.modalLibro.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Cerrar modal de libro
     */
    cerrarModalLibro() {
        this.modalLibro.classList.remove('active');
        this.formLibro.reset();
        clearValidationErrors('formLibro');
        document.body.style.overflow = '';
    }

    /**
     * Abrir modal de categoría (crear)
     */
    abrirModalCategoria() {
        document.getElementById('modalCategoriaTitle').textContent = 'Nueva Categoría';
        this.formCategoria.reset();
        document.getElementById('categoriaId').value = '';
        clearValidationErrors('formCategoria');
        
        this.modalCategoria.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Abrir modal de categoría (editar)
     */
    abrirModalCategoriaEditar(categoria) {
        document.getElementById('modalCategoriaTitle').textContent = 'Editar Categoría';
        
        document.getElementById('categoriaId').value = categoria.id;
        document.getElementById('categoriaNombre').value = categoria.nombre;
        document.getElementById('categoriaDescripcion').value = categoria.descripcion || '';
        document.getElementById('categoriaIcono').value = categoria.icono || '📚';
        document.getElementById('categoriaColor').value = categoria.color || '#2563eb';
        
        clearValidationErrors('formCategoria');
        
        this.modalCategoria.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Cerrar modal de categoría
     */
    cerrarModalCategoria() {
        this.modalCategoria.classList.remove('active');
        this.formCategoria.reset();
        clearValidationErrors('formCategoria');
        document.body.style.overflow = '';
    }

    /**
     * Cargar opciones de categorías en select
     */
    cargarCategorias(categorias) {
        const select = document.getElementById('libroCategoria');
        if (!select) return;

        select.innerHTML = '<option value="">Seleccionar categoría</option>' +
            categorias.map(cat => 
                `<option value="${cat.id}">${cat.nombre}</option>`
            ).join('');
    }

    /**
     * Obtener datos del formulario de libro
     */
    getFormLibroData() {
        return {
            id: document.getElementById('libroId').value,
            titulo: document.getElementById('libroTitulo').value.trim(),
            autor: document.getElementById('libroAutor').value.trim(),
            categoria_id: parseInt(document.getElementById('libroCategoria').value),
            anio_publicacion: parseInt(document.getElementById('libroAnio').value),
            isbn: document.getElementById('libroISBN').value.trim(),
            editorial: document.getElementById('libroEditorial').value.trim(),
            paginas: document.getElementById('libroPaginas').value ? parseInt(document.getElementById('libroPaginas').value) : null,
            idioma: document.getElementById('libroIdioma').value,
            descripcion: document.getElementById('libroDescripcion').value.trim(),
            stock: parseInt(document.getElementById('libroStock').value) || 1,
            disponible: document.getElementById('libroDisponible').checked
        };
    }

    /**
     * Obtener datos del formulario de categoría
     */
    getFormCategoriaData() {
        return {
            id: document.getElementById('categoriaId').value,
            nombre: document.getElementById('categoriaNombre').value.trim(),
            descripcion: document.getElementById('categoriaDescripcion').value.trim(),
            icono: document.getElementById('categoriaIcono').value.trim() || '📚',
            color: document.getElementById('categoriaColor').value || '#2563eb',
            activo: true
        };
    }

    /**
     * Mostrar errores de validación
     */
    mostrarErrores(errors, formId) {
        showValidationErrors(errors, formId);
    }

    /**
     * Limpiar errores
     */
    limpiarErrores(formId) {
        clearValidationErrors(formId);
    }
}

// Exportar instancia única
export const modalView = new ModalView();

export default ModalView;