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

-- Usuari admin per defecte
INSERT INTO usuaris (nom, email, password, rol)
VALUES (
    'Administrador',
    'admin@botiga.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: "password"
    'admin'
);

-- Categories d'exemple
INSERT INTO categories (nom) VALUES ('Roba'), ('Calçat'), ('Accessoris');