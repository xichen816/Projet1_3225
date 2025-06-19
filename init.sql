CREATE DATABASE IF NOT EXISTS gestion_taches DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_taches;

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('utilisateur', 'admin') NOT NULL DEFAULT 'utilisateur',
    token VARCHAR(64) DEFAULT NULL
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE tuiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    id_utilisateur INT NOT NULL,
    id_categorie INT DEFAULT NULL,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (id_categorie) REFERENCES categories(id) ON DELETE SET NULL
);