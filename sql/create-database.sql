-- Creation de la base de données EcoRide
CREATE DATABASE IF NOT EXISTS ecoride CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE ecoride;

-- Table des utilisateurs
CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    adresse VARCHAR(255),
    photo VARCHAR(255), -- Chemin vers le fichier image
    credits INT DEFAULT 20,
    statut ENUM('passager', 'chauffeur', 'admin', 'employe') DEFAULT 'passager',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    suspendu BOOLEAN DEFAULT FALSE
);

-- Table des véhicules
CREATE TABLE vehicule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    marque VARCHAR(50) NOT NULL,
    modele VARCHAR(50) NOT NULL,
    couleur VARCHAR(30),
    plaque_immatriculation VARCHAR(20) NOT NULL,
    date_premiere_immatriculation DATE,
    nombre_places INT NOT NULL,
    energie ENUM('essence', 'diesel', 'electrique', 'hybride') DEFAULT 'essence',
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
);

-- Table des covoiturages
CREATE TABLE covoiturage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chauffeur_id INT NOT NULL,
    vehicule_id INT NOT NULL,
    ville_depart VARCHAR(100) NOT NULL,
    ville_arrivee VARCHAR(100) NOT NULL,
    date_depart DATE NOT NULL,
    heure_depart TIME NOT NULL,
    heure_arrivee TIME,
    prix DECIMAL(5,2) NOT NULL,
    places_disponibles INT NOT NULL,
    statut ENUM('prevu', 'en_cours', 'termine', 'annule') DEFAULT 'prevu',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chauffeur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicule_id) REFERENCES vehicule(id) ON DELETE CASCADE
);

-- Table des réservations
CREATE TABLE reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    passager_id INT NOT NULL,
    covoiturage_id INT NOT NULL,
    statut ENUM('confirmee', 'annule', 'terminee') DEFAULT 'confirmee',
    date_reservation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (passager_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (covoiturage_id) REFERENCES covoiturage(id) ON DELETE CASCADE
);

-- Table des avis
CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evaluateur_id INT NOT NULL,
    evalue_id INT NOT NULL,
    covoiturage_id INT NOT NULL,
    note INT CHECK (note >= 1 AND note <= 5),
    commentaire TEXT,
    valide BOOLEAN DEFAULT FALSE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evaluateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (evalue_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (covoiturage_id) REFERENCES covoiturage(id) ON DELETE CASCADE
);

-- Table des préférences chauffeur
CREATE TABLE preferences_chauffeur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chauffeur_id INT NOT NULL,
    accepte_fumeur BOOLEAN DEFAULT FALSE,
    accepte_animaux BOOLEAN DEFAULT FALSE,
    preferences_custom TEXT,
    FOREIGN KEY (chauffeur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
);

-- Table de configuration
CREATE TABLE parametre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cle_param VARCHAR(50) NOT NULL UNIQUE,
    valeur VARCHAR(255) NOT NULL,
    description TEXT
);

-- Table pour le rate limiting
CREATE TABLE security_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    success BOOLEAN DEFAULT FALSE,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pour logger les emails envoyés
CREATE TABLE `email_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `destinataire` varchar(255) NOT NULL,
    `sujet` varchar(255) NOT NULL,
    `type_notification` enum('reservation','confirmation','annulation','message','rappel') NOT NULL,
    `statut` enum('envoye','echec') DEFAULT 'envoye',
    `message_erreur` text DEFAULT NULL,
    `date_envoi` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_destinataire` (`destinataire`),
    KEY `idx_type` (`type_notification`),
    KEY `idx_date` (`date_envoi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Insertion des paramètres de base
INSERT INTO parametre (cle_param, valeur, description) VALUES
('credits_commission', '2', 'Nombre de crédits pris par la plateforme par trajet'),
('credits_nouveaux_utilisateurs', '20', 'Crédits accordés à la création de compte');

-- Création d'un utilisateur admin par défaut
INSERT INTO utilisateur (pseudo, email, mot_de_passe, statut, credits) VALUES
('admin', 'admin@ecoride.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1000);
-- Mot de passe: password