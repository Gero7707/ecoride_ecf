<?php
/**
 * Point d'entrée principal de l'application EcoRide
 * Routeur simple pour gérer les pages
 */

// Démarrage de la session
session_start();

// Chargement de la configuration
require_once 'config/database.php';
require_once 'app/controllers/AuthController.php';
require_once 'app/controllers/CovoiturageController.php';

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
        $covoiturageController = new CovoiturageController($pdo);
        $covoiturageController->search();
        break;
        
    case (preg_match('/^\/covoiturage\/(\d+)$/', $path, $matches) ? true : false):
        // Page détail d'un covoiturage
        $covoiturageController = new CovoiturageController($pdo);
        $covoiturageController->details($matches[1]);
        break;
        
    case '/connexion':
        // Page de connexion
        $authController = new AuthController($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->processLogin();
        } else {
            $authController->showLogin();
        }
        break;
        
    case '/inscription':
        // Page d'inscription
        $authController = new AuthController($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->processRegister();
        } else {
            $authController->showRegister();
        }
        break;
        
    case '/deconnexion':
        // Déconnexion
        $authController = new AuthController($pdo);
        $authController->logout();
        break;
        
    default:
        // Page 404
        http_response_code(404);
        echo "<h1>Page non trouvée</h1>";
        break;
}
?>