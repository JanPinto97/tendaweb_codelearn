CREATE DATABASE IF NOT EXISTS botiga
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE botiga;

CREATE TABLE usuaris (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    nom      VARCHAR(100)  NOT NULL,
    email    VARCHAR(150)  NOT NULL UNIQUE,
    password VARCHAR(255)  NOT NULL,
    rol      ENUM('client','admin') NOT NULL DEFAULT 'client',
    gastat   DECIMAL(10,2) NOT NULL DEFAULT 0,
    creat_a  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id  INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE productes (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nom          VARCHAR(200)   NOT NULL,
    descripcio   TEXT,
    preu         DECIMAL(10,2)  NOT NULL,
    estoc        INT            NOT NULL DEFAULT 0,
    imatge       VARCHAR(255),
    categoria_id INT,
    creat_a      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Dades inicials: usuaris
INSERT INTO usuaris (nom, email, password, rol) VALUES
('Administrador', 'admin@botiga.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Joan Garcia',   'joan@exemple.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client');

-- Dades inicials: categories
INSERT INTO categories (nom) VALUES
('Roba'),
('Calçat'),
('Accessoris');

-- Dades inicials: productes
INSERT INTO productes (nom, descripcio, preu, estoc, categoria_id) VALUES
('Samarreta codelearn',   'Samarreta de cotó 100%',     9.99,  50, 1),
('Pantalons texans',   'Texans slim fit',              29.99,  20, 1),
('Sabates esportives', 'Sabates per córrer',           49.99,   0, 2),
('Cinturó de cuir',    'Cinturó negre de cuir genuí', 14.99,  15, 3),
('Gorra de cotó',      'Gorra ajustable unisex',       12.99,  30, 3),
('Botes de muntanya',  'Botes impermeables',           89.99,  10, 2);