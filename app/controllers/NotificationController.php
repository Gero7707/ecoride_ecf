<?php
// app/controllers/NotificationController.php

require_once 'app/models/EmailService.php';
require_once 'config/mongodb.php';

class NotificationController {
    private $pdo;  // ✅ PDO
    private $emailService;
    
    public function __construct($pdo) {  // ✅ PDO
        $this->pdo = $pdo;  // ✅ CORRECTION
        
        $mongodb = getMongoConnection();
        $this->emailService = new EmailService($pdo, $mongodb);
    }
    
    public function apresCreationReservation($reservationId) {
        try {
            $this->emailService->notifierNouvelleReservation($reservationId);
            return ['success' => true, 'message' => 'Notifications envoyées'];
        } catch (Exception $e) {
            error_log("Erreur notification : " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function apresConfirmationReservation($reservationId) {
        try {
            $this->emailService->notifierReservationConfirmee($reservationId);
            return ['success' => true, 'message' => 'Notification envoyée'];
        } catch (Exception $e) {
            error_log("Erreur confirmation : " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function apresRefusReservation($reservationId, $motif = null) {
        try {
            $this->emailService->notifierReservationRefusee($reservationId, $motif);
            return ['success' => true, 'message' => 'Notification envoyée'];
        } catch (Exception $e) {
            error_log("Erreur refus : " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function apresAnnulationCovoiturage($covoiturageId, $motif = null) {
        try {
            // Mise à jour et recrédit faits dans EmailService ou ReservationController
            $nbNotifications = $this->emailService->notifierAnnulationCovoiturage($covoiturageId, $motif);
            
            return [
                'success' => true, 
                'message' => "$nbNotifications notifications envoyées"
            ];
        } catch (Exception $e) {
            error_log("Erreur annulation : " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function apresEnvoiMessage($messageId) {
        try {
            $this->emailService->notifierNouveauMessage($messageId);
            return ['success' => true, 'message' => 'Notification envoyée'];
        } catch (Exception $e) {
            error_log("Erreur notification message : " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function envoyerRappelsQuotidiens() {
        try {
            $nbRappels = $this->emailService->envoyerRappelsTrajet();
            return ['success' => true, 'message' => "$nbRappels rappels envoyés"];
        } catch (Exception $e) {
            error_log("Erreur rappels : " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}