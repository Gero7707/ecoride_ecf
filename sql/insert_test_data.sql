-- Données de test pour EcoRide
USE ecoride;

-- Utilisateurs de test
INSERT INTO utilisateur (pseudo, email, mot_de_passe, telephone, statut, credits) VALUES
('marie_eco', 'marie@test.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0601020304', 'chauffeur', 45),
('paul_voy', 'paul@test.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0605060708', 'passager', 25),
('julie_green', 'julie@test.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0709101112', 'chauffeur', 38),
('thomas_road', 'thomas@test.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0612131415', 'passager', 15),
('employe1', 'employe@ecoride.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0616171819', 'employe', 100);

-- Véhicules de test
INSERT INTO vehicule (utilisateur_id, marque, modele, couleur, plaque_immatriculation, date_premiere_immatriculation, nombre_places, energie) VALUES
(1, 'Tesla', 'Model 3', 'Blanc', 'AB-123-CD', '2022-03-15', 4, 'electrique'),
(1, 'Renault', 'Zoe', 'Bleu', 'EF-456-GH', '2021-09-20', 4, 'electrique'),
(3, 'Toyota', 'Prius', 'Gris', 'IJ-789-KL', '2020-06-10', 4, 'hybride'),
(3, 'Volkswagen', 'Golf', 'Rouge', 'MN-012-OP', '2019-11-05', 4, 'essence');

-- Préférences chauffeurs
INSERT INTO preferences_chauffeur (chauffeur_id, accepte_fumeur, accepte_animaux, preferences_custom) VALUES
(1, FALSE, TRUE, 'Musique douce appréciée, pas de conversation excessive'),
(3, TRUE, FALSE, 'Partage des frais d\'autoroute, ponctualité exigée');

-- Covoiturages de test
INSERT INTO covoiturage (chauffeur_id, vehicule_id, ville_depart, ville_arrivee, date_depart, heure_depart, heure_arrivee, prix, places_disponibles, statut) VALUES
(1, 1, 'Paris', 'Lyon', '2025-01-15', '08:00:00', '12:30:00', 35.00, 3, 'prevu'),
(1, 2, 'Lyon', 'Marseille', '2025-01-16', '14:00:00', '18:00:00', 28.00, 2, 'prevu'),
(3, 3, 'Paris', 'Bordeaux', '2025-01-18', '07:30:00', '13:00:00', 42.00, 3, 'prevu'),
(3, 4, 'Nantes', 'Paris', '2025-01-20', '06:00:00', '10:30:00', 38.00, 1, 'prevu'),
(1, 1, 'Paris', 'Lille', '2025-01-22', '16:00:00', '19:30:00', 32.00, 4, 'prevu');

-- Réservations de test
INSERT INTO reservation (passager_id, covoiturage_id, statut) VALUES
(2, 1, 'confirmee'),
(4, 3, 'confirmee'),
(2, 5, 'confirmee');

-- Avis de test
INSERT INTO avis (evaluateur_id, evalue_id, covoiturage_id, note, commentaire, valide) VALUES
(2, 1, 1, 5, 'Excellente conductrice, très ponctuelle et voiture très propre !', TRUE),
(4, 3, 3, 4, 'Bon chauffeur, trajet agréable. Juste un peu de retard au départ.', TRUE),
(1, 2, 1, 5, 'Passager très sympa et respectueux !', TRUE);