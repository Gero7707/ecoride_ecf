<?php
// app/models/UserModel.php

class UserModel {
    private $db;
    
    public function __construct() {
        // Utiliser la connexion PDO globale de ton config/database.php
        global $pdo;
        $this->db = $pdo;
    }
    
    /**
     * Récupérer un utilisateur par son ID
     */
    public function getUserById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM utilisateur WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getUserById: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mettre à jour les informations de l'utilisateur
     */
    public function updateUser($id, $data) {
        try {
            $sql = "UPDATE utilisateur SET 
                    pseudo = ?, 
                    email = ?, 
                    telephone = ?, 
                    adresse = ?,
                    photo = ?,
                    statut = ?
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['pseudo'],
                $data['email'],
                $data['telephone'] ?? null,
                $data['adresse'] ?? null,
                $data['photo'] ?? null,
                $data['statut'] ?? 'passager',
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Erreur updateUser: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Changer le mot de passe de l'utilisateur
     */
    public function updatePassword($id, $newPassword) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE id = ?");
            return $stmt->execute([$hashedPassword, $id]);
        } catch (PDOException $e) {
            error_log("Erreur updatePassword: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifier le mot de passe actuel
     */
    public function verifyCurrentPassword($id, $password) {
        try {
            $stmt = $this->db->prepare("SELECT mot_de_passe FROM utilisateur WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                return password_verify($password, $user['mot_de_passe']);
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur verifyCurrentPassword: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer les véhicules de l'utilisateur
     */
    public function getUserVehicles($userId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM vehicule WHERE utilisateur_id = ? ORDER BY id DESC");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getUserVehicles: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer les covoiturages proposés par l'utilisateur (si chauffeur)
     */
    public function getUserCovoiturages($userId) {
        try {
            $sql = "SELECT c.*, v.marque, v.modele, 
                    (SELECT COUNT(*) FROM reservation r 
                    WHERE r.covoiturage_id = c.id AND r.statut = 'confirmee') as nb_reservations
                    FROM covoiturage c 
                    LEFT JOIN vehicule v ON c.vehicule_id = v.id 
                    WHERE c.chauffeur_id = ? 
                    ORDER BY c.date_depart DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getUserCovoiturages: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer les réservations de l'utilisateur (si passager)
     */
    public function getUserReservations($userId) {
        try {
            $sql = "SELECT r.*, c.ville_depart, c.ville_arrivee, c.date_depart, 
                    c.heure_depart, c.prix, u.pseudo as chauffeur_pseudo, 
                    v.marque, v.modele, v.couleur
                    FROM reservation r 
                    JOIN covoiturage c ON r.covoiturage_id = c.id 
                    JOIN utilisateur u ON c.chauffeur_id = u.id 
                    LEFT JOIN vehicule v ON c.vehicule_id = v.id 
                    WHERE r.passager_id = ? 
                    ORDER BY c.date_depart DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getUserReservations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer les avis reçus par l'utilisateur
     */
    public function getReceivedReviews($userId) {
        try {
            $sql = "SELECT a.*, u.pseudo as evaluateur_pseudo, 
                    c.ville_depart, c.ville_arrivee, c.date_depart
                    FROM avis a 
                    JOIN utilisateur u ON a.evaluateur_id = u.id 
                    JOIN covoiturage c ON a.covoiturage_id = c.id 
                    WHERE a.evalue_id = ? AND a.valide = TRUE 
                    ORDER BY a.date_creation DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getReceivedReviews: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer les avis donnés par l'utilisateur
     */
    public function getGivenReviews($userId) {
        try {
            $sql = "SELECT a.*, u.pseudo as evalue_pseudo, 
                    c.ville_depart, c.ville_arrivee, c.date_depart
                    FROM avis a 
                    JOIN utilisateur u ON a.evalue_id = u.id 
                    JOIN covoiturage c ON a.covoiturage_id = c.id 
                    WHERE a.evaluateur_id = ? 
                    ORDER BY a.date_creation DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getGivenReviews: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Calculer la note moyenne de l'utilisateur
     */
    public function getAverageRating($userId) {
        try {
            $stmt = $this->db->prepare("SELECT AVG(note) as moyenne FROM avis WHERE evalue_id = ? AND valide = TRUE");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result && $result['moyenne'] ? round($result['moyenne'], 1) : 0;
        } catch (PDOException $e) {
            error_log("Erreur getAverageRating: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Compter le nombre de trajets proposés
     */
    public function countUserTrips($userId) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM covoiturage WHERE chauffeur_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Erreur countUserTrips: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Compter le nombre de trajets terminés
     */
    public function countCompletedTrips($userId) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM covoiturage WHERE chauffeur_id = ? AND statut = 'termine'");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Erreur countCompletedTrips: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Récupérer les préférences du chauffeur
     */
    public function getChauffeurPreferences($userId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM preferences_chauffeur WHERE chauffeur_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getChauffeurPreferences: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Créer ou mettre à jour les préférences du chauffeur
     */
    public function updateChauffeurPreferences($userId, $preferences) {
        try {
            // Vérifier si les préférences existent déjà
            $existing = $this->getChauffeurPreferences($userId);
            
            if ($existing) {
                // Mise à jour
                $sql = "UPDATE preferences_chauffeur SET 
                        accepte_fumeur = ?, 
                        accepte_animaux = ?, 
                        preferences_custom = ? 
                        WHERE chauffeur_id = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    $preferences['accepte_fumeur'] ?? false,
                    $preferences['accepte_animaux'] ?? false,
                    $preferences['preferences_custom'] ?? '',
                    $userId
                ]);
            } else {
                // Création
                $sql = "INSERT INTO preferences_chauffeur 
                        (chauffeur_id, accepte_fumeur, accepte_animaux, preferences_custom) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    $userId,
                    $preferences['accepte_fumeur'] ?? false,
                    $preferences['accepte_animaux'] ?? false,
                    $preferences['preferences_custom'] ?? ''
                ]);
            }
        } catch (PDOException $e) {
            error_log("Erreur updateChauffeurPreferences: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer les statistiques complètes de l'utilisateur
     */
    public function getUserStats($userId) {
        return [
            'credits' => $this->getUserById($userId)['credits'] ?? 0,
            'trajets_proposes' => $this->countUserTrips($userId),
            'trajets_termines' => $this->countCompletedTrips($userId),
            'note_moyenne' => $this->getAverageRating($userId),
            'nb_avis' => $this->countReceivedReviews($userId)
        ];
    }
    
    /**
     * Compter le nombre d'avis reçus
     */
    private function countReceivedReviews($userId) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM avis WHERE evalue_id = ? AND valide = TRUE");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Erreur countReceivedReviews: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Vérifier si un pseudo est disponible (pour modification)
     */
    public function isPseudoAvailable($pseudo, $excludeUserId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM utilisateur WHERE pseudo = ?";
            $params = [$pseudo];
            
            if ($excludeUserId) {
                $sql .= " AND id != ?";
                $params[] = $excludeUserId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] == 0;
        } catch (PDOException $e) {
            error_log("Erreur isPseudoAvailable: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifier si un email est disponible (pour modification)
     */
    public function isEmailAvailable($email, $excludeUserId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM utilisateur WHERE email = ?";
            $params = [$email];
            
            if ($excludeUserId) {
                $sql .= " AND id != ?";
                $params[] = $excludeUserId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] == 0;
        } catch (PDOException $e) {
            error_log("Erreur isEmailAvailable: " . $e->getMessage());
            return false;
        }
    }

    public function deductCredits($userId, $amount) {
        $sql = "UPDATE utilisateur SET credits = credits - ? WHERE id = ? AND credits >= ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$amount, $userId, $amount]);
    }
    
    public function addCredits($userId, $amount) {
        $sql = "UPDATE utilisateur SET credits = credits + ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$amount, $userId]);
    }
    
    public function getUserCredits($userId) {
        $stmt = $this->db->prepare("SELECT credits FROM utilisateur WHERE id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['credits'] : 0;
    }
}