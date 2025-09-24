-- ÉTAPE 1: Nettoyage complet de toutes les données
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM avis;
DELETE FROM reservation;
DELETE FROM preferences_chauffeur;
DELETE FROM security_attempts;
DELETE FROM covoiturage;
DELETE FROM vehicule;
-- Garder seulement l'admin
DELETE FROM utilisateur WHERE id > 1;
DELETE FROM parametre;

-- Réinitialiser les auto-incréments
ALTER TABLE avis AUTO_INCREMENT = 1;
ALTER TABLE reservation AUTO_INCREMENT = 1;
ALTER TABLE preferences_chauffeur AUTO_INCREMENT = 1;
ALTER TABLE security_attempts AUTO_INCREMENT = 1;
ALTER TABLE covoiturage AUTO_INCREMENT = 1;
ALTER TABLE vehicule AUTO_INCREMENT = 1;
ALTER TABLE utilisateur AUTO_INCREMENT = 2; -- Reprend après admin (id=1)
ALTER TABLE parametre AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- Réinsérer les paramètres essentiels
INSERT INTO parametre (cle_param, valeur, description) VALUES
('credits_commission', '2', 'Nombre de crédits pris par la plateforme par trajet'),
('credits_nouveaux_utilisateurs', '20', 'Crédits accordés à la création de compte');

