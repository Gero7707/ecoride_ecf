<?php
/**
 * Contrôleur de gestion des réservations
 * Gère la création, annulation et confirmation des réservations
 */

require_once 'app/models/ReservationModel.php';
require_once 'app/models/UserModel.php';

// ✅ AJOUT 1 : Importer le contrôleur de notifications
require_once 'app/controllers/NotificationController.php';

class ReservationController {
    private $pdo;
    private $reservationModel;
    private $userModel;
    
    // ✅ AJOUT 2 : Propriété pour le contrôleur de notifications
    private $notificationCtrl;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->reservationModel = new ReservationModel();
        $this->userModel = new UserModel();
        
        // ✅ AJOUT 3 : Initialiser le contrôleur de notifications
        $this->notificationCtrl = new NotificationController($pdo);
    }
    
    /**
     * Créer une nouvelle réservation
     */
    public function createReservation() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour réserver un trajet';
            header('Location: /connexion');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Méthode non autorisée';
            header('Location: /covoiturages');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $covoiturageId = intval($_POST['covoiturage_id'] ?? 0);

        // Validation de base
        if ($covoiturageId <= 0) {
            $_SESSION['error'] = 'Covoiturage invalide';
            header('Location: /covoiturages');
            exit();
        }

        // Récupérer les informations du covoiturage
        $covoiturage = $this->reservationModel->getCovoiturageById($covoiturageId);

        if (!$covoiturage) {
            $_SESSION['error'] = 'Covoiturage introuvable';
            header('Location: /covoiturages');
            exit();
        }

        // Vérifications métier
        $validationResult = $this->validateReservation($userId, $covoiturage);

        if (!$validationResult['success']) {
            $_SESSION['error'] = $validationResult['error'];
            header('Location: /covoiturage/' . $covoiturageId);
            exit();
        }

        // IMPORTANT : Déterminer le statut selon confirmation_requise
        $statut_reservation = ($covoiturage['confirmation_requise'] == 1) 
            ? 'en_attente'   // Nécessite confirmation du chauffeur
            : 'confirmee';   // Réservation automatique

        // Créer la réservation AVEC le statut
        $reservationResult = $this->reservationModel->createReservation($userId, $covoiturageId, $statut_reservation);

        if ($reservationResult['success']) {
            // ✅ AJOUT 4 : Récupérer l'ID de la réservation créée
            $reservationId = $reservationResult['reservation_id'] ?? $this->pdo->lastInsertId();

            // Si réservation automatique (confirmée), déduire crédits et places
            if ($statut_reservation === 'confirmee') {
                // Déduire les crédits du passager
                $this->userModel->deductCredits($userId, $covoiturage['prix']);

                // Mettre à jour les places disponibles
                $this->reservationModel->updateAvailableSeats($covoiturageId, -1);

                $_SESSION['success'] = 'Réservation confirmée ! Vos crédits ont été débités.';
            } else {
                // Réservation en attente - NE PAS déduire crédits ni places
                $_SESSION['success'] = 'Demande de réservation envoyée ! Le conducteur doit confirmer. Vos crédits seront débités après validation.';
            }

            // ✅ AJOUT 5 : ENVOYER LES NOTIFICATIONS EMAIL
            try {
                $this->notificationCtrl->apresCreationReservation($reservationId);
            } catch (Exception $e) {
                // Ne pas bloquer l'utilisateur si l'email échoue
                error_log("Erreur envoi email : " . $e->getMessage());
            }

            header('Location: /covoiturage/' . $covoiturageId);
            exit();
        } else {
            $_SESSION['error'] = $reservationResult['error'];
            header('Location: /covoiturage/' . $covoiturageId);
            exit();
        }
    }
    
    /**
     * Annuler une réservation
     */
    public function cancelReservation() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté';
            header('Location: /connexion');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profil');
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        $reservationId = intval($_POST['reservation_id'] ?? 0);
        
        if ($reservationId <= 0) {
            $_SESSION['error'] = 'Réservation invalide';
            header('Location: /profil');
            exit();
        }
        
        // Récupérer les détails de la réservation
        $reservation = $this->reservationModel->getReservationById($reservationId);
        
        if (!$reservation) {
            $_SESSION['error'] = 'Réservation introuvable';
            header('Location: /profil');
            exit();
        }
        
        // Vérifier que la réservation appartient à l'utilisateur
        if ($reservation['passager_id'] != $userId) {
            $_SESSION['error'] = 'Cette réservation ne vous appartient pas';
            header('Location: /profil');
            exit();
        }
        
        // Vérifier si l'annulation est encore possible
        if (!$this->canCancelReservation($reservation)) {
            $_SESSION['error'] = 'Impossible d\'annuler cette réservation (trajet déjà commencé ou terminé)';
            header('Location: /profil');
            exit();
        }
        
        // Annuler la réservation
        $cancelResult = $this->reservationModel->cancelReservation($reservationId);
        
        if ($cancelResult) {
            // Rembourser les crédits
            $this->userModel->addCredits($userId, $reservation['prix']);
            
            // Libérer une place
            $this->reservationModel->updateAvailableSeats($reservation['covoiturage_id'], 1);
            
            $_SESSION['success'] = 'Réservation annulée avec succès. Vos crédits ont été remboursés.';
            
            // ✅ AJOUT 6 : Notification d'annulation (optionnel - si vous voulez notifier le chauffeur)
            // Vous pouvez créer une nouvelle méthode dans NotificationController pour ça
            // try {
            //     $this->notificationCtrl->apresAnnulationParPassager($reservationId);
            // } catch (Exception $e) {
            //     error_log("Erreur envoi email : " . $e->getMessage());
            // }
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'annulation de la réservation';
        }
        
        header('Location: /profil');
        exit();
    }
    
    /**
     * Confirmer une réservation (pour le chauffeur)
     */
    public function confirmReservation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté';
            header('Location: /connexion');
            exit();
        }
        
        $reservationId = $_POST['reservation_id'] ?? null;
        
        if (!$reservationId) {
            $_SESSION['error'] = 'Réservation introuvable';
            header('Location: /profil');
            exit();
        }
        
        // Récupérer la réservation
        $reservation = $this->reservationModel->getReservationById($reservationId);
        
        if (!$reservation) {
            $_SESSION['error'] = 'Réservation introuvable';
            header('Location: /profil');
            exit();
        }
        
        // Récupérer le covoiturage
        $covoiturage = $this->reservationModel->getCovoiturageById($reservation['covoiturage_id']);
        
        // Vérifier que c'est bien le chauffeur
        if ($covoiturage['chauffeur_id'] !== $_SESSION['user_id']) {
            $_SESSION['error'] = 'Vous n\'êtes pas autorisé';
            header('Location: /profil');
            exit();
        }
        
        // Vérifier qu'il reste des places
        if ($covoiturage['places_disponibles'] <= 0) {
            $_SESSION['error'] = 'Il n\'y a plus de places disponibles';
            header('Location: /covoiturage/' . $reservation['covoiturage_id'] . '/passagers');
            exit();
        }
        
        // Confirmer la réservation
        $this->reservationModel->updateReservationStatus($reservationId, 'confirmee');
        
        // Déduire les crédits du passager
        $this->userModel->deductCredits($reservation['passager_id'], $covoiturage['prix']);
        
        // Déduire une place
        $this->reservationModel->updateAvailableSeats($reservation['covoiturage_id'], -1);
        
        $_SESSION['success'] = 'Réservation confirmée ! Le passager a été notifié.';
        
        // ✅ AJOUT 7 : NOTIFICATION DE CONFIRMATION
        try {
            $this->notificationCtrl->apresConfirmationReservation($reservationId);
        } catch (Exception $e) {
            error_log("Erreur envoi email : " . $e->getMessage());
        }
        
        header('Location: /covoiturage/' . $reservation['covoiturage_id'] . '/passagers');
        exit();
    }
    
    /**
     * Refuser une réservation (pour le chauffeur)
     */
    public function refuserReservation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit();
        }

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté';
            header('Location: /connexion');
            exit();
        }

        $reservationId = $_POST['reservation_id'] ?? null;
        // ✅ AJOUT 8 : Récupérer le motif (optionnel)
        $motif = $_POST['motif'] ?? null;

        if (!$reservationId) {
            $_SESSION['error'] = 'Réservation introuvable';
            header('Location: /profil');
            exit();
        }

        // Récupérer la réservation et le trajet
        $stmt = $this->pdo->prepare("
            SELECT r.*, c.chauffeur_id, c.id as covoiturage_id, c.prix
            FROM reservation r
            JOIN covoiturage c ON r.covoiturage_id = c.id
            WHERE r.id = ?
        ");
        $stmt->execute([$reservationId]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reservation) {
            $_SESSION['error'] = 'Réservation introuvable';
            header('Location: /profil');
            exit();
        }

        // Vérifier que c'est bien le chauffeur
        if ($reservation['chauffeur_id'] !== $_SESSION['user_id']) {
            $_SESSION['error'] = 'Vous n\'êtes pas autorisé à refuser cette réservation';
            header('Location: /profil');
            exit();
        }

        // Refuser la réservation
        $stmt = $this->pdo->prepare("
            UPDATE reservation 
            SET statut = 'annulee' 
            WHERE id = ?
        ");
        $stmt->execute([$reservationId]);

        // ✅ MODIFICATION 9 : Recréditer le passager seulement si la réservation était confirmée
        if ($reservation['statut'] === 'confirmee') {
            // Recréditer le passager
            $this->userModel->addCredits($reservation['passager_id'], $reservation['prix']);
            
            // Libérer une place
            $stmt = $this->pdo->prepare("
                UPDATE covoiturage 
                SET places_disponibles = places_disponibles + 1 
                WHERE id = ?
            ");
            $stmt->execute([$reservation['covoiturage_id']]);
        }

        $_SESSION['success'] = 'Réservation refusée';
        
        // ✅ AJOUT 10 : NOTIFICATION DE REFUS
        try {
            $this->notificationCtrl->apresRefusReservation($reservationId, $motif);
        } catch (Exception $e) {
            error_log("Erreur envoi email : " . $e->getMessage());
        }
        
        header('Location: /covoiturage/' . $reservation['covoiturage_id'] . '/passagers');
        exit();
    }
    
    /**
     * Valider si une réservation est possible
     */
    private function validateReservation($userId, $covoiturage) {
        // Vérifier que ce n'est pas son propre trajet
        if ($covoiturage['chauffeur_id'] == $userId) {
            return ['success' => false, 'error' => 'Vous ne pouvez pas réserver votre propre trajet'];
        }
        
        // Vérifier le statut du covoiturage
        if ($covoiturage['statut'] !== 'prevu') {
            return ['success' => false, 'error' => 'Ce trajet n\'est plus disponible à la réservation'];
        }
        
        // Vérifier les places disponibles
        if ($covoiturage['places_disponibles'] <= 0) {
            return ['success' => false, 'error' => 'Aucune place disponible pour ce trajet'];
        }
        
        // Vérifier que l'utilisateur n'a pas déjà réservé ce trajet
        if ($this->reservationModel->hasUserReserved($userId, $covoiturage['id'])) {
            return ['success' => false, 'error' => 'Vous avez déjà réservé ce trajet'];
        }
        
        // Vérifier que l'utilisateur a suffisamment de crédits
        $userCredits = $this->userModel->getUserCredits($userId);
        if ($userCredits < $covoiturage['prix']) {
            return ['success' => false, 'error' => 'Crédits insuffisants. Il vous faut ' . $covoiturage['prix'] . ' crédits pour ce trajet.'];
        }
        
        // Vérifier que la date du trajet est dans le futur
        $departureDateTime = $covoiturage['date_depart'] . ' ' . $covoiturage['heure_depart'];
        if (strtotime($departureDateTime) <= time()) {
            return ['success' => false, 'error' => 'Ce trajet est déjà passé ou en cours'];
        }
        
        return ['success' => true];
    }
    
    /**
     * Vérifier si une réservation peut être annulée
     */
    private function canCancelReservation($reservation) {
        // Ne peut pas annuler si le trajet est terminé ou en cours
        if (in_array($reservation['statut_covoiturage'], ['termine', 'en_cours'])) {
            return false;
        }
        
        // Ne peut pas annuler si la réservation est déjà annulée
        if ($reservation['statut'] === 'annule') {
            return false;
        }
        
        // Politique d'annulation : au moins 2 heures avant le départ
        $departureDateTime = $reservation['date_depart'] . ' ' . $reservation['heure_depart'];
        $timeUntilDeparture = strtotime($departureDateTime) - time();
        
        if ($timeUntilDeparture < 2 * 3600) { // 2 heures = 7200 secondes
            return false;
        }
        
        return true;
    }
    
    /**
     * Voir les détails d'une réservation
     */
    public function showReservationDetails($reservationId) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté';
            header('Location: /connexion');
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        $reservation = $this->reservationModel->getReservationWithDetails($reservationId);
        
        if (!$reservation) {
            $_SESSION['error'] = 'Réservation introuvable';
            header('Location: /profil');
            exit();
        }
        
        // Vérifier que l'utilisateur a le droit de voir cette réservation
        if ($reservation['passager_id'] != $userId && $reservation['chauffeur_id'] != $userId) {
            $_SESSION['error'] = 'Accès non autorisé';
            header('Location: /profil');
            exit();
        }
        
        // Charger la vue des détails
        include 'app/views/reservation/details.php';
    }
    
    /**
     * Lister les réservations d'un utilisateur
     */
    public function listUserReservations() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        $reservations = $this->reservationModel->getUserReservations($userId);
        
        // Charger la vue de la liste
        include 'app/views/reservation/list.php';
    }

    /**
     * Supprimer une réservation annulée
     */
    public function deleteReservation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profil');
            exit();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit();
        }

        $reservationId = isset($_POST['reservation_id']) ? intval($_POST['reservation_id']) : 0;

        error_log("Tentative de suppression de la réservation #$reservationId par l'utilisateur #{$_SESSION['user_id']}");

        try {
            // Vérifier que la réservation appartient à l'utilisateur et est annulée
            $stmt = $this->pdo->prepare("
                SELECT id, statut 
                FROM reservation 
                WHERE id = ? AND passager_id = ?
            ");
            $stmt->execute([$reservationId, $_SESSION['user_id']]);
            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

            error_log("Réservation trouvée : " . print_r($reservation, true));

            if (!$reservation) {
                $_SESSION['error'] = 'Réservation introuvable';
                header('Location: /profil');
                exit();
            }

            if ($reservation['statut'] !== 'annule') {
                $_SESSION['error'] = 'Seules les réservations annulées peuvent être supprimées';
                header('Location: /profil');
                exit();
            }

            // Supprimer la réservation
            $stmt = $this->pdo->prepare("DELETE FROM reservation WHERE id = ?");
            $stmt->execute([$reservationId]);

            $_SESSION['success'] = 'Réservation supprimée avec succès';
            header('Location: /profil');
            exit();

        } catch (PDOException $e) {
            error_log('Erreur suppression réservation: ' . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la suppression';
            header('Location: /profil');
            exit();
        }
    }
}