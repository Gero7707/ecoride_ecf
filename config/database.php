<?php
/**
 * Configuration de la base de données
 * EcoRide - Application de covoiturage
 */

// Configuration base de données MySQL
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecoride');
define('DB_USER', 'root');
define('DB_PASS', ''); // Mot de passe MySQL (vide par défaut sur XAMPP)
define('DB_CHARSET', 'utf8');

// Configuration MongoDB sera ajoutée plus tard selon les besoins ECF

try {
    // Connexion PDO pour MySQL
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>