-- ÉTAPE 2: Insertion des nouveaux utilisateurs
INSERT INTO utilisateur (pseudo, email, mot_de_passe, telephone, adresse, statut, credits) VALUES
-- Chauffeurs principaux (35 chauffeurs)
('marie_paris', 'marie.dupont@gmail.com', '$2y$10$hash1234', '0123456789', '15 rue de la Paix, Paris', 'chauffeur', 45),
('julien_lyon', 'julien.martin@gmail.com', '$2y$10$hash1234', '0234567890', '8 place Bellecour, Lyon', 'chauffeur', 38),
('sophie_marseille', 'sophie.bernard@outlook.fr', '$2y$10$hash1234', '0345678901', '22 Canebière, Marseille', 'chauffeur', 52),
('thomas_toulouse', 'thomas.petit@gmail.com', '$2y$10$hash1234', '0456789012', '10 place du Capitole, Toulouse', 'chauffeur', 29),
('camille_nice', 'camille.robert@free.fr', '$2y$10$hash1234', '0567890123', '5 promenade des Anglais, Nice', 'chauffeur', 41),
('pierre_nantes', 'pierre.richard@gmail.com', '$2y$10$hash1234', '0678901234', '12 cours Cambronne, Nantes', 'chauffeur', 33),
('lea_strasbourg', 'lea.moreau@gmail.com', '$2y$10$hash1234', '0789012345', '18 place Kléber, Strasbourg', 'chauffeur', 47),
('maxime_bordeaux', 'maxime.simon@orange.fr', '$2y$10$hash1234', '0890123456', '25 place de la Bourse, Bordeaux', 'chauffeur', 36),
('clara_lille', 'clara.laurent@gmail.com', '$2y$10$hash1234', '0901234567', '30 Grand Place, Lille', 'chauffeur', 44),
('antoine_rennes', 'antoine.michel@gmail.com', '$2y$10$hash1234', '0123456780', '14 place du Parlement, Rennes', 'chauffeur', 31),
('emma_montpellier', 'emma.garcia@gmail.com', '$2y$10$hash1234', '0234567891', '20 place de la Comédie, Montpellier', 'chauffeur', 39),
('lucas_reims', 'lucas.lopez@gmail.com', '$2y$10$hash1234', '0345678902', '8 place Drouet d''Erlon, Reims', 'chauffeur', 42),
('manon_nancy', 'manon.gonzalez@gmail.com', '$2y$10$hash1234', '0456789013', '15 place Stanislas, Nancy', 'chauffeur', 35),
('hugo_dijon', 'hugo.rodriguez@gmail.com', '$2y$10$hash1234', '0567890124', '22 place de la Libération, Dijon', 'chauffeur', 48),
('chloe_tours', 'chloe.martinez@gmail.com', '$2y$10$hash1234', '0678901235', '12 place Plumereau, Tours', 'chauffeur', 37),
('paul_grenoble', 'paul.davis@gmail.com', '$2y$10$hash1234', '0789012346', '18 place Victor Hugo, Grenoble', 'chauffeur', 43),
('alice_angers', 'alice.wilson@gmail.com', '$2y$10$hash1234', '0890123457', '25 place du Ralliement, Angers', 'chauffeur', 32),
('romain_clermont', 'romain.anderson@gmail.com', '$2y$10$hash1234', '0901234568', '30 place de Jaude, Clermont-Ferrand', 'chauffeur', 46),
('julie_orleans', 'julie.taylor@gmail.com', '$2y$10$hash1234', '0123456781', '14 place du Martroi, Orléans', 'chauffeur', 34),
('nicolas_caen', 'nicolas.thomas@gmail.com', '$2y$10$hash1234', '0234567892', '20 place Saint-Pierre, Caen', 'chauffeur', 40),
('sarah_brest', 'sarah.jackson@gmail.com', '$2y$10$hash1234', '0345678903', '8 rue de Siam, Brest', 'chauffeur', 45),
('kevin_amiens', 'kevin.white@gmail.com', '$2y$10$hash1234', '0456789014', '15 place Gambetta, Amiens', 'chauffeur', 38),
('laura_limoges', 'laura.harris@gmail.com', '$2y$10$hash1234', '0567890125', '22 place de la République, Limoges', 'chauffeur', 41),
('david_perpignan', 'david.martin2@gmail.com', '$2y$10$hash1234', '0678901236', '12 place Arago, Perpignan', 'chauffeur', 33),
('marine_metz', 'marine.garcia2@gmail.com', '$2y$10$hash1234', '0789012347', '18 place Saint-Louis, Metz', 'chauffeur', 47),
('theo_besancon', 'theo.rodriguez2@gmail.com', '$2y$10$hash1234', '0890123458', '25 place de la Révolution, Besançon', 'chauffeur', 36),
('lea_poitiers', 'lea.lopez2@gmail.com', '$2y$10$hash1234', '0901234569', '30 place du Maréchal Leclerc, Poitiers', 'chauffeur', 44),
('quentin_troyes', 'quentin.wilson2@gmail.com', '$2y$10$hash1234', '0123456782', '14 place Alexandre Israël, Troyes', 'chauffeur', 31),
('camille_pau', 'camille.anderson2@gmail.com', '$2y$10$hash1234', '0234567893', '20 place Royale, Pau', 'chauffeur', 39),
('arthur_lorient', 'arthur.taylor2@gmail.com', '$2y$10$hash1234', '0345678904', '8 place Aristide Briand, Lorient', 'chauffeur', 42),
('manon_bayonne', 'manon.thomas2@gmail.com', '$2y$10$hash1234', '0456789015', '15 place de la Liberté, Bayonne', 'chauffeur', 35),
('louis_colmar', 'louis.jackson2@gmail.com', '$2y$10$hash1234', '0567890126', '22 place Rapp, Colmar', 'chauffeur', 48),
('eva_chambery', 'eva.white2@gmail.com', '$2y$10$hash1234', '0678901237', '12 place Saint-Léger, Chambéry', 'chauffeur', 37),
('nathan_ajaccio', 'nathan.harris2@gmail.com', '$2y$10$hash1234', '0789012348', '18 cours Napoléon, Ajaccio', 'chauffeur', 43),
('celia_avignon', 'celia.clark@gmail.com', '$2y$10$hash1234', '0890123459', '25 place de l''Horloge, Avignon', 'chauffeur', 32),

