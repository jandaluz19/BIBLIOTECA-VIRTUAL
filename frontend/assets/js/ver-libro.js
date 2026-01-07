const params = new URLSearchParams(window.location.search);
const libroId = params.get('id');

const titulo = document.getElementById('titulo');
const autor = document.getElementById('autor');
const visor = document.getElementById('visorPDF');
const descargar = document.getElementById('descargar');

const token = localStorage.getItem('token');

if (!libroId) {
    alert('Libro no encontrado');
    history.back();
}

async function cargarLibro() {
    try {
        const response = await fetch(`/BIBLIOTECA-VIRTUAL/backend/api/libros/${libroId}`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message);
        }

        const libro = result.data;

        titulo.textContent = libro.titulo;
        autor.textContent = `Autor: ${libro.autor}`;

        const pdfUrl = `/BIBLIOTECA-VIRTUAL/backend/uploads/${libro.archivo}`;

        visor.src = pdfUrl;
        descargar.href = pdfUrl;

    } catch (error) {
        console.error(error);
        alert('Error al cargar el libro');
    }
}

cargarLibro();
