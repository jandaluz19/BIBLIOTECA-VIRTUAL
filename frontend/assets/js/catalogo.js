const catalogo = document.getElementById('catalogoLibros');

fetch('http://localhost/BIBLIOTECA-VIRTUAL/backend/api/libros')
    .then(res => res.json())
    .then(data => {
        data.data.libros.forEach(libro => {
            catalogo.innerHTML += `
                <div class="libro">
                    <img src="${libro.portada_url}" alt="${libro.titulo}">
                    <h3>${libro.titulo}</h3>
                    <a href="ver-libro.html?id=${libro.id}">📖 Ver libro</a>
                </div>
            `;
        });
    });