-- Passagers (15 passagers)
('alex_pass1', 'alex.pass1@gmail.com', '$2y$10$hash1234', '0601234567', '45 rue du Faubourg, Paris', 'passager', 25),
('julie_pass2', 'julie.pass2@gmail.com', '$2y$10$hash1234', '0612345678', '30 avenue Victor Hugo, Lyon', 'passager', 18),
('marc_pass3', 'marc.pass3@gmail.com', '$2y$10$hash1234', '0623456789', '12 boulevard Longchamp, Marseille', 'passager', 22),
('anna_pass4', 'anna.pass4@gmail.com', '$2y$10$hash1234', '0634567890', '8 rue Alsace Lorraine, Toulouse', 'passager', 16),
('tom_pass5', 'tom.pass5@gmail.com', '$2y$10$hash1234', '0645678901', '20 avenue Jean Médecin, Nice', 'passager', 27),
('lisa_pass6', 'lisa.pass6@gmail.com', '$2y$10$hash1234', '0656789012', '15 rue Crébillon, Nantes', 'passager', 19),
('paul_pass7', 'paul.pass7@gmail.com', '$2y$10$hash1234', '0667890123', '25 rue du Dôme, Strasbourg', 'passager', 21),
('claire_pass8', 'claire.pass8@gmail.com', '$2y$10$hash1234', '0678901234', '18 cours de l''Intendance, Bordeaux', 'passager', 24),
('jean_pass9', 'jean.pass9@gmail.com', '$2y$10$hash1234', '0689012345', '22 rue de Béthune, Lille', 'passager', 17),
('marie_pass10', 'marie.pass10@gmail.com', '$2y$10$hash1234', '0690123456', '10 rue Saint-Georges, Rennes', 'passager', 26),
('simon_pass11', 'simon.pass11@gmail.com', '$2y$10$hash1234', '0601234568', '35 rue de la Loge, Montpellier', 'passager', 20),
('emma_pass12', 'emma.pass12@gmail.com', '$2y$10$hash1234', '0612345679', '28 place d''Erlon, Reims', 'passager', 23),
('luc_pass13', 'luc.pass13@gmail.com', '$2y$10$hash1234', '0623456780', '14 rue Saint-Dizier, Nancy', 'passager', 15),
('zoe_pass14', 'zoe.pass14@gmail.com', '$2y$10$hash1234', '0634567891', '40 rue de la Liberté, Dijon', 'passager', 28),
('ryan_pass15', 'ryan.pass15@gmail.com', '$2y$10$hash1234', '0645678902', '16 place Jean Jaurès, Tours', 'passager', 14);

