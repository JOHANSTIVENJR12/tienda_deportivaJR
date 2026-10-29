-- Esquema inicial: el repositorio original no tenía tablas ni checkout.
-- Importar en una base de datos nueva llamada tienda_deportiva_jr.
CREATE DATABASE IF NOT EXISTS tienda_deportiva_jr CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tienda_deportiva_jr;
CREATE TABLE usuarios (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password VARCHAR(255) NOT NULL,
  fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id), UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE direcciones (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id INT UNSIGNED NOT NULL,
  calle VARCHAR(200) NOT NULL,
  ciudad VARCHAR(100) NOT NULL,
  cp CHAR(5) NOT NULL,
  tipo ENUM('entrega','facturación') NOT NULL,
  principal TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id), KEY idx_direcciones_usuario_tipo (usuario_id,tipo,principal),
  CONSTRAINT fk_direcciones_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT chk_direccion_principal CHECK (principal IN (0,1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE pedidos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id INT UNSIGNED NULL,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL,
  calle VARCHAR(200) NOT NULL,
  ciudad VARCHAR(100) NOT NULL,
  cp CHAR(5) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  estado ENUM('pendiente','enviado','entregado') NOT NULL DEFAULT 'pendiente',
  fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  token CHAR(64) NOT NULL,
  PRIMARY KEY (id), UNIQUE KEY uq_pedido_token (token), KEY idx_pedidos_usuario_fecha (usuario_id,fecha),
  CONSTRAINT fk_pedidos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
  CONSTRAINT chk_pedido_total CHECK (total >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE pedido_lineas (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  pedido_id INT UNSIGNED NOT NULL,
  producto_id INT UNSIGNED NOT NULL,
  producto VARCHAR(150) NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  cantidad SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_lineas_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
  CONSTRAINT chk_linea_precio CHECK (precio_unitario >= 0),
  CONSTRAINT chk_linea_cantidad CHECK (cantidad BETWEEN 1 AND 20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
