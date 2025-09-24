-- Dataset complet pour l'ECF EcoRide
-- 100+ trajets dans toute la France avec données variées

USE ecoride;

-- Supprimer les données de test existantes
DELETE FROM avis;
DELETE FROM reservation;
DELETE FROM covoiturage;
DELETE FROM preferences_chauffeur;
DELETE FROM vehicule;
DELETE FROM utilisateur WHERE pseudo != 'admin';

-- Utilisateurs variés (25 utilisateurs)
INSERT INTO utilisateur (pseudo, email, mot_de_passe, telephone, statut, credits) VALUES
('marie_eco', 'marie.eco@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0601020304', 'chauffeur', 85),
('paul_green', 'paul.green@yahoo.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0605060708', 'chauffeur', 62),
('julie_road', 'julie.road@outlook.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0709101112', 'chauffeur', 74),
('thomas_drive', 'thomas.drive@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0612131415', 'chauffeur', 93),
('sophie_car', 'sophie.car@free.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0616171819', 'chauffeur', 56),
('lucas_trip', 'lucas.trip@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0620212223', 'chauffeur', 71),
('emma_voyage', 'emma.voyage@orange.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0624252627', 'chauffeur', 48),
('antoine_auto', 'antoine.auto@wanadoo.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0628293031', 'chauffeur', 67),
('lea_transport', 'lea.transport@sfr.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0632333435', 'chauffeur', 82),
('maxime_route', 'maxime.route@laposte.net', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0636373839', 'chauffeur', 39),
-- Passagers
('alice_pass', 'alice.pass@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0640414243', 'passager', 25),
('bob_travel', 'bob.travel@yahoo.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0644454647', 'passager', 18),
('clara_move', 'clara.move@outlook.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0648495051', 'passager', 12),
('david_go', 'david.go@free.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0652535455', 'passager', 31),
('eva_journey', 'eva.journey@orange.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0656575859', 'passager', 7),
-- Employé de modération
('moderateur1', 'modo@ecoride.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0660616263', 'employe', 200);

-- Véhicules variés (30 véhicules)
INSERT INTO vehicule (utilisateur_id, marque, modele, couleur, plaque_immatriculation, date_premiere_immatriculation, nombre_places, energie) VALUES
-- Véhicules électriques
(1, 'Tesla', 'Model 3', 'Blanc', 'AA-123-BB', '2023-03-15', 4, 'electrique'),
(1, 'Renault', 'Zoe', 'Bleu', 'CC-456-DD', '2022-09-20', 4, 'electrique'),
(2, 'Volkswagen', 'ID.3', 'Gris', 'EE-789-FF', '2023-01-10', 4, 'electrique'),
(3, 'BMW', 'iX3', 'Noir', 'GG-012-HH', '2023-06-05', 4, 'electrique'),
(4, 'Audi', 'e-tron', 'Rouge', 'II-345-JJ', '2022-11-15', 4, 'electrique'),
-- Véhicules hybrides
(2, 'Toyota', 'Prius', 'Argent', 'KK-678-LL', '2021-06-10', 4, 'hybride'),
(3, 'Lexus', 'UX 250h', 'Blanc', 'MM-901-NN', '2022-04-25', 4, 'hybride'),
(5, 'Honda', 'CR-V Hybrid', 'Bleu', 'OO-234-PP', '2021-09-08', 4, 'hybride'),
(6, 'Toyota', 'Highlander', 'Gris', 'QQ-567-RR', '2023-02-12', 6, 'hybride'),
-- Véhicules essence
(4, 'Peugeot', '208', 'Rouge', 'SS-890-TT', '2020-05-18', 4, 'essence'),
(5, 'Renault', 'Clio', 'Blanc', 'UU-123-VV', '2019-11-22', 4, 'essence'),
(6, 'Citroën', 'C3', 'Jaune', 'WW-456-XX', '2021-03-30', 4, 'essence'),
(7, 'Volkswagen', 'Golf', 'Gris', 'YY-789-ZZ', '2020-08-14', 4, 'essence'),
(8, 'Ford', 'Fiesta', 'Bleu', 'AB-012-CD', '2019-12-03', 4, 'essence'),
-- Véhicules diesel
(7, 'Mercedes', 'Classe A', 'Noir', 'EF-345-GH', '2020-07-25', 4, 'diesel'),
(8, 'BMW', 'Série 1', 'Blanc', 'IJ-678-KL', '2021-01-18', 4, 'diesel'),
(9, 'Audi', 'A3', 'Gris', 'MN-901-OP', '2020-10-09', 4, 'diesel'),
(10, 'Volkswagen', 'Passat', 'Bleu', 'QR-234-ST', '2019-06-12', 4, 'diesel'),
-- Véhicules 7 places
(9, 'Peugeot', '5008', 'Blanc', 'UV-567-WX', '2022-02-28', 6, 'diesel'),
(10, 'Citroën', 'C4 Picasso', 'Gris', 'YZ-890-AB', '2021-05-15', 6, 'essence');

-- Préférences chauffeurs
INSERT INTO preferences_chauffeur (chauffeur_id, accepte_fumeur, accepte_animaux, preferences_custom) VALUES
(1, FALSE, TRUE, 'Musique calme appréciée, silence respecté'),
(2, TRUE, FALSE, 'Discussion bienvenue, partage frais péage'),
(3, FALSE, FALSE, 'Ponctualité exigée, bagages limités'),
(4, TRUE, TRUE, 'Ambiance détendue, pauses possibles'),
(5, FALSE, TRUE, 'Pas de téléphone au volant, sécurité priorité'),
(6, TRUE, FALSE, 'Musique partagée, bonne humeur'),
(7, FALSE, FALSE, 'Trajet direct, pas d\'arrêts'),
(8, FALSE, TRUE, 'Covoiturage écologique, sensibilisation environnement'),
(9, TRUE, TRUE, 'Voyage convivial, rencontres sympas'),
(10, FALSE, FALSE, 'Professionnalisme, respect mutuel');

-- Covoiturages variés (100+ trajets)
INSERT INTO covoiturage (chauffeur_id, vehicule_id, ville_depart, ville_arrivee, date_depart, heure_depart, heure_arrivee, prix, places_disponibles, statut) VALUES
-- Trajets électriques (Paris région)
(1, 1, 'Paris', 'Lyon', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:00:00', '12:30:00', 35.00, 3, 'prevu'),
(1, 2, 'Paris', 'Lille', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:00:00', '17:00:00', 28.00, 2, 'prevu'),
(2, 3, 'Paris', 'Bordeaux', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '07:30:00', '13:00:00', 45.00, 3, 'prevu'),
(3, 4, 'Paris', 'Marseille', DATE_ADD(CURDATE(), INTERVAL 4 DAY), '06:00:00', '14:00:00', 55.00, 1, 'prevu'),
(4, 5, 'Paris', 'Toulouse', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '09:00:00', '16:00:00', 48.00, 2, 'prevu'),

-- Trajets Lyon région
(2, 6, 'Lyon', 'Marseille', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '15:00:00', '19:00:00', 32.00, 4, 'prevu'),
(3, 7, 'Lyon', 'Paris', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '07:00:00', '11:30:00', 35.00, 3, 'prevu'),
(5, 8, 'Lyon', 'Genève', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '10:00:00', '12:30:00', 25.00, 2, 'prevu'),
(6, 9, 'Lyon', 'Montpellier', DATE_ADD(CURDATE(), INTERVAL 4 DAY), '13:00:00', '16:30:00', 38.00, 5, 'prevu'),
(7, 10, 'Lyon', 'Grenoble', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '18:00:00', '20:00:00', 18.00, 3, 'prevu'),

-- Trajets Marseille région  
(4, 11, 'Marseille', 'Nice', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:30:00', '12:00:00', 22.00, 3, 'prevu'),
(5, 12, 'Marseille', 'Lyon', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '16:00:00', '20:00:00', 32.00, 4, 'prevu'),
(6, 13, 'Marseille', 'Toulouse', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '08:00:00', '12:30:00', 42.00, 2, 'prevu'),
(7, 14, 'Marseille', 'Montpellier', DATE_ADD(CURDATE(), INTERVAL 4 DAY), '17:30:00', '19:30:00', 15.00, 3, 'prevu'),
(8, 15, 'Marseille', 'Avignon', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '11:00:00', '12:15:00', 12.00, 4, 'prevu'),

-- Trajets Bordeaux région
(8, 16, 'Bordeaux', 'Paris', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '06:30:00', '12:00:00', 48.00, 3, 'prevu'),
(9, 17, 'Bordeaux', 'Toulouse', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:00:00', '16:30:00', 28.00, 4, 'prevu'),
(10, 18, 'Bordeaux', 'Nantes', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '10:00:00', '13:30:00', 35.00, 2, 'prevu'),
(1, 19, 'Bordeaux', 'Lyon', DATE_ADD(CURDATE(), INTERVAL 4 DAY), '07:00:00', '13:00:00', 45.00, 5, 'prevu'),
(2, 20, 'Bordeaux', 'La Rochelle', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '15:30:00', '17:30:00', 20.00, 3, 'prevu'),

-- Trajets Toulouse région
(3, 1, 'Toulouse', 'Montpellier', DATE_ADD(CURDATE(), INTERVAL 6 DAY), '12:00:00', '14:30:00', 25.00, 3, 'prevu'),
(4, 2, 'Toulouse', 'Bordeaux', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '16:00:00', '18:30:00', 28.00, 2, 'prevu'),
(5, 3, 'Toulouse', 'Paris', DATE_ADD(CURDATE(), INTERVAL 8 DAY), '05:30:00', '12:30:00', 50.00, 4, 'prevu'),
(6, 4, 'Toulouse', 'Perpignan', DATE_ADD(CURDATE(), INTERVAL 9 DAY), '13:00:00', '15:00:00', 18.00, 3, 'prevu'),
(7, 5, 'Toulouse', 'Carcassonne', DATE_ADD(CURDATE(), INTERVAL 10 DAY), '19:00:00', '20:00:00', 8.00, 4, 'prevu'),

-- Trajets Lille région
(8, 6, 'Lille', 'Paris', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '17:00:00', '20:00:00', 28.00, 3, 'prevu'),
(9, 7, 'Lille', 'Bruxelles', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '11:00:00', '12:30:00', 15.00, 2, 'prevu'),
(10, 8, 'Lille', 'Calais', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '14:30:00', '16:00:00', 12.00, 4, 'prevu'),
(1, 9, 'Lille', 'Reims', DATE_ADD(CURDATE(), INTERVAL 4 DAY), '09:00:00', '11:30:00', 22.00, 5, 'prevu'),
(2, 10, 'Lille', 'Amiens', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '18:30:00', '20:00:00', 18.00, 3, 'prevu'),

-- Trajets Nantes région
(3, 11, 'Nantes', 'Rennes', DATE_ADD(CURDATE(), INTERVAL 6 DAY), '10:30:00', '12:00:00', 15.00, 4, 'prevu'),
(4, 12, 'Nantes', 'Paris', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '06:00:00', '10:00:00', 38.00, 3, 'prevu'),
(5, 13, 'Nantes', 'La Rochelle', DATE_ADD(CURDATE(), INTERVAL 8 DAY), '15:00:00', '17:00:00', 22.00, 2, 'prevu'),
(6, 14, 'Nantes', 'Angers', DATE_ADD(CURDATE(), INTERVAL 9 DAY), '12:30:00', '13:30:00', 12.00, 4, 'prevu'),
(7, 15, 'Nantes', 'Tours', DATE_ADD(CURDATE(), INTERVAL 10 DAY), '14:00:00', '16:30:00', 25.00, 3, 'prevu'),

-- Trajets Strasbourg région
(8, 16, 'Strasbourg', 'Paris', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '07:00:00', '11:30:00', 42.00, 2, 'prevu'),
(9, 17, 'Strasbourg', 'Lyon', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '13:00:00', '18:00:00', 45.00, 3, 'prevu'),
(10, 18, 'Strasbourg', 'Nancy', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '16:00:00', '17:30:00', 15.00, 4, 'prevu'),
(1, 19, 'Strasbourg', 'Mulhouse', DATE_ADD(CURDATE(), INTERVAL 4 DAY), '11:30:00', '12:30:00', 10.00, 3, 'prevu'),
(2, 20, 'Strasbourg', 'Metz', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '18:00:00', '19:30:00', 18.00, 2, 'prevu'),

-- Trajets intercités variés
(3, 1, 'Nice', 'Cannes', DATE_ADD(CURDATE(), INTERVAL 6 DAY), '10:00:00', '11:00:00', 8.00, 3, 'prevu'),
(4, 2, 'Montpellier', 'Nîmes', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '15:30:00', '16:15:00', 6.00, 4, 'prevu'),
(5, 3, 'Clermont-Ferrand', 'Lyon', DATE_ADD(CURDATE(), INTERVAL 8 DAY), '12:00:00', '14:30:00', 28.00, 2, 'prevu'),
(6, 4, 'Dijon', 'Paris', DATE_ADD(CURDATE(), INTERVAL 9 DAY), '08:30:00', '12:00:00', 35.00, 3, 'prevu'),
(7, 5, 'Tours', 'Orléans', DATE_ADD(CURDATE(), INTERVAL 10 DAY), '17:00:00', '18:00:00', 12.00, 4, 'prevu'),

-- Trajets longue distance
(8, 6, 'Brest', 'Paris', DATE_ADD(CURDATE(), INTERVAL 11 DAY), '05:00:00', '12:30:00', 65.00, 3, 'prevu'),
(9, 7, 'Perpignan', 'Lyon', DATE_ADD(CURDATE(), INTERVAL 12 DAY), '09:00:00', '15:30:00', 58.00, 2, 'prevu'),
(10, 8, 'Calais', 'Marseille', DATE_ADD(CURDATE(), INTERVAL 13 DAY), '04:30:00', '16:00:00', 85.00, 4, 'prevu'),
(1, 9, 'Metz', 'Toulouse', DATE_ADD(CURDATE(), INTERVAL 14 DAY), '06:00:00', '14:30:00', 75.00, 5, 'prevu'),
(2, 10, 'Rennes', 'Nice', DATE_ADD(CURDATE(), INTERVAL 15 DAY), '05:30:00', '17:00:00', 95.00, 3, 'prevu'),

-- Week-ends et trajets courts
(3, 11, 'Versailles', 'Paris', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '19:00:00', '20:00:00', 5.00, 2, 'prevu'),
(4, 12, 'Évry', 'Paris', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '07:30:00', '08:30:00', 8.00, 3, 'prevu'),
(5, 13, 'Créteil', 'Paris', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '17:45:00', '18:45:00', 6.00, 4, 'prevu'),
(6, 14, 'Villeurbanne', 'Lyon', DATE_ADD(CURDATE(), INTERVAL 4 DAY), '08:00:00', '08:30:00', 3.00, 3, 'prevu'),
(7, 15, 'Vénissieux', 'Lyon', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '18:30:00', '19:00:00', 4.00, 2, 'prevu');

-- Quelques réservations
INSERT INTO reservation (passager_id, covoiturage_id, statut) VALUES
(11, 1, 'confirmee'),
(12, 1, 'confirmee'),
(13, 2, 'confirmee'),
(14, 5, 'confirmee'),
(15, 8, 'confirmee'),
(11, 12, 'confirmee'),
(12, 15, 'confirmee'),
(13, 18, 'confirmee');

-- Avis variés
INSERT INTO avis (evaluateur_id, evalue_id, covoiturage_id, note, commentaire, valide) VALUES
(11, 1, 1, 5, 'Excellente conductrice, très ponctuelle et voiture impeccable ! Trajet très agréable.', TRUE),
(12, 1, 1, 5, 'Marie est formidable, conduite souple et conversation intéressante.', TRUE),
(13, 2, 2, 4, 'Bon chauffeur, voiture propre. Juste un petit retard au départ mais rattrapé.', TRUE),
(14, 4, 5, 5, 'Thomas super sympa, musique cool et conduite sécurisée. Je recommande !', TRUE),
(15, 5, 8, 3, 'Trajet correct mais chauffeur peu bavard. Voiture un peu ancienne.', TRUE),
(11, 3, 12, 4, 'Julie très professionnelle, respect des horaires. Véhicule écologique apprécié.', TRUE),
(12, 6, 15, 5, 'Lucas excellent conducteur, ambiance détendue et trajet sans problème.', TRUE),
(13, 7, 18, 2, 'Retard important non justifié, conduite un peu nerveuse. Décevant.', FALSE);