-- ÉTAPE 3: Insertion des véhicules (un par chauffeur)
INSERT INTO vehicule (utilisateur_id, marque, modele, couleur, plaque_immatriculation, date_premiere_immatriculation, nombre_places, energie) VALUES
-- Les IDs commencent à 2 car admin=1
(2, 'Peugeot', '208', 'Blanche', 'AA-123-BB', '2019-03-15', 4, 'essence'),
(3, 'Renault', 'Clio', 'Rouge', 'BB-456-CC', '2018-07-22', 4, 'essence'),
(4, 'Citroën', 'C3', 'Bleue', 'CC-789-DD', '2020-01-10', 4, 'essence'),
(5, 'Volkswagen', 'Polo', 'Noire', 'DD-012-EE', '2017-11-05', 4, 'essence'),
(6, 'Ford', 'Fiesta', 'Grise', 'EE-345-FF', '2019-09-18', 4, 'essence'),
(7, 'Opel', 'Corsa', 'Blanche', 'FF-678-GG', '2018-04-12', 4, 'essence'),
(8, 'Fiat', '500', 'Rouge', 'GG-901-HH', '2020-06-30', 4, 'essence'),
-- Diesel
(9, 'Peugeot', '308', 'Grise', 'HH-234-II', '2017-12-08', 4, 'diesel'),
(10, 'Renault', 'Megane', 'Bleue', 'II-567-JJ', '2018-08-25', 4, 'diesel'),
(11, 'Citroën', 'C4', 'Noire', 'JJ-890-KK', '2019-02-14', 4, 'diesel'),
(12, 'Volkswagen', 'Golf', 'Blanche', 'KK-123-LL', '2016-10-03', 4, 'diesel'),
(13, 'Audi', 'A3', 'Grise', 'LL-456-MM', '2018-05-17', 4, 'diesel'),
(14, 'BMW', 'Serie 1', 'Noire', 'MM-789-NN', '2017-09-21', 4, 'diesel'),
(15, 'Mercedes', 'Classe A', 'Blanche', 'NN-012-OO', '2019-01-09', 4, 'diesel'),
-- Hybride
(16, 'Toyota', 'Prius', 'Blanche', 'OO-345-PP', '2019-11-28', 4, 'hybride'),
(17, 'Toyota', 'Yaris', 'Grise', 'PP-678-QQ', '2020-04-16', 4, 'hybride'),
(18, 'Honda', 'Jazz', 'Bleue', 'QQ-901-RR', '2018-12-07', 4, 'hybride'),
(19, 'Lexus', 'CT', 'Noire', 'RR-234-SS', '2017-08-19', 4, 'hybride'),
(20, 'Toyota', 'Corolla', 'Rouge', 'SS-567-TT', '2019-06-11', 4, 'hybride'),
(21, 'Hyundai', 'Ioniq', 'Blanche', 'TT-890-UU', '2018-03-26', 4, 'hybride'),
-- Électrique
(22, 'Renault', 'Zoe', 'Blanche', 'UU-123-VV', '2020-07-14', 4, 'electrique'),
(23, 'Nissan', 'Leaf', 'Bleue', 'VV-456-WW', '2019-10-02', 4, 'electrique'),
(24, 'BMW', 'i3', 'Grise', 'WW-789-XX', '2018-01-23', 4, 'electrique'),
(25, 'Tesla', 'Model 3', 'Rouge', 'XX-012-YY', '2020-09-05', 4, 'electrique'),
(26, 'Peugeot', 'e-208', 'Noire', 'YY-345-ZZ', '2019-12-18', 4, 'electrique'),
-- Plus d'essence
(27, 'Seat', 'Ibiza', 'Rouge', 'ZZ-678-AA', '2018-06-29', 4, 'essence'),
(28, 'Skoda', 'Fabia', 'Blanche', 'AA-901-BB', '2017-04-11', 4, 'essence'),
(29, 'Dacia', 'Sandero', 'Grise', 'BB-234-CC', '2019-08-07', 4, 'essence'),
(30, 'Hyundai', 'i20', 'Bleue', 'CC-567-DD', '2018-11-22', 4, 'essence'),
(31, 'Kia', 'Rio', 'Noire', 'DD-890-EE', '2020-03-15', 4, 'essence'),
(32, 'Mazda', '2', 'Rouge', 'EE-123-FF', '2017-07-08', 4, 'essence'),
(33, 'Suzuki', 'Swift', 'Blanche', 'FF-456-GG', '2019-05-31', 4, 'essence'),
(34, 'Mini', 'Cooper', 'Grise', 'GG-789-HH', '2018-09-13', 4, 'essence'),
(35, 'Smart', 'ForTwo', 'Rouge', 'HH-012-II', '2020-02-06', 2, 'essence'),
(36, 'Alfa Romeo', 'Giulietta', 'Noire', 'II-345-JJ', '2017-12-24', 4, 'essence');

-- ÉTAPE 4: Covoiturages variés (50+ trajets)
INSERT INTO covoiturage (chauffeur_id, vehicule_id, ville_depart, ville_arrivee, date_depart, heure_depart, heure_arrivee, prix, places_disponibles, statut) VALUES
-- Trajets longue distance
(2, 1, 'Paris', 'Lyon', '2026-06-15', '07:00:00', '11:30:00', 35.00, 3, 'prevu'),
(3, 2, 'Lyon', 'Marseille', '2026-06-16', '08:30:00', '11:15:00', 28.00, 3, 'prevu'),
(4, 3, 'Marseille', 'Toulouse', '2026-06-17', '09:00:00', '13:45:00', 42.00, 3, 'prevu'),
(5, 4, 'Toulouse', 'Bordeaux', '2026-06-18', '14:00:00', '16:30:00', 25.00, 3, 'prevu'),
(6, 5, 'Bordeaux', 'Nantes', '2026-06-19', '10:30:00', '14:00:00', 38.00, 3, 'prevu'),
(7, 6, 'Nantes', 'Rennes', '2026-06-20', '16:15:00', '17:30:00', 15.00, 3, 'prevu'),
(8, 7, 'Rennes', 'Lille', '2026-06-21', '06:45:00', '11:30:00', 45.00, 3, 'prevu'),
(9, 8, 'Lille', 'Strasbourg', '2026-06-22', '08:00:00', '13:15:00', 48.00, 3, 'prevu'),
(10, 9, 'Strasbourg', 'Nancy', '2026-06-23', '18:30:00', '20:00:00', 18.00, 3, 'prevu'),
(11, 10, 'Nancy', 'Reims', '2026-06-24', '07:30:00', '09:45:00', 22.00, 3, 'prevu'),

