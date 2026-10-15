-- =====================================================
-- PROYECTO: FACTUWEB PRO
-- MOTOR: MySQL / MariaDB
-- =====================================================

CREATE DATABASE IF NOT EXISTS factuweb_pro
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE factuweb_pro;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS detalle_borrador;
DROP TABLE IF EXISTS detalle_factura;
DROP TABLE IF EXISTS ventas_borrador;
DROP TABLE IF EXISTS facturas;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS resolucion_dian;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS empresa;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- 1. EMPRESA
-- =====================================================

CREATE TABLE empresa (
    id_empresa BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nit VARCHAR(30) NOT NULL,
    razon_social VARCHAR(200) NOT NULL,
    direccion VARCHAR(255),
    telefono VARCHAR(30),
    email VARCHAR(150),
    logo_url VARCHAR(500),
    UNIQUE KEY uk_empresa_nit (nit)
) ENGINE=InnoDB;

-- =====================================================
-- 2. ROLES
-- =====================================================

CREATE TABLE roles (
    id_rol BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- =====================================================
-- 3. USUARIOS
-- =====================================================

CREATE TABLE usuarios (
    id_usuario BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_empresa BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    id_rol BIGINT UNSIGNED NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    intentos_fallidos INT UNSIGNED NOT NULL DEFAULT 0,
    ultimo_acceso DATETIME NULL,
    token_recuperacion VARCHAR(255) NULL,
    porcentaje_comision DECIMAL(5,2) NOT NULL DEFAULT 0,

    CONSTRAINT fk_usuarios_empresa
        FOREIGN KEY (id_empresa)
        REFERENCES empresa(id_empresa)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_usuarios_rol
        FOREIGN KEY (id_rol)
        REFERENCES roles(id_rol)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =====================================================
-- 4. CATEGORIAS
-- =====================================================

CREATE TABLE categorias (
    id_categoria BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    fecha_creacion TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- 5. PRODUCTOS
-- =====================================================

CREATE TABLE productos (
    id_producto BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_categoria BIGINT UNSIGNED NOT NULL,
    codigo_barras VARCHAR(100) NULL,
    nombre_producto VARCHAR(200) NOT NULL,
    precio_compra DECIMAL(12,2) NOT NULL DEFAULT 0,
    precio_venta DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock_actual INT NOT NULL DEFAULT 0,
    estado_producto VARCHAR(30) NOT NULL DEFAULT 'activo',
    tarifa_iva DECIMAL(5,2) NOT NULL DEFAULT 0,
    imagen_url VARCHAR(500) NULL,

    UNIQUE KEY uk_productos_codigo_barras (codigo_barras),
    INDEX idx_productos_nombre (nombre_producto),

    CONSTRAINT fk_productos_categoria
        FOREIGN KEY (id_categoria)
        REFERENCES categorias(id_categoria)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =====================================================
-- 6. CLIENTES
-- =====================================================

CREATE TABLE clientes (
    id_cliente BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    identificacion VARCHAR(50) NOT NULL,
    nombre_razon_social VARCHAR(200) NOT NULL,
    email VARCHAR(150) NULL,
    direccion VARCHAR(255) NULL,
    telefono VARCHAR(30) NULL,

    INDEX idx_clientes_identificacion (identificacion)
) ENGINE=InnoDB;

-- =====================================================
-- 7. RESOLUCIONES DIAN
-- =====================================================

CREATE TABLE resolucion_dian (
    id_resolucion BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_empresa BIGINT UNSIGNED NOT NULL,
    numero_resolucion VARCHAR(100) NOT NULL,
    fecha_vigencia DATE NOT NULL,
    prefijo VARCHAR(20) NOT NULL,
    rango_inicial BIGINT UNSIGNED NOT NULL,
    rango_final BIGINT UNSIGNED NOT NULL,
    contador_actual BIGINT UNSIGNED NOT NULL DEFAULT 0,
    estado VARCHAR(30) NOT NULL DEFAULT 'activa',

    CONSTRAINT fk_resolucion_empresa
        FOREIGN KEY (id_empresa)
        REFERENCES empresa(id_empresa)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    UNIQUE KEY uk_resolucion_empresa_numero
        (id_empresa, numero_resolucion)
) ENGINE=InnoDB;

-- =====================================================
-- 8. FACTURAS
-- =====================================================

CREATE TABLE facturas (
    id_factura BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_empresa BIGINT UNSIGNED NOT NULL,
    id_resolucion BIGINT UNSIGNED NOT NULL,
    prefijo_resolucion VARCHAR(20) NOT NULL,
    consecutivo BIGINT UNSIGNED NOT NULL,
    fecha_emision DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_cliente BIGINT UNSIGNED NOT NULL,
    id_usuario BIGINT UNSIGNED NOT NULL,
    subtotal DECIMAL(14,2) NOT NULL DEFAULT 0,
    total_iva DECIMAL(14,2) NOT NULL DEFAULT 0,
    total_pagar DECIMAL(14,2) NOT NULL DEFAULT 0,
    porcentaje_comision_aplicado DECIMAL(5,2) NOT NULL DEFAULT 0,
    cufe VARCHAR(255) NULL,
    codigo_qr TEXT NULL,
    estado_dian VARCHAR(50) NOT NULL DEFAULT 'pendiente',
    motivo_rechazo TEXT NULL,
    fecha_validacion_dian DATETIME NULL,

    CONSTRAINT fk_facturas_empresa
        FOREIGN KEY (id_empresa)
        REFERENCES empresa(id_empresa)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_facturas_resolucion
        FOREIGN KEY (id_resolucion)
        REFERENCES resolucion_dian(id_resolucion)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_facturas_cliente
        FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_facturas_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    UNIQUE KEY uk_facturas_empresa_numero
        (id_empresa, prefijo_resolucion, consecutivo),

    INDEX idx_facturas_fecha (fecha_emision),
    INDEX idx_facturas_estado_dian (estado_dian)
) ENGINE=InnoDB;

-- =====================================================
-- 9. VENTAS BORRADOR
-- =====================================================

CREATE TABLE ventas_borrador (
    id_borrador BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario BIGINT UNSIGNED NOT NULL,
    id_cliente BIGINT UNSIGNED NOT NULL,
    fecha_actualizacion TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_borrador_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_borrador_cliente
        FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =====================================================
-- 10. DETALLE FACTURA
-- =====================================================

CREATE TABLE detalle_factura (
    id_detalle BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_factura BIGINT UNSIGNED NOT NULL,
    id_producto BIGINT UNSIGNED NOT NULL,
    cantidad INT UNSIGNED NOT NULL,
    precio_unitario_venta DECIMAL(12,2) NOT NULL,
    precio_unitario_costo DECIMAL(12,2) NOT NULL,
    subtotal_linea DECIMAL(14,2) NOT NULL,

    CONSTRAINT fk_detalle_factura_factura
        FOREIGN KEY (id_factura)
        REFERENCES facturas(id_factura)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_detalle_factura_producto
        FOREIGN KEY (id_producto)
        REFERENCES productos(id_producto)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =====================================================
-- 11. DETALLE BORRADOR
-- =====================================================

CREATE TABLE detalle_borrador (
    id_detalle_borrador BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_borrador BIGINT UNSIGNED NOT NULL,
    id_producto BIGINT UNSIGNED NOT NULL,
    cantidad INT UNSIGNED NOT NULL,

    CONSTRAINT fk_detalle_borrador_venta
        FOREIGN KEY (id_borrador)
        REFERENCES ventas_borrador(id_borrador)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_detalle_borrador_producto
        FOREIGN KEY (id_producto)
        REFERENCES productos(id_producto)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;
