CREATE DATABASE IF NOT EXISTS belamitech;
USE belamitech;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    correo VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'empleado', 'usuario') DEFAULT 'usuario',
    tipo_empleado ENUM('almacen', 'programador', 'soporte', 'ciberseguridad') DEFAULT NULL,
    status ENUM('activo', 'baneado') DEFAULT 'activo',
    descripcion TEXT,
    region VARCHAR(100) DEFAULT '',
    foto_perfil VARCHAR(255) DEFAULT 'default.png',
    correo_confirmado BOOLEAN DEFAULT FALSE,
    github_url VARCHAR(255) DEFAULT '',
    linkedin_url VARCHAR(255) DEFAULT '',
    twitter_url VARCHAR(255) DEFAULT '',
    website_url VARCHAR(255) DEFAULT '',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    telefono VARCHAR(50),
    direccion TEXT,
    ingreso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('activo', 'inactivo') DEFAULT 'activo'
);

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    marca VARCHAR(255),
    proveedor_id INT,
    sku VARCHAR(100),
    codigo VARCHAR(100),
    foto_url VARCHAR(255) DEFAULT 'default.png',
    galeria JSON DEFAULT NULL,
    ingreso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('activo', 'inactivo', 'baja') DEFAULT 'activo',
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE SET NULL
);

CREATE TABLE compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_envio ENUM('sucursal', 'domicilio') NOT NULL,
    direccion_envio TEXT,
    metodo_pago ENUM('sucursal', 'paypal', 'tarjeta') NOT NULL,
    estado_pago ENUM('pendiente', 'completado', 'fallido') DEFAULT 'pendiente',
    total DECIMAL(10,2) DEFAULT 0.00,
    fecha_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE compra_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (compra_id) REFERENCES compras(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

CREATE TABLE facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NOT NULL,
    folio VARCHAR(20) UNIQUE NOT NULL,
    subtotal DECIMAL(10,2),
    iva DECIMAL(10,2),
    total DECIMAL(10,2),
    fecha_emision DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (compra_id) REFERENCES compras(id)
);

CREATE TABLE servicios_contacto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    asunto VARCHAR(255) NOT NULL,
    mensaje_usuario TEXT NOT NULL,
    notas_empleado TEXT,
    respuesta_empleado TEXT,
    empleado_id INT,
    estado ENUM('abierto', 'pago_pendiente', 'en_proceso', 'cerrado') DEFAULT 'abierto',
    precio_acordado DECIMAL(10,2),
    fecha_contacto DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (empleado_id) REFERENCES usuarios(id)
);

-- Insertar un admin por defecto 
-- Contraseña: password
INSERT INTO usuarios (nombre, correo, password_hash, rol, correo_confirmado, descripcion, foto_perfil) 
VALUES (
    'Administrador',
    'admin@belamitech.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'admin', 
    TRUE, 
    'Administrador principal del sistema', 
    'default.png'
);

-- ============================================================
-- MIGRATION: Ejecutar si la base de datos ya existe
-- ============================================================
-- ALTER TABLE usuarios ADD COLUMN github_url VARCHAR(255) DEFAULT '';
-- ALTER TABLE usuarios ADD COLUMN linkedin_url VARCHAR(255) DEFAULT '';
-- ALTER TABLE usuarios ADD COLUMN twitter_url VARCHAR(255) DEFAULT '';
-- ALTER TABLE usuarios ADD COLUMN website_url VARCHAR(255) DEFAULT '';
