<?php
/**
 * Contrôleur de gestion du compte utilisateur
 * Gère l'affichage et la modification du profil
 */

require_once 'app/models/UserModel.php';

class AccountController {
    private $pdo;
    private $userModel;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new UserModel();
    }
    
    /**
     * Afficher la page Mon Compte
     */
    public function showProfile() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        
        // Récupérer les données de l'utilisateur
        $user = $this->userModel->getUserById($userId);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur introuvable';
            header('Location: /');
            exit();
        }
        
        // Récupérer les données selon le statut
        $stats = $this->userModel->getUserStats($userId);
        $data = [
            'user' => $user,
            'vehicules' => $this->userModel->getUserVehicles($userId),
            'stats' => $stats
        ];

        $data['mes_covoiturages'] = [];
        $data['mes_reservations'] = [];
        $data['avis_recus'] = [];
        $data['avis_donnes'] = [];
        $data['preferences'] = null;
        
        if ($user['statut'] === 'chauffeur') {
            // Données spécifiques au chauffeur
            $data['mes_covoiturages'] = $this->userModel->getUserCovoiturages($userId);
            $data['avis_recus'] = $this->userModel->getReceivedReviews($userId);
            $data['preferences'] = $this->userModel->getChauffeurPreferences($userId);
        } else {
            // Données spécifiques au passager
            $data['mes_reservations'] = $this->userModel->getUserReservations($userId);
            $data['avis_donnes'] = $this->userModel->getGivenReviews($userId);
        }
        // Extraire les variables pour la vue
        extract($data);
        
        // Charger la vue
        include 'app/views/account/profile.php';
    }
    
    /**
     * Afficher le formulaire de modification du profil
     */
    public function showEditProfile() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur introuvable';
            header('Location: /mon-compte');
            exit();
        }
        
        $preferences = null;
        if ($user['statut'] === 'chauffeur') {
            $preferences = $this->userModel->getChauffeurPreferences($userId);
        }
        
        // Charger la vue
        include 'app/views/account/edit.php';
    }
    
    /**
     * Traiter la mise à jour du profil
     */
    public function processUpdateProfile() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /mon-compte/modifier');
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        $errors = [];
        
        // Validation des données
        $pseudo = trim($_POST['pseudo'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $statut = $_POST['statut'] ?? '';
        
        // Validation
        if (empty($pseudo)) {
            $errors[] = 'Le pseudo est requis';
        } elseif (!$this->userModel->isPseudoAvailable($pseudo, $userId)) {
            $errors[] = 'Ce pseudo est déjà utilisé';
        }
        
        if (empty($email)) {
            $errors[] = 'L\'email est requis';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format d\'email invalide';
        } elseif (!$this->userModel->isEmailAvailable($email, $userId)) {
            $errors[] = 'Cet email est déjà utilisé';
        }
        
        if (!empty($telephone) && !preg_match('/^[0-9+\-\.\s\(\)]{10,20}$/', $telephone)) {
            $errors[] = 'Format de téléphone invalide';
        }
        
        if (!in_array($statut, ['passager', 'chauffeur'])) {
            $errors[] = 'Statut invalide';
        }
        
        // Gestion du changement de mot de passe
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $changePassword = false;
        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                $errors[] = 'Mot de passe actuel requis pour changer le mot de passe';
            } elseif (!$this->userModel->verifyCurrentPassword($userId, $currentPassword)) {
                $errors[] = 'Mot de passe actuel incorrect';
            } elseif (strlen($newPassword) < 8) {
                $errors[] = 'Le nouveau mot de passe doit contenir au moins 8 caractères';
            } elseif ($newPassword !== $confirmPassword) {
                $errors[] = 'Les nouveaux mots de passe ne correspondent pas';
            } else {
                $changePassword = true;
            }
        }
        

        // Récupérer la photo actuelle de l'utilisateur
        $currentUser = $this->userModel->getUserById($userId);
        // Gestion de l'upload de photo
        $photoPath = $currentUser['photo'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoResult = $this->handlePhotoUpload($_FILES['photo']);
            if ($photoResult['success']) {
                $photoPath = $photoResult['path'];
            } else {
                $errors[] = $photoResult['error'];
            }
        }
        
        // S'il y a des erreurs, revenir au formulaire
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header('Location: /mon-compte/modifier');
            exit();
        }
        
        // Préparer les données à mettre à jour
        $updateData = [
            'pseudo' => $pseudo,
            'email' => $email,
            'telephone' => $telephone,
            'adresse' => $adresse,
            'photo' => $photoPath,
            'statut' => $statut
        ];
        
        // Mettre à jour le profil
        $updateResult = $this->userModel->updateUser($userId, $updateData);
        
        if (!$updateResult) {
            $_SESSION['error'] = 'Erreur lors de la mise à jour du profil';
            header('Location: /mon-compte/modifier');
            exit();
        }
        
        // Changer le mot de passe si demandé
        if ($changePassword) {
            $passwordResult = $this->userModel->updatePassword($userId, $newPassword);
            if (!$passwordResult) {
                $_SESSION['error'] = 'Profil mis à jour mais erreur lors du changement de mot de passe';
                header('Location: /mon-compte/modifier');
                exit();
            }
        }
        
        // Gestion des préférences chauffeur
        if ($statut === 'chauffeur') {
            $preferences = [
                'accepte_fumeur' => isset($_POST['accepte_fumeur']) ? 1 : 0,
                'accepte_animaux' => isset($_POST['accepte_animaux']) ? 1 : 0,
                'preferences_custom' => trim($_POST['preferences_custom'] ?? '')
            ];
            $this->userModel->updateChauffeurPreferences($userId, $preferences);
        }
        
        // Mettre à jour les données de session
        $_SESSION['user_pseudo'] = $pseudo;
        $_SESSION['user_statut'] = $statut;
        
        if ($statut === 'chauffeur') {
            $_SESSION['success'] = 'Profil mis à jour ! Vous êtes maintenant chauffeur et pouvez proposer des trajets.';
        } else {
            $_SESSION['success'] = 'Profil mis à jour ! Vous êtes maintenant passager.';
        }
        header('Location: /mon-compte');
        exit();
    }
    
    /**
     * Gérer l'upload de photo de profil
     */
    private function handlePhotoUpload($file) {
        // Vérifications de sécurité
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Format de fichier non autorisé. Utilisez JPG, PNG ou GIF.'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Le fichier est trop volumineux (max 5MB).'];
        }
        
        // Créer le dossier s'il n'existe pas
        $uploadDir = 'public/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Générer un nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
        $filePath = $uploadDir . $fileName;
        
        // Déplacer le fichier
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['success' => true, 'path' => $filePath];
        } else {
            return ['success' => false, 'error' => 'Erreur lors de l\'upload du fichier.'];
        }
    }
    
    /**
     * Supprimer la photo de profil
     */
    public function deletePhoto() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);
        
        if ($user && !empty($user['photo'])) {
            // Supprimer le fichier physique
            if (file_exists($user['photo'])) {
                unlink($user['photo']);
            }
            
            // Mettre à jour la base de données
            $updateData = [
                'pseudo' => $user['pseudo'],
                'email' => $user['email'],
                'telephone' => $user['telephone'],
                'adresse' => $user['adresse'],
                'photo' => null
            ];
            
            $this->userModel->updateUser($userId, $updateData);
            $_SESSION['success'] = 'Photo supprimée avec succès';
        }
        
        header('Location: /mon-compte/modifier');
        exit();
    }
    
    /**
     * Changer le statut (passager <-> chauffeur)
     */
    public function switchStatus() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);
        
        if ($user) {
            $newStatut = ($user['statut'] === 'chauffeur') ? 'passager' : 'chauffeur';
            
            $updateData = [
                'pseudo' => $user['pseudo'],
                'email' => $user['email'],
                'telephone' => $user['telephone'],
                'adresse' => $user['adresse'],
                'photo' => $user['photo']
            ];
            
            // Note: Tu devras ajouter une méthode updateStatut dans UserModel pour changer juste le statut
            // ou modifier updateUser pour inclure le statut
            
            $_SESSION['user_statut'] = $newStatut;
            $_SESSION['success'] = 'Statut changé en ' . $newStatut;
        }
        
        header('Location: /mon-compte');
        exit();
    }
}
?>