-- Paris vers autres villes
(12, 11, 'Paris', 'Marseille', '2026-06-25', '05:30:00', '13:45:00', 65.00, 3, 'prevu'),
(13, 12, 'Paris', 'Toulouse', '2026-06-26', '06:00:00', '12:30:00', 58.00, 3, 'prevu'),
(14, 13, 'Paris', 'Nice', '2026-06-27', '07:15:00', '16:00:00', 72.00, 3, 'prevu'),
(15, 14, 'Paris', 'Bordeaux', '2026-06-28', '08:30:00', '14:15:00', 52.00, 3, 'prevu'),
(16, 15, 'Paris', 'Nantes', '2026-06-29', '09:45:00', '13:30:00', 38.00, 3, 'prevu'),
(17, 16, 'Paris', 'Strasbourg', '2026-06-30', '10:00:00', '14:45:00', 45.00, 3, 'prevu'),
(18, 17, 'Paris', 'Montpellier', '2026-07-01', '11:15:00', '18:30:00', 62.00, 3, 'prevu'),
(19, 18, 'Paris', 'Grenoble', '2026-07-02', '12:30:00', '17:15:00', 48.00, 3, 'prevu'),
(20, 19, 'Paris', 'Dijon', '2026-07-03', '13:45:00', '17:00:00', 35.00, 3, 'prevu'),

-- Trajets retour
(2, 1, 'Lyon', 'Paris', '2026-07-04', '14:30:00', '19:00:00', 35.00, 3, 'prevu'),
(3, 2, 'Marseille', 'Lyon', '2026-07-05', '15:15:00', '18:00:00', 28.00, 3, 'prevu'),
(4, 3, 'Toulouse', 'Marseille', '2026-07-06', '16:00:00', '20:45:00', 42.00, 3, 'prevu'),
(5, 4, 'Bordeaux', 'Toulouse', '2026-07-07', '17:30:00', '20:00:00', 25.00, 3, 'prevu'),
(6, 5, 'Nantes', 'Bordeaux', '2026-07-08', '18:45:00', '22:15:00', 38.00, 3, 'prevu'),

-- Trajets moyennes distances
(21, 20, 'Lyon', 'Grenoble', '2026-07-09', '07:00:00', '08:30:00', 18.00, 3, 'prevu'),
(22, 21, 'Marseille', 'Nice', '2026-07-10', '08:15:00', '10:45:00', 22.00, 3, 'prevu'),
(23, 22, 'Toulouse', 'Montpellier', '2026-07-11', '09:30:00', '12:00:00', 28.00, 3, 'prevu'),
(24, 23, 'Bordeaux', 'Pau', '2026-07-12', '10:45:00', '12:15:00', 16.00, 3, 'prevu'),
(25, 24, 'Nantes', 'Angers', '2026-07-13', '11:00:00', '12:00:00', 12.00, 3, 'prevu'),
(26, 25, 'Rennes', 'Brest', '2026-07-14', '12:15:00', '14:45:00', 25.00, 3, 'prevu'),
(27, 26, 'Lille', 'Amiens', '2026-07-15', '13:30:00', '14:45:00', 14.00, 3, 'prevu'),
(28, 27, 'Strasbourg', 'Colmar', '2026-07-16', '14:45:00', '15:30:00', 8.00, 3, 'prevu'),
(29, 28, 'Nancy', 'Metz', '2026-07-17', '15:00:00', '16:00:00', 10.00, 3, 'prevu'),
(30, 29, 'Reims', 'Troyes', '2026-07-18', '16:15:00', '17:30:00', 13.00, 3, 'prevu'),

