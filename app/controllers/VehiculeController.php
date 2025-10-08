<?php
/**
 * Contrôleur de gestion des véhicules
 * Gère l'ajout, modification et suppression des véhicules
 */

require_once 'app/models/VehiculeModel.php';

class VehiculeController {
    private $pdo;
    private $vehiculeModel;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->vehiculeModel = new VehiculeModel();
    }
    
    /**
     * Afficher le formulaire d'ajout de véhicule
     */
    public function showAddForm() {
        // Vérifier si l'utilisateur est connecté et est chauffeur
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté';
            header('Location: /connexion');
            exit();
        }
        
        if ($_SESSION['user_statut'] !== 'chauffeur') {
            $_SESSION['error'] = 'Vous devez être chauffeur pour ajouter un véhicule';
            header('Location: /profil');
            exit();
        }
        
        // Charger la vue
        include 'app/views/vehicule/add.php';
    }
    
    /**
     * Traiter l'ajout d'un véhicule
     */
    public function processAdd() {
        // Vérifications
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /vehicule/ajouter');
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        $errors = [];
        
        // Récupération et validation des données
        $marque = trim($_POST['marque'] ?? '');
        $modele = trim($_POST['modele'] ?? '');
        $couleur = trim($_POST['couleur'] ?? '');
        $plaque = strtoupper(trim($_POST['plaque_immatriculation'] ?? ''));
        $dateImmat = $_POST['date_premiere_immatriculation'] ?? '';
        $places = intval($_POST['nombre_places'] ?? 0);
        $energie = $_POST['energie'] ?? '';
        
        // Validation
        if (empty($marque)) {
            $errors[] = 'La marque est obligatoire';
        }
        
        if (empty($modele)) {
            $errors[] = 'Le modèle est obligatoire';
        }
        
        if (empty($plaque)) {
            $errors[] = 'La plaque d\'immatriculation est obligatoire';
        } elseif (!$this->validatePlaque($plaque)) {
            $errors[] = 'Format de plaque d\'immatriculation invalide';
        } elseif ($this->vehiculeModel->plaqueExists($plaque, $userId)) {
            $errors[] = 'Cette plaque d\'immatriculation est déjà enregistrée';
        }
        
        if (empty($dateImmat)) {
            $errors[] = 'La date de première immatriculation est obligatoire';
        } elseif (strtotime($dateImmat) > time()) {
            $errors[] = 'La date d\'immatriculation ne peut pas être dans le futur';
        }
        
        if ($places < 1 || $places > 8) {
            $errors[] = 'Le nombre de places doit être entre 1 et 8';
        }
        
        if (!in_array($energie, ['essence', 'diesel', 'electrique', 'hybride'])) {
            $errors[] = 'Type d\'énergie invalide';
        }
        
        // S'il y a des erreurs
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header('Location: /vehicule/ajouter');
            exit();
        }
        
        // Préparer les données
        $vehiculeData = [
            'utilisateur_id' => $userId,
            'marque' => $marque,
            'modele' => $modele,
            'couleur' => $couleur,
            'plaque_immatriculation' => $plaque,
            'date_premiere_immatriculation' => $dateImmat,
            'nombre_places' => $places,
            'energie' => $energie
        ];
        
        // Créer le véhicule
        $result = $this->vehiculeModel->create($vehiculeData);
        
        if ($result) {
            $_SESSION['success'] = 'Véhicule ajouté avec succès !';
            header('Location: /profil');
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'ajout du véhicule';
            header('Location: /vehicule/ajouter');
        }
        exit();
    }
    
    /**
     * Afficher le formulaire de modification
     */
    public function showEditForm($vehiculeId) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }
        
        $vehicule = $this->vehiculeModel->getById($vehiculeId);
        
        if (!$vehicule) {
            $_SESSION['error'] = 'Véhicule introuvable';
            header('Location: /profil');
            exit();
        }
        
        // Vérifier que le véhicule appartient à l'utilisateur
        if ($vehicule['utilisateur_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Ce véhicule ne vous appartient pas';
            header('Location: /profil');
            exit();
        }
        
        include 'app/views/vehicule/edit.php';
    }
    
    /**
     * Traiter la modification
     */
    public function processEdit($vehiculeId) {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profil');
            exit();
        }
        
        $vehicule = $this->vehiculeModel->getById($vehiculeId);
        
        if (!$vehicule || $vehicule['utilisateur_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Véhicule introuvable ou accès non autorisé';
            header('Location: /profil');
            exit();
        }
        
        // Même validation que pour l'ajout
        $errors = [];
        $marque = trim($_POST['marque'] ?? '');
        $modele = trim($_POST['modele'] ?? '');
        $couleur = trim($_POST['couleur'] ?? '');
        $places = intval($_POST['nombre_places'] ?? 0);
        $energie = $_POST['energie'] ?? '';
        
        if (empty($marque)) $errors[] = 'La marque est obligatoire';
        if (empty($modele)) $errors[] = 'Le modèle est obligatoire';
        if ($places < 1 || $places > 8) $errors[] = 'Nombre de places invalide';
        if (!in_array($energie, ['essence', 'diesel', 'electrique', 'hybride'])) {
            $errors[] = 'Type d\'énergie invalide';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header('Location: /vehicule/modifier/' . $vehiculeId);
            exit();
        }
        
        $updateData = [
            'marque' => $marque,
            'modele' => $modele,
            'couleur' => $couleur,
            'nombre_places' => $places,
            'energie' => $energie
        ];
        
        if ($this->vehiculeModel->update($vehiculeId, $updateData)) {
            $_SESSION['success'] = 'Véhicule modifié avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la modification';
        }
        
        header('Location: /profil');
        exit();
    }
    
    /**
     * Supprimer un véhicule
     */
    public function delete($vehiculeId) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }
        
        $vehicule = $this->vehiculeModel->getById($vehiculeId);
        
        if (!$vehicule || $vehicule['utilisateur_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Véhicule introuvable ou accès non autorisé';
            header('Location: /profil');
            exit();
        }
        
        // Vérifier qu'aucun covoiturage actif n'utilise ce véhicule
        if ($this->vehiculeModel->hasActiveTrips($vehiculeId)) {
            $_SESSION['error'] = 'Impossible de supprimer ce véhicule car il est utilisé pour des covoiturages actifs';
            header('Location: /profil');
            exit();
        }
        
        if ($this->vehiculeModel->delete($vehiculeId)) {
            $_SESSION['success'] = 'Véhicule supprimé avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression';
        }
        
        header('Location: /profil');
        exit();
    }
    
    /**
     * Valider le format de la plaque d'immatriculation
     */
    private function validatePlaque($plaque) {
        // Format français: AA-123-AA ou 1234-AB-12
        $patterns = [
            '/^[A-Z]{2}-[0-9]{3}-[A-Z]{2}$/',  // Nouveau format
            '/^[0-9]{1,4}-[A-Z]{2,3}-[0-9]{2}$/' // Ancien format
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $plaque)) {
                return true;
            }
        }
        
        return false;
    }
}