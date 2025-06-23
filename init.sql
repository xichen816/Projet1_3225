CREATE DATABASE IF NOT EXISTS cafe_run DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cafe_run;

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('utilisateur', 'admin') NOT NULL DEFAULT 'utilisateur',
    token VARCHAR(64) DEFAULT NULL,
    cree TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE cafes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    adresse VARCHAR(255) DEFAULT NULL,
    categories VARCHAR(255) DEFAULT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    site_web VARCHAR(255) DEFAULT NULL
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE revues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    id_cafe INT NOT NULL,
    id_utilisateur INT NOT NULL,
    id_categorie INT DEFAULT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    description TEXT NOT NULL, -- Short preview (gets auto-cut from contenu when inserted)
    contenu TEXT NOT NULL,     -- Full review body
    rating INT CHECK (rating >= 1 AND rating <= 5),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cafe) REFERENCES cafes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (id_categorie) REFERENCES categories(id) ON DELETE SET NULL
);


CREATE TABLE revues_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_categorie INT NOT NULL,
    id_revue INT NOT NULL,
    FOREIGN KEY (id_categorie) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (id_revue) REFERENCES revues(id) ON DELETE CASCADE
);

CREATE TABLE commentaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_revue INT NOT NULL,
    id_utilisateur INT NOT NULL,
    contenu TEXT NOT NULL,
    cree TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_revue) REFERENCES revues(id) ON DELETE CASCADE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

CREATE TABLE followers (
    follower_id INT NOT NULL,
    followee_id INT NOT NULL,
    cree TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (follower_id, followee_id),
    FOREIGN KEY (follower_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (followee_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

CREATE TABLE photos_revue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_revue INT NOT NULL,
    filepath VARCHAR(255) NOT NULL,
    caption VARCHAR(255) DEFAULT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_revue) REFERENCES revues(id) ON DELETE CASCADE
);

CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_revue INT NOT NULL,
    id_utilisateur INT NOT NULL,
    cree TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id_revue, id_utilisateur),
    FOREIGN KEY (id_revue) REFERENCES revues(id) ON DELETE CASCADE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
);