-- Trajets courts
(31, 30, 'Dijon', 'Besançon', '2026-07-19', '17:30:00', '19:00:00', 15.00, 3, 'prevu'),
(32, 31, 'Tours', 'Orléans', '2026-07-20', '18:45:00', '20:00:00', 12.00, 3, 'prevu'),
(33, 32, 'Clermont-Ferrand', 'Limoges', '2026-07-21', '19:00:00', '21:15:00', 22.00, 3, 'prevu'),
(34, 33, 'Caen', 'Rouen', '2026-07-22', '07:15:00', '09:30:00', 18.00, 3, 'prevu'),
(35, 34, 'Brest', 'Lorient', '2026-07-23', '08:30:00', '10:00:00', 14.00, 1, 'prevu'),
(36, 35, 'Amiens', 'Reims', '2026-07-24', '09:45:00', '11:15:00', 16.00, 3, 'prevu');

-- ÉTAPE 5: Réservations (40+ réservations)
INSERT INTO reservation (passager_id, covoiturage_id, statut) VALUES
-- Réservations pour les premiers trajets
(37, 1, 'confirmee'), (38, 1, 'confirmee'),
(39, 2, 'confirmee'), (40, 2, 'confirmee'),
(41, 3, 'confirmee'), (42, 3, 'confirmee'),
(43, 4, 'confirmee'), (44, 4, 'confirmee'),
(45, 5, 'confirmee'), (46, 5, 'confirmee'),
(47, 6, 'confirmee'), (48, 6, 'confirmee'),
(49, 7, 'confirmee'), (50, 7, 'confirmee'),
(51, 8, 'confirmee'), (37, 8, 'confirmee'),
(38, 9, 'confirmee'), (39, 9, 'confirmee'),
(40, 10, 'confirmee'), (41, 10, 'confirmee'),
(42, 11, 'confirmee'), (43, 11, 'confirmee'),
(44, 12, 'confirmee'), (45, 12, 'confirmee'),
(46, 13, 'confirmee'), (47, 13, 'confirmee'),
(48, 14, 'confirmee'), (49, 14, 'confirmee'),
(50, 15, 'confirmee'), (51, 15, 'confirmee'),
(37, 16, 'confirmee'), (38, 16, 'confirmee'),
(39, 17, 'confirmee'), (40, 17, 'confirmee'),
(41, 18, 'confirmee'), (42, 18, 'confirmee'),
(43, 19, 'confirmee'), (44, 19, 'confirmee'),
(45, 20, 'confirmee'), (46, 20, 'confirmee'),
-- Quelques annulations
(47, 21, 'annulee'), (48, 22, 'annulee');

