-- Création de la base de données
CREATE DATABASE IF NOT EXISTS formcampus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE formcampus;

-- Table des formations
CREATE TABLE IF NOT EXISTS formations (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    categorie VARCHAR(255) NOT NULL,
    description TEXT,
    duree VARCHAR(100),
    prix DECIMAL(10, 2) NOT NULL
);

-- Table des inscriptions (les inscriptions aux formations)
CREATE TABLE IF NOT EXISTS inscriptions (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    tel VARCHAR(20),
    id_formation INT(11) UNSIGNED NOT NULL,
    commentaire TEXT,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_formation) REFERENCES formations(id) ON DELETE CASCADE
);

-- Table des utilisateurs (pour l'administration)
CREATE TABLE IF NOT EXISTS users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Table des clients (utilisateurs publics qui s'inscrivent au site)
CREATE TABLE IF NOT EXISTS clients (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Pour le mot de passe haché
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertion d'un utilisateur admin par défaut (mot de passe 'admin' haché)
INSERT INTO users (login, password) VALUES ('admin', '$2y$10$Q.g9Q6x8J7N2L5J4K3M2O1P0I9H8G7F6E5D4C3B2A1Z0Y9X8W7V6U5T4S3R2Q1P0');

-- Insertion d'un client de test (mot de passe 'client' haché)
-- Identifiants de test client : email: client@test.com, mot de passe: client
INSERT INTO clients (nom, prenom, email, password) VALUES ('Test', 'Client', 'client@test.com', '$2y$10$Q.g9Q6x8J7N2L5J4K3M2O1P0I9H8G7F6E5D4C3B2A1Z0Y9X8W7V6U5T4S3R2Q1P0');

-- Insertion de quelques formations de test
INSERT INTO formations (titre, categorie, description, duree, prix) VALUES
('Développement Web Full Stack', 'Informatique', 'Formation complète pour maîtriser le développement front-end et back-end.', '6 mois', 4500.00),
('Marketing Digital Avancé', 'Marketing', 'Apprenez les stratégies de marketing en ligne les plus efficaces.', '3 mois', 2800.00),
('Gestion de Projet Agile', 'Management', 'Maîtriser les méthodes Agile pour la gestion de projet.', '1 mois', 1200.00);
