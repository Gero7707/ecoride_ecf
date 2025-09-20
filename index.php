<?php
/**
 * Point d'entrée principal de l'application EcoRide
 * Routeur simple pour gérer les pages
 */

// Démarrage de la session
session_start();

// Chargement de la configuration (commenté pour debug)
// require_once 'config/database.php';

// Récupération de l'URL demandée
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Routage simple
switch ($path) {
    case '/':
    case '/index.php':
        // Page d'accueil
        include 'app/views/home/index.php';
        break;
        
    case '/covoiturages':
        // Page de recherche de covoiturages
        include 'app/views/covoiturage/search.php';
        break;
        
    case '/connexion':
        // Page de connexion
        include 'app/views/auth/login.php';
        break;
        
    case '/inscription':
        // Page d'inscription
        include 'app/views/auth/register.php';
        break;
        
    default:
        // Page 404
        http_response_code(404);
        echo "<h1>Page non trouvée</h1>";
        break;
}
?>