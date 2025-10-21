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
require_once 'app/controllers/MessageController.php';

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
    case '/contact':
        // Page d'accueil
        include 'app/views/contact/contact.php';
        break;
    case '/mentions-legales':
        // Page des mentions légales
        include 'app/views/contact/mentions-legales.php';
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

    case '/mes-avis':
        // Page de tous les avis
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }
        $accountController = new AccountController($pdo);
        $accountController->showAllReviews();
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

    case (preg_match('#^/utilisateur/(\d+)$#', $path, $matches) ? true : false):
        // Profil public d'un utilisateur
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté';
            header('Location: /connexion');
            exit();
        }
        require_once 'app/controllers/UserController.php';
        $userController = new UserController($pdo);
        $userController->showPublicProfile($matches[1]);
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

        case '/covoiturage/annuler':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $covoiturageController = new CovoiturageController($pdo);
            $covoiturageController->cancelCovoiturage();
        }
        break;
    
    case '/covoiturage/supprimer':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $covoiturageController = new CovoiturageController($pdo);
            $covoiturageController->deleteCovoiturage();
        }
        break;

    case (preg_match('#^/covoiturage/(\d+)/passagers$#', $path, $matches) ? true : false):
        // Liste des passagers d'un covoiturage (pour le chauffeur)
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté';
            header('Location: /connexion');
            exit();
        }
        $covoiturageController = new CovoiturageController($pdo);
        $covoiturageController->showPassengers($matches[1]);
        break;

    case '/reservation/refuser':
        $reservationController = new ReservationController($pdo);
        $reservationController->refuserReservation();
        break;

    case '/mes-covoiturages':
        // Liste de tous mes covoiturages (pour le chauffeur)
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté';
            header('Location: /connexion');
            exit();
        }

        if ($_SESSION['user_statut'] !== 'chauffeur' && $_SESSION['user_statut'] !== 'admin') {
            $_SESSION['error'] = 'Vous devez être chauffeur';
            header('Location: /profil');
            exit();
        }
    
        $covoiturageController = new CovoiturageController($pdo);
        $covoiturageController->mesCovoiturages();
        break;
        
    case '/messagerie':
        // Page principale de messagerie - Liste des conversations
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour accéder à la messagerie';
            header('Location: /connexion');
            exit();
        }
        $messageController = new MessageController($pdo);
        $messageController->index();
        break;

    case (preg_match('#^/messagerie/conversation/([a-f0-9]{24})$#', $path, $matches) ? true : false):
        // Afficher une conversation spécifique
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour accéder à la messagerie';
            header('Location: /connexion');
            exit();
        }
        $messageController = new MessageController($pdo);
        $messageController->conversation($matches[1]);
        break;

    case (preg_match('#^/messagerie/creer/(\d+)$#', $path, $matches) ? true : false):
        
        // Créer une conversation pour un trajet
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour créer une conversation';
            header('Location: /connexion');
            exit();
        }
        $messageController = new MessageController($pdo);
        $messageController->creerConversation($matches[1]);
        break;

    case '/messagerie/unread-count':
        // API - Compteur de messages non lus (pour notifications)
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Non connecté']);
            exit();
        }
        $messageController = new MessageController($pdo);
        $messageController->getUnreadCount();
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