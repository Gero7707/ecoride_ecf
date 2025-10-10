<?php
/**
 * Contrôleur de gestion du compte utilisateur
 * Gère l'affichage et la modification du profil
 */
require_once 'app/models/UserModel.php';
require_once 'app/models/ReservationModel.php';

class AccountController {
    private $pdo;
    private $userModel;
    private $reservationModel;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new UserModel();
        $this->reservationModel = new ReservationModel();
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
            $data['mes_reservations'] = $this->reservationModel->getUserReservations($userId);
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
        
        $_SESSION['success'] = 'Profil mis à jour avec succès !';
        
        header('Location: /mon-compte');
        exit();
    }
    

/* Gérer l'upload de photo de profil avec compression
 */
private function handlePhotoUpload($file) {
    // Vérifications de sécurité
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    $profileMaxSize = 100 * 1024; // 100KB pour le fichier final
    $profileDimensions = [300, 300]; // Dimensions max pour avatar
    $jpegQuality = 85;
    
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'error' => 'Format de fichier non autorisé. Utilisez JPG, PNG, GIF ou WebP.'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'Le fichier est trop volumineux (max 5MB).'];
    }
    
    // Vérifier si c'est vraiment une image
    $imageInfo = getimagesize($file['tmp_name']);
    if (!$imageInfo) {
        return ['success' => false, 'error' => 'Le fichier n\'est pas une image valide.'];
    }
    
    // Créer le dossier s'il n'existe pas
    $uploadDir = 'public/uploads/avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    try {
        // Informations sur l'image originale
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Créer la ressource image selon le type
        $sourceImage = $this->createImageResource($file['tmp_name'], $mimeType);
        if (!$sourceImage) {
            return ['success' => false, 'error' => 'Impossible de traiter l\'image.'];
        }
        
        // Calculer les nouvelles dimensions (carré pour avatar)
        $newDimensions = $this->calculateAvatarDimensions($originalWidth, $originalHeight, $profileDimensions);
        
        // Créer l'image redimensionnée
        $resizedImage = imagecreatetruecolor($newDimensions['width'], $newDimensions['height']);
        
        // Préserver la transparence pour PNG et GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
            imagefill($resizedImage, 0, 0, $transparent);
        }
        
        // Redimensionner l'image
        imagecopyresampled(
            $resizedImage, $sourceImage,
            0, 0, 0, 0,
            $newDimensions['width'], $newDimensions['height'],
            $originalWidth, $originalHeight
        );
        
        // Générer un nom unique
        $fileName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.jpg'; // Forcer en JPEG pour compression
        $filePath = $uploadDir . $fileName;
        
        // Sauvegarder avec compression JPEG
        $saved = imagejpeg($resizedImage, $filePath, $jpegQuality);
        
        // Nettoyer la mémoire
        imagedestroy($sourceImage);
        imagedestroy($resizedImage);
        
        if (!$saved) {
            return ['success' => false, 'error' => 'Erreur lors de la sauvegarde.'];
        }
        
        // Vérifier la taille finale et compresser davantage si nécessaire
        $finalSize = filesize($filePath);
        if ($finalSize > $profileMaxSize) {
            $this->aggressiveCompress($filePath, $profileMaxSize);
            $finalSize = filesize($filePath);
        }
        
        // Supprimer l'ancien avatar s'il existe
        $this->removeOldAvatar($_SESSION['user_id'], $fileName);
        
        return [
            'success' => true, 
            'path' => $filePath,
            'fileName' => $fileName,
            'originalSize' => $file['size'],
            'finalSize' => $finalSize,
            'compressionRatio' => round((1 - ($finalSize / $file['size'])) * 100, 1),
            'dimensions' => $newDimensions
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Erreur technique : ' . $e->getMessage()];
    }
}

/**
 * Créer une ressource image selon le type MIME
 */
private function createImageResource($filePath, $mimeType) {
    switch ($mimeType) {
        case 'image/jpeg':
        case 'image/jpg':
            return imagecreatefromjpeg($filePath);
        case 'image/png':
            return imagecreatefrompng($filePath);
        case 'image/gif':
            return imagecreatefromgif($filePath);
        case 'image/webp':
            return imagecreatefromwebp($filePath);
        default:
            return false;
    }
}

/**
 * Calculer les dimensions pour un avatar (format carré centré)
 */
private function calculateAvatarDimensions($originalWidth, $originalHeight, $targetDimensions) {
    $targetSize = min($targetDimensions[0], $targetDimensions[1]); // Prendre la plus petite dimension pour un carré
    
    // Si l'image est déjà plus petite, la redimensionner quand même pour uniformiser
    if ($originalWidth <= $targetSize && $originalHeight <= $targetSize) {
        $size = max($originalWidth, $originalHeight); // Prendre la plus grande pour remplir le carré
        return ['width' => $size, 'height' => $size];
    }
    
    // Pour un avatar, on veut un carré, donc on prend la dimension cible
    return ['width' => $targetSize, 'height' => $targetSize];
}

/**
 * Compression agressive si le fichier dépasse encore la limite
 */
private function aggressiveCompress($filePath, $maxSize) {
    $currentSize = filesize($filePath);
    $quality = 85;
    
    while ($currentSize > $maxSize && $quality > 30) {
        $quality -= 10;
        
        // Recharger et recompresser
        $image = imagecreatefromjpeg($filePath);
        if ($image) {
            imagejpeg($image, $filePath, $quality);
            imagedestroy($image);
            $currentSize = filesize($filePath);
        } else {
            break; // Arrêter si on ne peut plus charger l'image
        }
    }
    
    return $currentSize;
}

/**
 * Supprimer l'ancien avatar de l'utilisateur
 */
private function removeOldAvatar($userId, $newFileName) {
    $uploadDir = 'public/uploads/avatars/';
    $pattern = $uploadDir . 'avatar_' . $userId . '_*';
    
    foreach (glob($pattern) as $oldFile) {
        $oldFileName = basename($oldFile);
        // Ne pas supprimer le nouveau fichier qu'on vient de créer
        if ($oldFileName !== $newFileName && is_file($oldFile)) {
            unlink($oldFile);
        }
    }
}

/**
 * Formater les octets en format lisible
 */
private function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
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