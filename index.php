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
require_once 'app/controllers/AccountController.php';
require_once 'app/controllers/ReservationController.php';
require_once 'app/controllers/VehiculeController.php';

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

    case '/profil':
    case '/mon-compte':
        // Page de profil utilisateur
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }
        $accountController = new AccountController($pdo);
        $accountController->showProfile();
        break;
        
    case '/mon-compte/modifier':
    case '/profil/modifier':
        // Page de modification du profil
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }
        $accountController = new AccountController($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accountController->processUpdateProfile();
        } else {
            $accountController->showEditProfile();
        }
        break;
        
    case (preg_match('/^\/covoiturage\/(\d+)$/', $path, $matches) ? true : false):
        // Page détail d'un covoiturage
        $covoiturageController = new CovoiturageController($pdo);
        $covoiturageController->details($matches[1]);
        break;

    case '/covoiturage/creer':
    case '/covoiturage/proposer':
        // Créer un covoiturage
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour créer un covoiturage';
            header('Location: /connexion');
            exit();
        }
        
        if ($_SESSION['user_statut'] !== 'chauffeur') {
            $_SESSION['error'] = 'Vous devez être chauffeur pour créer un covoiturage';
            header('Location: /profil');
            exit();
        }
        
        $covoiturageController = new CovoiturageController($pdo);
        $covoiturageController->create();
        break;

    case '/reservation/creer':
        $reservationController = new ReservationController($pdo);
        $reservationController->createReservation();
        break;
        
    case '/reservation/annuler':
        $reservationController = new ReservationController($pdo);
        $reservationController->cancelReservation();
        break;

    case '/reservation/confirmer':
        $reservationController = new ReservationController($pdo);
        $reservationController->confirmReservation();
        break;

    case '/reservation/supprimer':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reservationController = new ReservationController($pdo);
            $reservationController->deleteReservation();
        }
        break;

    case '/vehicule/ajouter':
        // Ajouter un véhicule
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }
        
        if ($_SESSION['user_statut'] !== 'chauffeur') {
            $_SESSION['error'] = 'Vous devez être chauffeur pour ajouter un véhicule';
            header('Location: /profil');
            exit();
        }
        
        require_once 'app/controllers/VehiculeController.php';
        $vehiculeController = new VehiculeController($pdo);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $vehiculeController->processAdd();
        } else {
            $vehiculeController->showAddForm();
        }
        break;
    
    case (preg_match('#^/vehicule/supprimer/(\d+)$#', $path, $matches) ? true : false):
        // Supprimer un véhicule
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }
        
        $vehiculeController = new VehiculeController($pdo);
        $vehiculeController->delete($matches[1]);
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
        echo "<p>La page demandée n'existe pas.</p>";
        echo "<a href='/'>Retour à l'accueil</a>";
        break;
}
?>