-- ÉTAPE 6: Avis authentiques (40+ avis)
INSERT INTO avis (evaluateur_id, evalue_id, covoiturage_id, note, commentaire, valide) VALUES
-- Avis positifs (notes 4-5)
(37, 2, 1, 5, 'Excellent chauffeur, très ponctuel et voiture impeccable !', TRUE),
(38, 2, 1, 4, 'Trajet agréable, bonne conduite, je recommande.', TRUE),
(2, 37, 1, 5, 'Passager très sympa et respectueux, merci !', TRUE),
(2, 38, 1, 4, 'RAS, passager discret et poli.', TRUE),
(39, 3, 2, 4, 'Bon voyage, conductrice prudente et à l''heure.', TRUE),
(40, 3, 2, 5, 'Super trajet, très bonne ambiance dans la voiture !', TRUE),
(3, 39, 2, 4, 'Passager ponctuel, aucun souci.', TRUE),
(3, 40, 2, 5, 'Excellente compagnie pour le trajet, très agréable !', TRUE),
(41, 4, 3, 3, 'Trajet correct mais un peu de retard au départ.', TRUE),
(42, 4, 3, 4, 'Bonne conduite, véhicule propre et confortable.', TRUE),
(4, 41, 3, 4, 'Passager sympa, bon échange durant le voyage.', TRUE),
(43, 5, 4, 5, 'Parfait ! Conduite souple et discussion intéressante.', TRUE),
(44, 5, 4, 4, 'Très bien, chauffeur accueillant et professionnel.', TRUE),
(5, 43, 4, 5, 'Super passager, très respectueux et ponctuel !', TRUE),
(45, 6, 5, 4, 'Trajet agréable, bonne ambiance générale.', TRUE),
(46, 6, 5, 3, 'Correct dans l''ensemble, quelques freinages brusques.', TRUE),
(6, 45, 5, 4, 'Passager discret et poli, parfait.', TRUE),
(47, 7, 6, 5, 'Excellent service, très professionnel !', TRUE),
(48, 7, 6, 4, 'Bon chauffeur, respect des horaires.', TRUE),
(7, 47, 6, 4, 'Passager agréable, bonne conversation.', TRUE),
(49, 8, 7, 2, 'Conduite un peu sportive à mon goût, sinon ça va.', TRUE),
(50, 8, 7, 3, 'Trajet correct mais musique un peu forte.', TRUE),
(8, 49, 7, 4, 'Passager sympa malgré quelques remarques.', TRUE),
(51, 9, 8, 4, 'Bonne conduite, véhicule récent et propre.', TRUE),
(37, 9, 8, 5, 'Parfait de A à Z, je recommande vivement !', TRUE),
(9, 51, 8, 5, 'Excellent passager, très respectueux.', TRUE),
(38, 10, 9, 3, 'Trajet correct, quelques embouteillages non anticipés.', TRUE),
(39, 10, 9, 4, 'Bon chauffeur, conduite prudente et sûre.', TRUE),
(10, 38, 9, 3, 'Passager correct mais un peu bavard.', TRUE),
(40, 11, 10, 5, 'Excellent trajet, chauffeur très sympa !', TRUE),
(41, 11, 10, 4, 'Très bien, ponctualité respectée.', TRUE),
(11, 40, 10, 5, 'Super passager, excellent compagnon de route !', TRUE),
-- Avis moyens (note 3)
(42, 12, 11, 3, 'Trajet correct, rien d''exceptionnel mais pas de souci.', TRUE),
(43, 12, 11, 3, 'Conduite un peu nerveuse mais on arrive à destination.', TRUE),
(12, 42, 11, 3, 'Passager correct, respect des règles.', TRUE),
(44, 13, 12, 3, 'Voyage standard, climatisation un peu faible.', TRUE),
(45, 13, 12, 4, 'Bon chauffeur dans l''ensemble, trajet agréable.', TRUE),
(13, 44, 12, 3, 'Passager discret, RAS.', TRUE),
(46, 14, 13, 2, 'Retard de 15 minutes sans prévenir, dommage.', TRUE),
(47, 14, 13, 3, 'Trajet correct une fois parti, conduite OK.', TRUE),
(14, 46, 13, 2, 'Passager un peu difficile, nombreuses demandes.', TRUE),
-- Plus d''avis variés
(48, 15, 14, 4, 'Très bon trajet, chauffeur expérimenté.', TRUE),
(49, 15, 14, 5, 'Parfait ! Voiture confortable et conduite excellente.', TRUE),
(15, 48, 14, 4, 'Passager agréable, bon voyage ensemble.', TRUE),
(50, 16, 15, 3, 'Correct, mais trajet un peu long à cause des bouchons.', TRUE),
(51, 16, 15, 4, 'Bon chauffeur, respect du code de la route.', TRUE),
(16, 50, 15, 4, 'Passager sympa, discussion intéressante.', TRUE),
(37, 17, 16, 5, 'Excellent ! Chauffeur très professionnel.', TRUE),
(38, 17, 16, 4, 'Très bien, conduite souple et sécurisée.', TRUE),
(17, 37, 16, 5, 'Super passager, très respectueux !', TRUE);

