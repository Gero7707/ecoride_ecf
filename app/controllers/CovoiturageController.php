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
        
        // Vérifier si l'utilisateur connecté a déjà réservé ce trajet
        $userReservation = null;
        if (isset($_SESSION['user_id'])) {
            $userReservation = $this->covoiturageModel->getUserReservation($id, $_SESSION['user_id']);
        }
        
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
        require_once 'app/models/UserModel.php';
        $userModel = new UserModel();
        $vehicules = $userModel->getUserVehicles($_SESSION['user_id']);
        
        // Charger la vue
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

        $reservation_type = $_POST['reservation_type'] ?? 'instant';
        $confirmation_requise = ($reservation_type === 'confirmation') ? 1 : 0;
        
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
            $places_disponibles,
            $confirmation_requise
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

    /**
 * Annuler un covoiturage
 */
    public function cancelCovoiturage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profil');
            exit();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }

        $covoiturageId = isset($_POST['covoiturage_id']) ? intval($_POST['covoiturage_id']) : 0;

        try {
            // Vérifier que le covoiturage appartient au chauffeur
            $stmt = $this->pdo->prepare("
                SELECT id, statut, date_depart, heure_depart
                FROM covoiturage 
                WHERE id = ? AND chauffeur_id = ?
            ");
            $stmt->execute([$covoiturageId, $_SESSION['user_id']]);
            $covoiturage = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$covoiturage) {
                $_SESSION['error'] = 'Covoiturage introuvable';
                header('Location: /profil');
                exit();
            }

            if ($covoiturage['statut'] !== 'prevu') {
                $_SESSION['error'] = 'Seuls les covoiturages actifs peuvent être annulés';
                header('Location: /profil');
                exit();
            }

            // Annuler le covoiturage
            $stmt = $this->pdo->prepare("UPDATE covoiturage SET statut = 'annule' WHERE id = ?");
            $stmt->execute([$covoiturageId]);

            // Annuler toutes les réservations associées
            $stmt = $this->pdo->prepare("UPDATE reservation SET statut = 'annule' WHERE covoiturage_id = ?");
            $stmt->execute([$covoiturageId]);

            $_SESSION['success'] = 'Covoiturage annulé avec succès. Les passagers ont été notifiés.';
            header('Location: /profil');
            exit();

        } catch (PDOException $e) {
            error_log('Erreur annulation covoiturage: ' . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de l\'annulation';
            header('Location: /profil');
            exit();
        }
    }

/**
 * Supprimer un covoiturage annulé
 */
    public function deleteCovoiturage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profil');
            exit();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }

        $covoiturageId = isset($_POST['covoiturage_id']) ? intval($_POST['covoiturage_id']) : 0;

        try {
            // Vérifier que le covoiturage appartient au chauffeur et est annulé
            $stmt = $this->pdo->prepare("
                SELECT id, statut 
                FROM covoiturage 
                WHERE id = ? AND chauffeur_id = ?
            ");
            $stmt->execute([$covoiturageId, $_SESSION['user_id']]);
            $covoiturage = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$covoiturage) {
                $_SESSION['error'] = 'Covoiturage introuvable';
                header('Location: /profil');
                exit();
            }

            if ($covoiturage['statut'] !== 'annule') {
                $_SESSION['error'] = 'Seuls les covoiturages annulés peuvent être supprimés';
                header('Location: /profil');
                exit();
            }

            // Supprimer d'abord les réservations associées
            $stmt = $this->pdo->prepare("DELETE FROM reservation WHERE covoiturage_id = ?");
            $stmt->execute([$covoiturageId]);

            // Supprimer le covoiturage
            $stmt = $this->pdo->prepare("DELETE FROM covoiturage WHERE id = ?");
            $stmt->execute([$covoiturageId]);

            $_SESSION['success'] = 'Covoiturage supprimé avec succès';
            header('Location: /profil');
            exit();

        } catch (PDOException $e) {
            error_log('Erreur suppression covoiturage: ' . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la suppression';
            header('Location: /profil');
            exit();
        }
    }

        /**
     * Afficher les passagers d'un covoiturage (pour le chauffeur)
     */
    public function showPassengers($covoiturageId) {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté';
            header('Location: /connexion');
            exit();
        }

        // Récupérer le trajet
        $stmt = $this->pdo->prepare("
            SELECT * FROM covoiturage WHERE id = ?
        ");
        $stmt->execute([$covoiturageId]);
        $covoiturage = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$covoiturage) {
            $_SESSION['error'] = 'Trajet introuvable';
            header('Location: /covoiturages');
            exit();
        }

        // SÉCURITÉ : Vérifier que c'est bien le chauffeur
        if ($covoiturage['chauffeur_id'] !== $_SESSION['user_id']) {
            $_SESSION['error'] = 'Vous n\'êtes pas autorisé à voir ces informations';
            header('Location: /covoiturage/' . $covoiturageId);
            exit();
        }

        // Récupérer les passagers avec leurs réservations
        $stmt = $this->pdo->prepare("
            SELECT 
                r.*,
                u.id as user_id,
                u.pseudo,
                u.photo,
                u.telephone,
                COALESCE(AVG(a.note), 0) as note_moyenne,
                COUNT(a.id) as nombre_avis
            FROM reservation r
            JOIN utilisateur u ON r.passager_id = u.id
            LEFT JOIN avis a ON a.evalue_id = u.id AND a.valide = 1
            WHERE r.covoiturage_id = ?
            GROUP BY r.id, u.id
            ORDER BY r.date_reservation DESC
        ");
        $stmt->execute([$covoiturageId]);
        $passagers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Passer les données à la vue
        $data = [
            'covoiturage' => $covoiturage,
            'passagers' => $passagers
        ];

        include 'app/views/covoiturage/passagers.php';
    }

    /**
     * Afficher tous mes covoiturages
     */
    public function mesCovoiturages() {
        // Vérifier que l'utilisateur est connecté et chauffeur
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
        
        $userId = $_SESSION['user_id'];
        
        // Récupérer tous les covoiturages du chauffeur avec statistiques
        $stmt = $this->pdo->prepare("
            SELECT 
                c.*,
                v.marque,
                v.modele,
                v.couleur,
                COUNT(DISTINCT r.id) as nb_reservations,
                COUNT(DISTINCT CASE WHEN r.statut = 'en_attente' THEN r.id END) as nb_en_attente,
                COUNT(DISTINCT CASE WHEN r.statut = 'confirmee' THEN r.id END) as nb_confirmees
            FROM covoiturage c
            LEFT JOIN vehicule v ON c.vehicule_id = v.id
            LEFT JOIN reservation r ON c.id = r.covoiturage_id AND r.statut IN ('en_attente', 'confirmee')
            WHERE c.chauffeur_id = ?
            GROUP BY c.id
            ORDER BY c.date_depart DESC, c.heure_depart DESC
        ");
        $stmt->execute([$userId]);
        $covoiturages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Grouper par statut
        $groupes = [
            'prevu' => [],
            'en_cours' => [],
            'termine' => [],
            'annule' => []
        ];
        
        foreach ($covoiturages as $covoiturage) {
            $groupes[$covoiturage['statut']][] = $covoiturage;
        }
        
        // Passer à la vue
        $data = [
            'covoiturages' => $covoiturages,
            'groupes' => $groupes,
            'total' => count($covoiturages)
        ];
        
        include 'app/views/covoiturage/mes-covoiturages.php';
    }
}
?>