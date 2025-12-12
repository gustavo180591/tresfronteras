-- sql/schema.sql
-- Esquema de base de datos para el sistema "tresfronteras"

-- Crear base de datos (podés omitir esto si la creás a mano)
CREATE DATABASE IF NOT EXISTS tresfronteras
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tresfronteras;

-- ============================================
-- Tabla: tipos_torneo
-- (por puntos / por eliminación)
-- ============================================
DROP TABLE IF EXISTS tipos_torneo;

CREATE TABLE tipos_torneo (
    id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cargar tipos de torneo básicos
INSERT INTO tipos_torneo (nombre) VALUES
  ('puntos'),
  ('eliminacion');


-- ============================================
-- Tabla: categorias
-- (Sub 12, Sub 14, Femenino, Libre, etc.)
-- ============================================
DROP TABLE IF EXISTS categorias;

CREATE TABLE categorias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    tipo_torneo_id TINYINT UNSIGNED NOT NULL,
    CONSTRAINT fk_categorias_tipo_torneo
        FOREIGN KEY (tipo_torneo_id)
        REFERENCES tipos_torneo (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================
-- Tabla: partidos
-- (fixture de todos los partidos)
-- ============================================
DROP TABLE IF EXISTS partidos;

CREATE TABLE partidos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT UNSIGNED NOT NULL,
    equipo_a VARCHAR(100) NOT NULL,
    equipo_b VARCHAR(100) NOT NULL,
    fecha_hora DATETIME NOT NULL,
    cancha VARCHAR(100) DEFAULT NULL,
    observaciones TEXT DEFAULT NULL,

    -- Estado del partido
    estado ENUM('pendiente', 'en_juego', 'finalizado') NOT NULL DEFAULT 'pendiente',

    -- Resultado (para posiciones y llaves)
    goles_equipo_a TINYINT UNSIGNED DEFAULT NULL,
    goles_equipo_b TINYINT UNSIGNED DEFAULT NULL,

    -- Información útil para torneos por eliminación (ronda)
    ronda VARCHAR(50) DEFAULT NULL,
    numero_en_ronda TINYINT UNSIGNED DEFAULT NULL,

    CONSTRAINT fk_partidos_categoria
        FOREIGN KEY (categoria_id)
        REFERENCES categorias (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índices para búsquedas frecuentes
CREATE INDEX idx_partidos_categoria ON partidos (categoria_id);
CREATE INDEX idx_partidos_fecha ON partidos (fecha_hora);
CREATE INDEX idx_partidos_estado ON partidos (estado);


-- ============================================
-- Tabla: pedidos_fotos
-- (lista de pedidos con estados y forma de pago)
-- ============================================
DROP TABLE IF EXISTS pedidos_fotos;

CREATE TABLE pedidos_fotos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    partido_id INT UNSIGNED NOT NULL,

    nombre_cliente VARCHAR(150) NOT NULL,
    archivos TEXT NOT NULL, -- Nombres de archivos separados por coma u otro formato
    cantidad_fotos INT UNSIGNED NOT NULL,

    forma_pago ENUM('efectivo', 'transferencia') NOT NULL,
    telefono VARCHAR(30) DEFAULT NULL,

    estado_pago ENUM('pagado', 'no_pagado') NOT NULL DEFAULT 'no_pagado',
    estado_entrega ENUM('entregado', 'no_entregado') NOT NULL DEFAULT 'no_entregado',

    -- Para recaudación y control temporal
    fecha_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- Monto total del pedido (puede calcularse como cantidad_fotos * precio_unitario)
    -- pero se guarda para facilitar reportes rápidos
    monto_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    observaciones TEXT DEFAULT NULL,

    CONSTRAINT fk_pedidos_partido
        FOREIGN KEY (partido_id)
        REFERENCES partidos (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índices para filtros en listado de pedidos
CREATE INDEX idx_pedidos_partido ON pedidos_fotos (partido_id);
CREATE INDEX idx_pedidos_fecha ON pedidos_fotos (fecha_pedido);
CREATE INDEX idx_pedidos_forma_pago ON pedidos_fotos (forma_pago);
CREATE INDEX idx_pedidos_estado_pago ON pedidos_fotos (estado_pago);
CREATE INDEX idx_pedidos_estado_entrega ON pedidos_fotos (estado_entrega);


-- ============================================
-- Tabla: configuracion
-- (clave/valor para ajustes generales)
-- ============================================
DROP TABLE IF EXISTS configuracion;

CREATE TABLE configuracion (
    clave VARCHAR(50) NOT NULL PRIMARY KEY,
    valor VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Valores iniciales de configuración
INSERT INTO configuracion (clave, valor) VALUES
  ('precio_foto', '3000.00'),
  ('nombre_evento', 'Tresfronteras'),
  ('moneda', 'ARS');
