-- 1. Tabla de Usuarios
CREATE TABLE honey_usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    tipo_cliente ENUM('personal', 'negocio') NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabla de Preferencias
CREATE TABLE honey_preferencias (
    id_preferencia INT AUTO_INCREMENT PRIMARY KEY,
    nombre_preferencia VARCHAR(50) NOT NULL
);

-- 3. Tabla de Productos
CREATE TABLE honey_productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre_producto VARCHAR(100) NOT NULL,
    categoria ENUM('Petit Fours', 'Tartas') NOT NULL
);

-- 4. Tabla relacional para Preferencias (Muchos a Muchos)
CREATE TABLE honey_usuarios_preferencias (
    id_usuario INT NOT NULL,
    id_preferencia INT NOT NULL,
    PRIMARY KEY (id_usuario, id_preferencia),
    FOREIGN KEY (id_usuario) REFERENCES honey_usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_preferencia) REFERENCES honey_preferencias(id_preferencia) ON DELETE CASCADE
);

-- 5. Tabla relacional para Productos (Muchos a Muchos)
CREATE TABLE honey_usuarios_productos (
    id_usuario INT NOT NULL,
    id_producto INT NOT NULL,
    PRIMARY KEY (id_usuario, id_producto),
    FOREIGN KEY (id_usuario) REFERENCES honey_usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES honey_productos(id_producto) ON DELETE CASCADE
);

-- Inserción de datos maestros
INSERT INTO honey_preferencias (nombre_preferencia) VALUES 
('Openmind'), ('Consumidor estandar'), ('Vegano'), ('Postres'), ('Con Gluten'), ('Sin Gluten');

INSERT INTO honey_productos (nombre_producto, categoria) VALUES 
('Café', 'Petit Fours'), ('Granada', 'Petit Fours'), ('Mango', 'Petit Fours'), ('Rosa Blanca', 'Petit Fours'),
('Honey Cake', 'Tartas'), ('Milhoja Napoleón', 'Tartas');