-- ÉTAPE 7: Préférences chauffeur
INSERT INTO preferences_chauffeur (chauffeur_id, accepte_fumeur, accepte_animaux, preferences_custom) VALUES
(2, FALSE, TRUE, 'Musique classique appréciée, pas de téléphone pendant la conduite'),
(3, FALSE, FALSE, 'Voyage en silence ou discussion légère, ponctualité exigée'),
(4, TRUE, TRUE, 'Ambiance décontractée, partage des frais d''autoroute si nécessaire'),
(5, FALSE, TRUE, 'Préfère les trajets matinaux, discussion sur l''écologie bienvenue'),
(6, FALSE, FALSE, 'Conduite prudente privilégiée, pas de nourriture dans le véhicule'),
(7, TRUE, FALSE, 'Flexible sur les horaires, aime la bonne ambiance'),
(8, FALSE, TRUE, 'Véhicule non-fumeur strict, animaux de petite taille OK'),
(9, FALSE, FALSE, 'Trajet professionnel, discrétion appréciée'),
(10, TRUE, TRUE, 'Très ouvert, tous profils bienvenus'),
(11, FALSE, FALSE, 'Préfère voyager avec des femmes, sécurité avant tout'),
(12, FALSE, TRUE, 'Passionné d''automobile, aime discuter mécanique'),
(13, TRUE, FALSE, 'Fumeur occasionnel, pas d''animaux par allergie'),
(14, FALSE, TRUE, 'Conduite écologique, sensible à l''environnement'),
(15, FALSE, FALSE, 'Trajet détente, musique zen appréciée'),
(16, FALSE, TRUE, 'Conduite hybride économique, discussions environnement'),
(17, FALSE, FALSE, 'Préfère les trajets courts, conduite urbaine'),
(18, TRUE, TRUE, 'Très sociable, aime faire des rencontres'),
(19, FALSE, TRUE, 'Conduite sportive mais sécurisée'),
(20, FALSE, FALSE, 'Trajets familiaux, ambiance calme privilégiée');

-- ÉTAPE 8: Mise à jour des places disponibles
UPDATE covoiturage SET places_disponibles = places_disponibles - 2 WHERE id BETWEEN 1 AND 20;
UPDATE covoiturage SET places_disponibles = places_disponibles - 1 WHERE id = 35; -- Smart ForTwo

-- ÉTAPE 9: Statistiques finales
SELECT 'RÉSUMÉ DES DONNÉES INSÉRÉES' as '=== ECORIDE STATS ===';
SELECT 'Utilisateurs total:' as Categorie, COUNT(*) as Nombre FROM utilisateur;
SELECT 'Chauffeurs:' as Categorie, COUNT(*) as Nombre FROM utilisateur WHERE statut = 'chauffeur';
SELECT 'Passagers:' as Categorie, COUNT(*) as Nombre FROM utilisateur WHERE statut = 'passager';
SELECT 'Véhicules:' as Categorie, COUNT(*) as Nombre FROM vehicule;
SELECT 'Covoiturages:' as Categorie, COUNT(*) as Nombre FROM covoiturage;
SELECT 'Réservations:' as Categorie, COUNT(*) as Nombre FROM reservation;
SELECT 'Avis:' as Categorie, COUNT(*) as Nombre FROM avis;
SELECT 'Préférences:' as Categorie, COUNT(*) as Nombre FROM preferences_chauffeur;

SELECT 'RÉPARTITION DES VÉHICULES PAR ÉNERGIE' as '=== VÉHICULES ===';
SELECT energie as Type, COUNT(*) as Nombre FROM vehicule GROUP BY energie;

SELECT 'TRAJETS LES PLUS CHERS' as '=== TOP PRIX ===';
SELECT CONCAT(ville_depart, ' → ', ville_arrivee) as Trajet, prix as Prix FROM covoiturage ORDER BY prix DESC LIMIT 5;