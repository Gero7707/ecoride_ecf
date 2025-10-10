<?php
/**
 * Modèle de gestion des réservations
 * Gère les opérations en base de données pour les réservations
 */

class ReservationModel {
    private $db;
    
    public function __construct() {
        // Utiliser la connexion PDO globale
        global $pdo;
        $this->db = $pdo;
    }
    
    /**
     * Créer une nouvelle réservation
     */
    public function createReservation($passagerId, $covoiturageId) {
        try {
            $sql = "INSERT INTO reservation (passager_id, covoiturage_id, statut, date_reservation) 
                    VALUES (?, ?, 'confirmee', NOW())";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$passagerId, $covoiturageId]);
            
            if ($result) {
                return [
                    'success' => true,
                    'reservation_id' => $this->db->lastInsertId()
                ];
            } else {
                return ['success' => false, 'error' => 'Erreur lors de la création de la réservation'];
            }
        } catch (PDOException $e) {
            error_log("Erreur createReservation: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erreur technique lors de la réservation'];
        }
    }
    
    /**
     * Récupérer un covoiturage par son ID
     */
    public function getCovoiturageById($id) {
        try {
            $sql = "SELECT c.*, u.pseudo as chauffeur_pseudo, u.email as chauffeur_email
                    FROM covoiturage c 
                    JOIN utilisateur u ON c.chauffeur_id = u.id 
                    WHERE c.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getCovoiturageById: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifier si un utilisateur a déjà réservé un covoiturage
     */
    public function hasUserReserved($userId, $covoiturageId) {
        try {
            $sql = "SELECT * FROM reservation 
                    WHERE passager_id = ? AND covoiturage_id = ? AND statut != 'annulee'";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $covoiturageId]);
            $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return count($reservations) > 0;
        } catch (PDOException $e) {
            error_log("Erreur hasUserReserved: " . $e->getMessage());
            return true; // Retourne true en cas d'erreur pour éviter les doublons
        }
    }
    
    /**
     * Mettre à jour le nombre de places disponibles
     */
    public function updateAvailableSeats($covoiturageId, $change) {
        try {
            $sql = "UPDATE covoiturage 
                    SET places_disponibles = places_disponibles + ? 
                    WHERE id = ? AND places_disponibles + ? >= 0";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$change, $covoiturageId, $change]);
        } catch (PDOException $e) {
            error_log("Erreur updateAvailableSeats: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer une réservation par son ID
     */
    public function getReservationById($id) {
        try {
            $sql = "SELECT r.*, c.date_depart, c.heure_depart, c.prix, c.statut as statut_covoiturage,
                            c.ville_depart, c.ville_arrivee, c.chauffeur_id
                    FROM reservation r 
                    JOIN covoiturage c ON r.covoiturage_id = c.id 
                    WHERE r.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getReservationById: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Annuler une réservation
     */
    public function cancelReservation($reservationId) {
        try {
            $sql = "UPDATE reservation SET statut = 'annulee' WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$reservationId]);
        } catch (PDOException $e) {
            error_log("Erreur cancelReservation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Confirmer une réservation
     */
    public function confirmReservation($reservationId) {
        try {
            $sql = "UPDATE reservation SET statut = 'confirmee' WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$reservationId]);
        } catch (PDOException $e) {
            error_log("Erreur confirmReservation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer une réservation avec les détails du covoiturage
     */
    public function getReservationWithCovoiturage($reservationId) {
        try {
            $sql = "SELECT r.*, c.*, u.pseudo as chauffeur_pseudo
                    FROM reservation r 
                    JOIN covoiturage c ON r.covoiturage_id = c.id 
                    JOIN utilisateur u ON c.chauffeur_id = u.id
                    WHERE r.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$reservationId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getReservationWithCovoiturage: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer les réservations d'un utilisateur
     */
    public function getUserReservations($userId) {
        try {
            $sql = "SELECT r.*, c.ville_depart, c.ville_arrivee, c.date_depart, c.heure_depart, 
                            c.prix, c.id as covoiturage_id, c.statut as statut_covoiturage, 
                            u.pseudo as chauffeur_pseudo, v.marque, v.modele, v.couleur
                    FROM reservation r 
                    JOIN covoiturage c ON r.covoiturage_id = c.id 
                    JOIN utilisateur u ON c.chauffeur_id = u.id 
                    LEFT JOIN vehicule v ON c.vehicule_id = v.id 
                    WHERE r.passager_id = ? 
                    ORDER BY 
                        CASE r.statut
                            WHEN 'confirmee' THEN 1
                            WHEN 'en_attente' THEN 2
                            WHEN 'terminee' THEN 3
                            WHEN 'annulee' THEN 4
                            ELSE 5
                        END,
                        c.date_depart DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getUserReservations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer les réservations reçues par un chauffeur
     */
    public function getDriverReservations($driverId) {
        try {
            $sql = "SELECT r.*, c.ville_depart, c.ville_arrivee, c.date_depart, c.heure_depart,
                            u.pseudo as passager_pseudo, u.telephone as passager_telephone,
                            u.email as passager_email
                    FROM reservation r 
                    JOIN covoiturage c ON r.covoiturage_id = c.id 
                    JOIN utilisateur u ON r.passager_id = u.id 
                    WHERE c.chauffeur_id = ? 
                    ORDER BY c.date_depart DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$driverId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getDriverReservations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer une réservation avec tous les détails
     */
    public function getReservationWithDetails($reservationId) {
        try {
            $sql = "SELECT r.*, 
                            c.ville_depart, c.ville_arrivee, c.date_depart, c.heure_depart, 
                            c.heure_arrivee, c.prix, c.statut as statut_covoiturage,
                            chauffeur.pseudo as chauffeur_pseudo, chauffeur.telephone as chauffeur_telephone,
                            chauffeur.email as chauffeur_email,
                            passager.pseudo as passager_pseudo, passager.telephone as passager_telephone,
                            passager.email as passager_email,
                            v.marque, v.modele, v.couleur, v.plaque_immatriculation
                    FROM reservation r 
                    JOIN covoiturage c ON r.covoiturage_id = c.id 
                    JOIN utilisateur chauffeur ON c.chauffeur_id = chauffeur.id
                    JOIN utilisateur passager ON r.passager_id = passager.id
                    LEFT JOIN vehicule v ON c.vehicule_id = v.id
                    WHERE r.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$reservationId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getReservationWithDetails: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Compter les réservations pour un covoiturage
     */
    public function countReservationsForTrip($covoiturageId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM reservation 
                    WHERE covoiturage_id = ? AND statut = 'confirmee'";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$covoiturageId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Erreur countReservationsForTrip: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Récupérer les réservations d'un covoiturage spécifique
     */
    public function getTripReservations($covoiturageId) {
        try {
            $sql = "SELECT r.*, u.pseudo as passager_pseudo, u.telephone as passager_telephone
                    FROM reservation r 
                    JOIN utilisateur u ON r.passager_id = u.id 
                    WHERE r.covoiturage_id = ? AND r.statut != 'annulee'
                    ORDER BY r.date_reservation ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$covoiturageId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getTripReservations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Marquer une réservation comme terminée
     */
    public function completeReservation($reservationId) {
        try {
            $sql = "UPDATE reservation SET statut = 'terminee' WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$reservationId]);
        } catch (PDOException $e) {
            error_log("Erreur completeReservation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir les statistiques de réservation pour un utilisateur
     */
    public function getUserReservationStats($userId) {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_reservations,
                        SUM(CASE WHEN r.statut = 'confirmee' THEN 1 ELSE 0 END) as confirmees,
                        SUM(CASE WHEN r.statut = 'annulee' THEN 1 ELSE 0 END) as annulees,
                        SUM(CASE WHEN r.statut = 'terminee' THEN 1 ELSE 0 END) as terminees,
                        SUM(CASE WHEN r.statut = 'confirmee' THEN c.prix ELSE 0 END) as total_depense
                    FROM reservation r 
                    JOIN covoiturage c ON r.covoiturage_id = c.id 
                    WHERE r.passager_id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getUserReservationStats: " . $e->getMessage());
            return [
                'total_reservations' => 0,
                'confirmees' => 0,
                'annulees' => 0,
                'terminees' => 0,
                'total_depense' => 0
            ];
        }
    }
    
    /**
     * Vérifier si une réservation peut être modifiée
     */
    public function canModifyReservation($reservationId) {
        try {
            $reservation = $this->getReservationById($reservationId);
            
            if (!$reservation) {
                return false;
            }
            
            // Ne peut pas modifier si annulée ou terminée
            if (in_array($reservation['statut'], ['annulee', 'terminee'])) {
                return false;
            }
            
            // Ne peut pas modifier si le trajet est en cours ou terminé
            if (in_array($reservation['statut_covoiturage'], ['en_cours', 'termine'])) {
                return false;
            }
            
            // Ne peut pas modifier si c'est dans moins de 2 heures
            $departureDateTime = $reservation['date_depart'] . ' ' . $reservation['heure_depart'];
            $timeUntilDeparture = strtotime($departureDateTime) - time();
            
            return $timeUntilDeparture >= 2 * 3600; // 2 heures
        } catch (PDOException $e) {
            error_log("Erreur canModifyReservation: " . $e->getMessage());
            return false;
        }
    }
}