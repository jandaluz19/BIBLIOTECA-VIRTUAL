-- ========================================
-- SEEDS - DATOS INICIALES
-- Biblioteca Virtual - Estadística e Informática
-- ========================================

USE biblioteca_virtual;

-- ========================================
-- INSERTAR CATEGORÍAS
-- ========================================

INSERT INTO categorias (nombre, descripcion, icono, color) VALUES
('Estadística Descriptiva', 'Análisis y presentación de datos mediante tablas, gráficos y medidas', '📊', '#3b82f6'),
('Estadística Inferencial', 'Inferencias sobre poblaciones basadas en muestras', '📈', '#8b5cf6'),
('Probabilidad', 'Teoría de probabilidades y modelos estocásticos', '🎲', '#ec4899'),
('Programación', 'Lenguajes de programación y desarrollo de software', '💻', '#10b981'),
('Bases de Datos', 'Diseño, gestión y administración de bases de datos', '🗄️', '#f59e0b'),
('Algoritmos', 'Estructuras de datos y análisis de algoritmos', '🔢', '#ef4444'),
('Inteligencia Artificial', 'Machine Learning, Deep Learning y IA', '🤖', '#6366f1'),
('Desarrollo Web', 'Tecnologías web, frontend y backend', '🌐', '#14b8a6'),
('Análisis de Datos', 'Data Science, Big Data y Analytics', '📉', '#a855f7'),
('Matemáticas Aplicadas', 'Matemáticas para ciencias e ingeniería', '🔬', '#06b6d4');

-- ========================================
-- INSERTAR LIBROS DE ESTADÍSTICA
-- ========================================

INSERT INTO libros (titulo, autor, categoria_id, anio_publicacion, isbn, editorial, descripcion, paginas, idioma, disponible, stock) VALUES
-- Estadística Descriptiva
('Estadística para Administración y Economía', 'William Mendenhall', 1, 2018, '978-0357033784', 'Cengage Learning', 'Introducción completa a la estadística aplicada a negocios y economía, con enfoque práctico y ejemplos reales.', 896, 'Español', TRUE, 3),
('Estadística Aplicada a los Negocios', 'David Anderson', 1, 2020, '978-6075266664', 'Cengage Learning', 'Métodos estadísticos para la toma de decisiones empresariales con Excel y Minitab.', 1056, 'Español', TRUE, 2),
('Estadística Descriptiva e Inferencial', 'Rufino Moya Calderón', 1, 2019, '978-6123042103', 'San Marcos', 'Texto universitario con teoría y práctica de estadística descriptiva aplicada.', 420, 'Español', TRUE, 4),

-- Estadística Inferencial
('Probabilidad y Estadística para Ingeniería', 'Ronald Walpole', 2, 2020, '978-6073239714', 'Pearson', 'Análisis completo de probabilidad y estadística inferencial con aplicaciones en ingeniería.', 816, 'Español', TRUE, 3),
('Inferencia Estadística', 'George Casella', 2, 2021, '978-8131519547', 'Cengage', 'Fundamentos teóricos y prácticos de la inferencia estadística moderna.', 660, 'Español', TRUE, 2),
('Statistical Inference', 'Roger Berger', 2, 2019, '978-0534243128', 'Duxbury Press', 'Texto avanzado sobre teoría de la estimación y pruebas de hipótesis.', 660, 'Inglés', TRUE, 2),

-- Probabilidad
('Introduction to Probability', 'Dimitri Bertsekas', 3, 2022, '978-1886529236', 'Athena Scientific', 'Curso completo de probabilidad con énfasis en problemas y aplicaciones.', 544, 'Inglés', TRUE, 2),
('Probabilidad y Estadística', 'Murray Spiegel', 3, 2018, '978-6071513229', 'McGraw-Hill', 'Más de 1000 problemas resueltos de probabilidad y estadística.', 432, 'Español', TRUE, 5),
('A First Course in Probability', 'Sheldon Ross', 3, 2021, '978-0134753119', 'Pearson', 'Introducción rigurosa a la teoría de probabilidades con aplicaciones.', 552, 'Inglés', TRUE, 3);

-- ========================================
-- INSERTAR LIBROS DE INFORMÁTICA
-- ========================================

INSERT INTO libros (titulo, autor, categoria_id, anio_publicacion, isbn, editorial, descripcion, paginas, idioma, disponible, stock) VALUES
-- Programación
('Clean Code', 'Robert C. Martin', 4, 2019, '978-0132350884', 'Prentice Hall', 'Guía para escribir código limpio, mantenible y profesional con principios SOLID.', 464, 'Inglés', TRUE, 4),
('JavaScript: The Definitive Guide', 'David Flanagan', 4, 2020, '978-1491952023', 'O''Reilly', 'La guía completa y definitiva para dominar JavaScript moderno.', 706, 'Inglés', TRUE, 3),
('Python Crash Course', 'Eric Matthes', 4, 2023, '978-1718502703', 'No Starch Press', 'Introducción práctica a Python con proyectos hands-on.', 552, 'Inglés', TRUE, 5),
('Eloquent JavaScript', 'Marijn Haverbeke', 4, 2018, '978-1593279509', 'No Starch Press', 'Introducción moderna a JavaScript, programación y maravillas digitales.', 472, 'Inglés', TRUE, 3),

-- Bases de Datos
('Database System Concepts', 'Abraham Silberschatz', 5, 2019, '978-0078022159', 'McGraw-Hill', 'Fundamentos completos de sistemas de bases de datos relacionales y NoSQL.', 1376, 'Inglés', TRUE, 3),
('SQL in 10 Minutes', 'Ben Forta', 5, 2020, '978-0135182796', 'Sams', 'Guía rápida y práctica para aprender SQL desde cero.', 288, 'Inglés', TRUE, 4),
('Diseño de Bases de Datos', 'Carlos Coronel', 5, 2018, '978-6075193823', 'Cengage', 'Modelado, diseño e implementación de bases de datos profesionales.', 752, 'Español', TRUE, 2),

