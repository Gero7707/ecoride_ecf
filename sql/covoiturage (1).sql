-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 24 sep. 2025 à 10:59
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecoride`
--

-- --------------------------------------------------------

--
-- Structure de la table `covoiturage`
--

CREATE TABLE `covoiturage` (
  `id` int(11) NOT NULL,
  `chauffeur_id` int(11) NOT NULL,
  `vehicule_id` int(11) NOT NULL,
  `ville_depart` varchar(100) NOT NULL,
  `ville_arrivee` varchar(100) NOT NULL,
  `date_depart` date NOT NULL,
  `heure_depart` time NOT NULL,
  `heure_arrivee` time DEFAULT NULL,
  `prix` decimal(5,2) NOT NULL,
  `places_disponibles` int(11) NOT NULL,
  `statut` enum('prevu','en_cours','termine','annule') DEFAULT 'prevu',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `covoiturage`
--

INSERT INTO `covoiturage` (`id`, `chauffeur_id`, `vehicule_id`, `ville_depart`, `ville_arrivee`, `date_depart`, `heure_depart`, `heure_arrivee`, `prix`, `places_disponibles`, `statut`, `date_creation`) VALUES
(6, 1001, 25, 'Paris', 'Lyon', '2025-09-25', '08:00:00', '12:30:00', 35.00, 3, 'prevu', '2025-09-24 08:49:05'),
(7, 1001, 26, 'Paris', 'Lille', '2025-09-26', '14:00:00', '17:00:00', 28.00, 2, 'prevu', '2025-09-24 08:49:05'),
(8, 1002, 27, 'Paris', 'Bordeaux', '2025-09-27', '07:30:00', '13:00:00', 45.00, 3, 'prevu', '2025-09-24 08:49:05'),
(9, 1003, 28, 'Paris', 'Marseille', '2025-09-28', '06:00:00', '14:00:00', 55.00, 1, 'prevu', '2025-09-24 08:49:05'),
(10, 1004, 29, 'Paris', 'Toulouse', '2025-09-29', '09:00:00', '16:00:00', 48.00, 2, 'prevu', '2025-09-24 08:49:05'),
(11, 1002, 30, 'Lyon', 'Marseille', '2025-09-25', '15:00:00', '19:00:00', 32.00, 4, 'prevu', '2025-09-24 08:49:05'),
(12, 1003, 31, 'Lyon', 'Paris', '2025-09-26', '07:00:00', '11:30:00', 35.00, 3, 'prevu', '2025-09-24 08:49:05'),
(13, 1005, 32, 'Lyon', 'Genève', '2025-09-27', '10:00:00', '12:30:00', 25.00, 2, 'prevu', '2025-09-24 08:49:05'),
(14, 1006, 33, 'Lyon', 'Montpellier', '2025-09-28', '13:00:00', '16:30:00', 38.00, 5, 'prevu', '2025-09-24 08:49:05'),
(15, 1007, 34, 'Lyon', 'Grenoble', '2025-09-29', '18:00:00', '20:00:00', 18.00, 3, 'prevu', '2025-09-24 08:49:05'),
(16, 1004, 35, 'Marseille', 'Nice', '2025-09-25', '09:30:00', '12:00:00', 22.00, 3, 'prevu', '2025-09-24 08:49:05'),
(17, 1005, 36, 'Marseille', 'Lyon', '2025-09-26', '16:00:00', '20:00:00', 32.00, 4, 'prevu', '2025-09-24 08:49:05'),
(18, 1006, 37, 'Marseille', 'Toulouse', '2025-09-27', '08:00:00', '12:30:00', 42.00, 2, 'prevu', '2025-09-24 08:49:05'),
(19, 1007, 38, 'Marseille', 'Montpellier', '2025-09-28', '17:30:00', '19:30:00', 15.00, 3, 'prevu', '2025-09-24 08:49:05'),
(20, 1008, 39, 'Marseille', 'Avignon', '2025-09-29', '11:00:00', '12:15:00', 12.00, 4, 'prevu', '2025-09-24 08:49:05'),
(21, 1008, 40, 'Bordeaux', 'Paris', '2025-09-25', '06:30:00', '12:00:00', 48.00, 3, 'prevu', '2025-09-24 08:49:05'),
(22, 1009, 41, 'Bordeaux', 'Toulouse', '2025-09-26', '14:00:00', '16:30:00', 28.00, 4, 'prevu', '2025-09-24 08:49:05'),
(23, 1010, 42, 'Bordeaux', 'Nantes', '2025-09-27', '10:00:00', '13:30:00', 35.00, 2, 'prevu', '2025-09-24 08:49:05'),
(24, 1001, 43, 'Bordeaux', 'Lyon', '2025-09-28', '07:00:00', '13:00:00', 45.00, 5, 'prevu', '2025-09-24 08:49:05'),
(25, 1002, 44, 'Bordeaux', 'La Rochelle', '2025-09-29', '15:30:00', '17:30:00', 20.00, 3, 'prevu', '2025-09-24 08:49:05'),
(26, 1003, 25, 'Toulouse', 'Montpellier', '2025-09-30', '12:00:00', '14:30:00', 25.00, 3, 'prevu', '2025-09-24 08:49:05'),
(27, 1004, 26, 'Toulouse', 'Bordeaux', '2025-10-01', '16:00:00', '18:30:00', 28.00, 2, 'prevu', '2025-09-24 08:49:05'),
(28, 1005, 27, 'Toulouse', 'Paris', '2025-10-02', '05:30:00', '12:30:00', 50.00, 4, 'prevu', '2025-09-24 08:49:05'),
(29, 1006, 28, 'Toulouse', 'Perpignan', '2025-10-03', '13:00:00', '15:00:00', 18.00, 3, 'prevu', '2025-09-24 08:49:05'),
(30, 1007, 29, 'Toulouse', 'Carcassonne', '2025-10-04', '19:00:00', '20:00:00', 8.00, 4, 'prevu', '2025-09-24 08:49:05'),
(31, 1008, 30, 'Lille', 'Paris', '2025-09-30', '17:00:00', '20:00:00', 28.00, 3, 'prevu', '2025-09-24 08:49:05'),
(32, 1009, 31, 'Nantes', 'Rennes', '2025-10-01', '10:30:00', '12:00:00', 15.00, 4, 'prevu', '2025-09-24 08:49:05'),
(33, 1010, 32, 'Strasbourg', 'Paris', '2025-10-02', '07:00:00', '11:30:00', 42.00, 2, 'prevu', '2025-09-24 08:49:05'),
(34, 1001, 33, 'Nice', 'Cannes', '2025-10-03', '10:00:00', '11:00:00', 8.00, 3, 'prevu', '2025-09-24 08:49:05'),
(35, 1002, 34, 'Montpellier', 'Nîmes', '2025-10-04', '15:30:00', '16:15:00', 6.00, 4, 'prevu', '2025-09-24 08:49:05'),
(36, 1003, 35, 'Clermont-Ferrand', 'Lyon', '2025-10-05', '12:00:00', '14:30:00', 28.00, 2, 'prevu', '2025-09-24 08:49:05'),
(37, 1004, 36, 'Dijon', 'Paris', '2025-10-06', '08:30:00', '12:00:00', 35.00, 3, 'prevu', '2025-09-24 08:49:05'),
(38, 1005, 37, 'Tours', 'Orléans', '2025-10-07', '17:00:00', '18:00:00', 12.00, 4, 'prevu', '2025-09-24 08:49:05');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `covoiturage`
--
ALTER TABLE `covoiturage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chauffeur_id` (`chauffeur_id`),
  ADD KEY `vehicule_id` (`vehicule_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `covoiturage`
--
ALTER TABLE `covoiturage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `covoiturage`
--
ALTER TABLE `covoiturage`
  ADD CONSTRAINT `covoiturage_ibfk_1` FOREIGN KEY (`chauffeur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `covoiturage_ibfk_2` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicule` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
