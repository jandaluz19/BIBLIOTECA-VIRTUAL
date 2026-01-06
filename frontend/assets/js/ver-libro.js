const params = new URLSearchParams(window.location.search);
const libroId = params.get('id');

fetch(`http://localhost/BIBLIOTECA-VIRTUAL/backend/api/libros/${libroId}`)
    .then(res => res.json())
    .then(data => {
        const pdfUrl = data.data.archivo_pdf;

        document.getElementById('visorPDF').src = pdfUrl;
        document.getElementById('descargar').href = pdfUrl;
    });
