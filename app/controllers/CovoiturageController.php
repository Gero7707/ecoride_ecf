<?php
/**
 * Contrôleur Covoiturage
 * Gère la recherche et l'affichage des covoiturages
 */

require_once 'app/models/Covoiturage.php';

class CovoiturageController {
    private $pdo;
    private $covoiturageModel;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->covoiturageModel = new Covoiturage($pdo);
    }
    
    /**
     * Afficher la page de recherche avec résultats
     */
    public function search() {
        $covoiturages = [];
        $suggestion_date = null;
        
        // Si des paramètres de recherche sont présents
        if (isset($_GET['depart']) && isset($_GET['arrivee'])) {
            $depart = trim($_GET['depart']);
            $arrivee = trim($_GET['arrivee']);
            $date = !empty($_GET['date']) ? $_GET['date'] : null;
            
            // Validation des données
            if (empty($depart) || empty($arrivee)) {
                $_SESSION['error'] = 'Veuillez remplir au minimum les villes de départ et d\'arrivée';
            } else {
                // Validation de la date si fournie
                if (!empty($date) && strtotime($date) < strtotime('today')) {
                    $_SESSION['error'] = 'La date de départ doit être dans le futur';
                } else {
                    // Préparer les filtres
                    $filters = [];
                    
                    if (isset($_GET['ecologique']) && $_GET['ecologique'] == '1') {
                        $filters['ecologique'] = true;
                    }
                    
                    if (isset($_GET['prix_max']) && !empty($_GET['prix_max'])) {
                        $filters['prix_max'] = floatval($_GET['prix_max']);
                    }
                    
                    if (isset($_GET['note_min']) && !empty($_GET['note_min'])) {
                        $filters['note_min'] = intval($_GET['note_min']);
                    }
                    
                    // Recherche des covoiturages
                    $covoiturages = $this->covoiturageModel->search($depart, $arrivee, $date, $filters);
                    
                    // Si aucun résultat, chercher une suggestion de date
                    if (empty($covoiturages)) {
                        $suggestion_date = $this->covoiturageModel->findNextAvailableDate($depart, $arrivee);
                    }
                }
            }
        }
        
        // Inclure la vue avec les données
        include 'app/views/covoiturage/search.php';
    }
    
    /**
     * Afficher les détails d'un covoiturage
     */
    public function details($id) {
        $covoiturage = $this->covoiturageModel->getById($id);
        
        if (!$covoiturage) {
            $_SESSION['error'] = 'Covoiturage introuvable';
            header('Location: /covoiturages');
            exit();
        }
        
        // Récupérer les avis du chauffeur
        $avis_chauffeur = $this->covoiturageModel->getDriverReviews($covoiturage['chauffeur_id']);
        
        // Inclure la vue détails
        include 'app/views/covoiturage/details.php';
    }
    
    /**
     * Créer un nouveau covoiturage (pour les chauffeurs)
     */
    public function create() {
        // Vérifier que l'utilisateur est connecté et est chauffeur
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour créer un covoiturage';
            header('Location: /connexion');
            exit();
        }
        
        if ($_SESSION['user_statut'] !== 'chauffeur' && $_SESSION['user_statut'] !== 'admin') {
            $_SESSION['error'] = 'Vous devez être chauffeur pour créer un covoiturage';
            header('Location: /profil');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCreateCovoiturage();
        } else {
            $this->showCreateForm();
        }
    }
    
    /**
     * Afficher le formulaire de création
     */
    private function showCreateForm() {
        // TODO: Récupérer les véhicules du chauffeur
        include 'app/views/covoiturage/create.php';
    }
    
    /**
     * Traiter la création d'un covoiturage
     */
    private function processCreateCovoiturage() {
        $ville_depart = trim($_POST['ville_depart'] ?? '');
        $ville_arrivee = trim($_POST['ville_arrivee'] ?? '');
        $date_depart = $_POST['date_depart'] ?? '';
        $heure_depart = $_POST['heure_depart'] ?? '';
        $heure_arrivee = $_POST['heure_arrivee'] ?? '';
        $prix = floatval($_POST['prix'] ?? 0);
        $places_disponibles = intval($_POST['places_disponibles'] ?? 1);
        $vehicule_id = intval($_POST['vehicule_id'] ?? 0);
        
        // Validation des données
        $errors = [];
        
        if (empty($ville_depart)) $errors[] = 'Ville de départ obligatoire';
        if (empty($ville_arrivee)) $errors[] = 'Ville d\'arrivée obligatoire';
        if (empty($date_depart)) $errors[] = 'Date de départ obligatoire';
        if (empty($heure_depart)) $errors[] = 'Heure de départ obligatoire';
        if ($prix <= 0) $errors[] = 'Prix invalide';
        if ($places_disponibles < 1 || $places_disponibles > 8) $errors[] = 'Nombre de places invalide';
        if ($vehicule_id <= 0) $errors[] = 'Véhicule obligatoire';
        
        // Vérifier que la date est dans le futur
        if (strtotime($date_depart) < strtotime('today')) {
            $errors[] = 'La date de départ doit être dans le futur';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header('Location: /covoiturage/creer');
            exit();
        }
        
        // Créer le covoiturage
        $result = $this->covoiturageModel->create(
            $_SESSION['user_id'],
            $vehicule_id,
            $ville_depart,
            $ville_arrivee,
            $date_depart,
            $heure_depart,
            $heure_arrivee,
            $prix,
            $places_disponibles
        );
        
        if ($result['success']) {
            $_SESSION['success'] = 'Covoiturage créé avec succès !';
            header('Location: /covoiturage/' . $result['covoiturage_id']);
            exit();
        } else {
            $_SESSION['error'] = $result['error'];
            header('Location: /covoiturage/creer');
            exit();
        }
    }
}
?>