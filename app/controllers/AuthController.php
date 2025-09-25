<?php
/**
 * Contrôleur d'authentification
 * Gère les inscriptions et connexions
 */

require_once 'app/models/User.php';

class AuthController {
    private $pdo;
    private $userModel;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
    }
    
    /**
     * Afficher le formulaire de connexion
     */
    public function showLogin() {
        include 'app/views/auth/login.php';
    }
    
    /**
     * Traiter la connexion
     */
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /connexion');
            exit();
        }
        
        $identifier = $_POST['identifier'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($identifier) || empty($password)) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs';
            header('Location: /connexion');
            exit();
        }
        
        $result = $this->userModel->login($identifier, $password);
        
        if ($result['success']) {
            // Connexion réussie
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['user_pseudo'] = $result['user']['pseudo'];
            $_SESSION['user_statut'] = $result['user']['statut'];
            $_SESSION['user_credits'] = $result['user']['credits'];
            
            $_SESSION['success'] = 'Connexion réussie !';
            header('Location: /'); // Redirection vers accueil
            exit();
        } else {
            // Erreur de connexion
            $_SESSION['error'] = $result['error'];
            header('Location: /connexion');
            exit();
        }
    }
    
    /**
     * Afficher le formulaire d'inscription
     */
    public function showRegister() {
        include 'app/views/auth/register.php';
    }
    
    /**
     * Traiter l'inscription
     */
    public function processRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /inscription');
            exit();
        }
        
        $pseudo = $_POST['pseudo'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['mot_de_passe'] ?? '';
        $confirmPassword = $_POST['confirmer_mot_de_passe'] ?? '';
        $telephone = $_POST['telephone'] ?? null;
        
        // Validation côté serveur
        if (empty($pseudo) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs obligatoires';
            header('Location: /inscription');
            exit();
        }
        
        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Les mots de passe ne correspondent pas';
            header('Location: /inscription');
            exit();
        }
        
        $result = $this->userModel->create($pseudo, $email, $password, $telephone);
        
        if ($result['success']) {
            // Inscription réussie
            $_SESSION['success'] = 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.';
            header('Location: /connexion');
            exit();
        } else {
            // Erreur d'inscription
            $_SESSION['error'] = $result['error'];
            header('Location: /inscription');
            exit();
        }
    }
    
    /**
     * Déconnexion
     */
    public function logout() {
        session_destroy();
        header('Location: /');
        exit();
    }
}

?>