-- ========================================
-- SCHEMA - BIBLIOTECA VIRTUAL
-- Base de datos para gestión de libros
-- ========================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS biblioteca_virtual
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE biblioteca_virtual;

-- ========================================
-- TABLA: categorias
-- ========================================
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    icono VARCHAR(50) DEFAULT '📚',
    color VARCHAR(7) DEFAULT '#2563eb',
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: libros
-- ========================================
CREATE TABLE IF NOT EXISTS libros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    autor VARCHAR(255) NOT NULL,
    categoria_id INT NOT NULL,
    anio_publicacion INT NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    editorial VARCHAR(255),
    descripcion TEXT,
    paginas INT,
    idioma VARCHAR(50) DEFAULT 'Español',
    portada_url VARCHAR(500),
    archivo_pdf VARCHAR(500),
    disponible BOOLEAN DEFAULT TRUE,
    stock INT DEFAULT 1,
    veces_prestado INT DEFAULT 0,
    calificacion DECIMAL(3,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT,
    
    INDEX idx_titulo (titulo),
    INDEX idx_autor (autor),
    INDEX idx_categoria (categoria_id),
    INDEX idx_isbn (isbn),
    INDEX idx_disponible (disponible),
    
    FULLTEXT INDEX ft_busqueda (titulo, autor, descripcion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ========================================
-- TABLA: cambiar contraseña
-- ========================================
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX(email),
    INDEX(token)
);

-- ========================================
-- TABLA: usuarios
-- ========================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    direccion TEXT,
    tipo ENUM('admin', 'bibliotecario', 'usuario') DEFAULT 'usuario',
    activo BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    avatar_url VARCHAR(500),
    
    INDEX idx_email (email),
    INDEX idx_tipo (tipo),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: recuperar contraseña
-- ========================================
CREATE TABLE recuperaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expira_en DATETIME NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- ========================================
-- TABLA: prestamos
-- ========================================
CREATE TABLE IF NOT EXISTS prestamos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libro_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_prestamo DATE NOT NULL,
    fecha_devolucion_esperada DATE NOT NULL,
    fecha_devolucion_real DATE NULL,
    estado ENUM('activo', 'devuelto', 'atrasado', 'perdido') DEFAULT 'activo',
    observaciones TEXT,
    multa DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (libro_id) REFERENCES libros(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    
    INDEX idx_libro (libro_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_estado (estado),
    INDEX idx_fechas (fecha_prestamo, fecha_devolucion_esperada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: valoraciones
-- ========================================
CREATE TABLE IF NOT EXISTS valoraciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libro_id INT NOT NULL,
    usuario_id INT NOT NULL,
    calificacion INT NOT NULL CHECK (calificacion BETWEEN 1 AND 5),
    comentario TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (libro_id) REFERENCES libros(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_valoracion (libro_id, usuario_id),
    INDEX idx_libro (libro_id),
    INDEX idx_calificacion (calificacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: historial_acciones
-- ========================================
CREATE TABLE IF NOT EXISTS historial_acciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(100) NOT NULL,
    tabla_afectada VARCHAR(50),
    registro_id INT,
    descripcion TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    
    INDEX idx_usuario (usuario_id),
    INDEX idx_accion (accion),
    INDEX idx_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- VISTAS ÚTILES
-- ========================================

-- Vista: Libros con información completa
CREATE OR REPLACE VIEW vista_libros_completa AS
SELECT 
    l.id,
    l.titulo,
    l.autor,
    l.anio_publicacion,
    l.isbn,
    l.editorial,
    l.descripcion,
    l.paginas,
    l.idioma,
    l.disponible,
    l.stock,
    l.veces_prestado,
    l.calificacion,
    c.nombre AS categoria,
    c.color AS categoria_color,
    c.icono AS categoria_icono,
    (SELECT COUNT(*) FROM prestamos WHERE libro_id = l.id AND estado = 'activo') AS prestamos_activos,
    l.created_at,
    l.updated_at
FROM libros l
INNER JOIN categorias c ON l.categoria_id = c.id;

-- Vista: Préstamos activos
CREATE OR REPLACE VIEW vista_prestamos_activos AS
SELECT 
    p.id,
    p.fecha_prestamo,
    p.fecha_devolucion_esperada,
    DATEDIFF(CURRENT_DATE, p.fecha_devolucion_esperada) AS dias_atraso,
    l.titulo AS libro_titulo,
    l.autor AS libro_autor,
    u.nombre AS usuario_nombre,
    u.email AS usuario_email,
    u.telefono AS usuario_telefono,
    p.estado,
    p.multa
FROM prestamos p
INNER JOIN libros l ON p.libro_id = l.id
INNER JOIN usuarios u ON p.usuario_id = u.id
WHERE p.estado IN ('activo', 'atrasado');

-- Vista: Estadísticas por categoría
CREATE OR REPLACE VIEW vista_estadisticas_categorias AS
SELECT 
    c.id,
    c.nombre,
    c.icono,
    COUNT(l.id) AS total_libros,
    SUM(l.stock) AS total_ejemplares,
    SUM(l.veces_prestado) AS total_prestamos,
    AVG(l.calificacion) AS calificacion_promedio,
    SUM(CASE WHEN l.disponible = TRUE THEN 1 ELSE 0 END) AS libros_disponibles
FROM categorias c
LEFT JOIN libros l ON c.id = l.categoria_id
GROUP BY c.id, c.nombre, c.icono;

-- ========================================
-- TRIGGERS
-- ========================================

-- Trigger: Actualizar disponibilidad del libro al crear préstamo
DELIMITER //
CREATE TRIGGER after_prestamo_insert
AFTER INSERT ON prestamos
FOR EACH ROW
BEGIN
    UPDATE libros 
    SET stock = stock - 1,
        veces_prestado = veces_prestado + 1
    WHERE id = NEW.libro_id;
    
    UPDATE libros 
    SET disponible = FALSE 
    WHERE id = NEW.libro_id AND stock <= 0;
END//
DELIMITER ;

-- Trigger: Actualizar disponibilidad del libro al devolver
DELIMITER //
CREATE TRIGGER after_prestamo_devolucion
AFTER UPDATE ON prestamos
FOR EACH ROW
BEGIN
    IF OLD.estado = 'activo' AND NEW.estado = 'devuelto' THEN
        UPDATE libros 
        SET stock = stock + 1,
            disponible = TRUE
        WHERE id = NEW.libro_id;
    END IF;
END//
DELIMITER ;

-- Trigger: Actualizar calificación promedio del libro
DELIMITER //
CREATE TRIGGER after_valoracion_insert
AFTER INSERT ON valoraciones
FOR EACH ROW
BEGIN
    UPDATE libros 
    SET calificacion = (
        SELECT AVG(calificacion) 
        FROM valoraciones 
        WHERE libro_id = NEW.libro_id
    )
    WHERE id = NEW.libro_id;
END//
DELIMITER ;

-- Trigger: Registrar acciones en historial
DELIMITER //
CREATE TRIGGER after_libro_insert
AFTER INSERT ON libros
FOR EACH ROW
BEGIN
    INSERT INTO historial_acciones (accion, tabla_afectada, registro_id, descripcion)
    VALUES ('INSERT', 'libros', NEW.id, CONCAT('Nuevo libro agregado: ', NEW.titulo));
END//
DELIMITER ;

-- ========================================
-- PROCEDIMIENTOS ALMACENADOS
-- ========================================

-- Procedimiento: Buscar libros
DELIMITER //
CREATE PROCEDURE buscar_libros(
    IN p_termino VARCHAR(255),
    IN p_categoria_id INT,
    IN p_disponible BOOLEAN
)
BEGIN
    SELECT * FROM vista_libros_completa
    WHERE (p_termino IS NULL OR 
           titulo LIKE CONCAT('%', p_termino, '%') OR
           autor LIKE CONCAT('%', p_termino, '%') OR
           descripcion LIKE CONCAT('%', p_termino, '%'))
      AND (p_categoria_id IS NULL OR categoria = (SELECT nombre FROM categorias WHERE id = p_categoria_id))
      AND (p_disponible IS NULL OR disponible = p_disponible)
    ORDER BY titulo;
END//
DELIMITER ;

-- Procedimiento: Obtener estadísticas generales
DELIMITER //
CREATE PROCEDURE obtener_estadisticas()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM libros) AS total_libros,
        (SELECT COUNT(*) FROM categorias WHERE activo = TRUE) AS total_categorias,
        (SELECT COUNT(*) FROM usuarios WHERE activo = TRUE) AS total_usuarios,
        (SELECT COUNT(*) FROM prestamos WHERE estado = 'activo') AS prestamos_activos,
        (SELECT COUNT(*) FROM prestamos WHERE estado = 'atrasado') AS prestamos_atrasados,
        (SELECT SUM(stock) FROM libros) AS total_ejemplares,
        (SELECT AVG(calificacion) FROM libros) AS calificacion_promedio;
END//
DELIMITER ;

-- ========================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ========================================

-- Crear índices compuestos para búsquedas frecuentes
CREATE INDEX idx_libro_categoria_disponible ON libros(categoria_id, disponible);
CREATE INDEX idx_prestamo_estado_fecha ON prestamos(estado, fecha_devolucion_esperada);
CREATE INDEX idx_usuario_tipo_activo ON usuarios(tipo, activo);