-- Algoritmos
('Introduction to Algorithms', 'Thomas Cormen', 6, 2022, '978-0262046305', 'MIT Press', 'El texto más completo sobre algoritmos, estructuras de datos y complejidad computacional.', 1312, 'Inglés', TRUE, 2),
('Algorithms', 'Robert Sedgewick', 6, 2021, '978-0321573513', 'Addison-Wesley', 'Algoritmos y estructuras de datos en Java con análisis de performance.', 976, 'Inglés', TRUE, 3),
('Grokking Algorithms', 'Aditya Bhargava', 6, 2019, '978-1617292231', 'Manning', 'Guía ilustrada y amigable para entender algoritmos complejos.', 256, 'Inglés', TRUE, 5),

-- Inteligencia Artificial
('Artificial Intelligence: A Modern Approach', 'Stuart Russell', 7, 2021, '978-0134610993', 'Pearson', 'El texto más completo y actualizado sobre inteligencia artificial.', 1136, 'Inglés', TRUE, 2),
('Deep Learning', 'Ian Goodfellow', 7, 2020, '978-0262035613', 'MIT Press', 'Fundamentos matemáticos y prácticos del deep learning.', 800, 'Inglés', TRUE, 2),
('Hands-On Machine Learning', 'Aurélien Géron', 7, 2022, '978-1492032649', 'O''Reilly', 'Guía práctica de ML con Scikit-Learn, Keras y TensorFlow.', 856, 'Inglés', TRUE, 4),

-- Desarrollo Web
('HTML and CSS: Design and Build Websites', 'Jon Duckett', 8, 2018, '978-1118008189', 'Wiley', 'Introducción visual y moderna a HTML5 y CSS3.', 512, 'Inglés', TRUE, 4),
('Learning React', 'Alex Banks', 8, 2020, '978-1492051725', 'O''Reilly', 'Guía moderna para construir aplicaciones web con React.', 310, 'Inglés', TRUE, 3),
('Node.js Design Patterns', 'Mario Casciaro', 8, 2020, '978-1839214110', 'Packt', 'Patrones de diseño y mejores prácticas para Node.js.', 660, 'Inglés', TRUE, 2),

-- Análisis de Datos
('Python for Data Analysis', 'Wes McKinney', 9, 2022, '978-1491957660', 'O''Reilly', 'Manipulación de datos con Pandas, NumPy y Jupyter.', 550, 'Inglés', TRUE, 4),
('Data Science from Scratch', 'Joel Grus', 9, 2019, '978-1492041139', 'O''Reilly', 'Fundamentos de data science implementados desde cero en Python.', 406, 'Inglés', TRUE, 3),
('The Data Warehouse Toolkit', 'Ralph Kimball', 9, 2020, '978-1118530801', 'Wiley', 'Guía definitiva para diseñar data warehouses dimensionales.', 600, 'Inglés', TRUE, 2);

-- ========================================
-- INSERTAR USUARIOS DE PRUEBA
-- ========================================

INSERT INTO usuarios (nombre, email, password, telefono, tipo, activo) VALUES
('Administrador Sistema', 'admin@biblioteca.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '999888777', 'admin', TRUE),
('María García', 'maria.garcia@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '987654321', 'bibliotecario', TRUE),
('Juan Pérez', 'juan.perez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '987654322', 'usuario', TRUE),
('Ana López', 'ana.lopez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '987654323', 'usuario', TRUE),
('Carlos Rodríguez', 'carlos.rodriguez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '987654324', 'usuario', TRUE);

-- Nota: El password hasheado corresponde a "password123" 
-- En producción, usar contraseñas seguras y hashear con password_hash() en PHP

-- ========================================
-- INSERTAR PRÉSTAMOS DE EJEMPLO
-- ========================================

INSERT INTO prestamos (libro_id, usuario_id, fecha_prestamo, fecha_devolucion_esperada, estado) VALUES
(1, 3, '2025-01-02', '2025-01-16', 'activo'),
(5, 4, '2024-12-28', '2025-01-11', 'activo'),
(10, 5, '2025-01-03', '2025-01-17', 'activo'),
(3, 3, '2024-12-20', '2025-01-03', 'devuelto'),
(7, 4, '2024-12-15', '2024-12-29', 'devuelto');

-- ========================================
-- INSERTAR VALORACIONES
-- ========================================

INSERT INTO valoraciones (libro_id, usuario_id, calificacion, comentario) VALUES
(1, 3, 5, 'Excelente libro para aprender estadística aplicada a negocios.'),
(10, 4, 5, 'La mejor introducción a algoritmos que he leído. Muy clara.'),
(13, 5, 4, 'Muy bueno para comenzar con Python, ejemplos prácticos.'),
(5, 3, 5, 'Fundamental para cualquier estudiante de estadística inferencial.'),
(20, 4, 5, 'Imprescindible para entender IA moderna. Muy completo.');

-- ========================================
-- ACTUALIZAR CONTADORES
-- ========================================

-- Actualizar veces_prestado basado en préstamos existentes
UPDATE libros l
SET veces_prestado = (
    SELECT COUNT(*) FROM prestamos WHERE libro_id = l.id
);

-- Actualizar calificaciones promedio
UPDATE libros l
SET calificacion = (
    SELECT COALESCE(AVG(calificacion), 0) 
    FROM valoraciones 
    WHERE libro_id = l.id
);