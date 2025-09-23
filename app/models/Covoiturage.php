<?php
class Covoiturage {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Rechercher des covoiturages selon les critères (sans date obligatoire)
     */
    public function search($depart, $arrivee, $date = null, $filters = []) {
        $sql = "SELECT c.*, u.pseudo, v.marque, v.modele, v.energie,
                       AVG(a.note) as note_moyenne
                FROM covoiturage c
                JOIN utilisateur u ON c.chauffeur_id = u.id
                JOIN vehicule v ON c.vehicule_id = v.id
                LEFT JOIN avis a ON a.evalue_id = c.chauffeur_id
                WHERE c.ville_depart LIKE :depart 
                AND c.ville_arrivee LIKE :arrivee
                AND c.date_depart >= CURDATE()
                AND c.places_disponibles > 0
                AND c.statut = 'prevu'";
        
        $params = [
            ':depart' => '%' . $depart . '%',
            ':arrivee' => '%' . $arrivee . '%'
        ];
        
        // Si une date spécifique est demandée, l'ajouter
        if (!empty($date)) {
            $sql .= " AND c.date_depart = :date";
            $params[':date'] = $date;
        }
        
        // Filtre écologique
        if (isset($filters['ecologique']) && $filters['ecologique']) {
            $sql .= " AND v.energie = 'electrique'";
        }
        
        // Filtre prix maximum
        if (isset($filters['prix_max']) && !empty($filters['prix_max'])) {
            $sql .= " AND c.prix <= :prix_max";
            $params[':prix_max'] = $filters['prix_max'];
        }
        
        $sql .= " GROUP BY c.id";
        
        // Filtre note minimale
        if (isset($filters['note_min']) && !empty($filters['note_min'])) {
            $sql .= " HAVING note_moyenne >= :note_min";
            $params[':note_min'] = $filters['note_min'];
        }
        
        $sql .= " ORDER BY c.date_depart ASC, c.heure_depart ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Trouver la prochaine date disponible pour un trajet
     */
    public function findNextAvailableDate($depart, $arrivee) {
        $sql = "SELECT c.date_depart
                FROM covoiturage c
                JOIN vehicule v ON c.vehicule_id = v.id
                WHERE c.ville_depart LIKE :depart 
                AND c.ville_arrivee LIKE :arrivee
                AND c.places_disponibles > 0
                AND c.statut = 'prevu'
                AND c.date_depart >= CURDATE()
                ORDER BY c.date_depart ASC
                LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':depart' => '%' . $depart . '%',
            ':arrivee' => '%' . $arrivee . '%'
        ]);
        
        $result = $stmt->fetch();
        return $result ? $result['date_depart'] : null;
    }
    
    /**
     * Récupérer un covoiturage par son ID
     */
    public function getById($id) {
        $sql = "SELECT c.*, u.pseudo, u.telephone, v.marque, v.modele, v.energie, v.couleur,
                       AVG(a.note) as note_moyenne, COUNT(a.id) as nb_avis
                FROM covoiturage c
                JOIN utilisateur u ON c.chauffeur_id = u.id
                JOIN vehicule v ON c.vehicule_id = v.id
                LEFT JOIN avis a ON a.evalue_id = c.chauffeur_id AND a.valide = 1
                WHERE c.id = :id
                GROUP BY c.id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch();
    }
    
    /**
     * Récupérer les avis d'un chauffeur
     */
    public function getDriverReviews($chauffeur_id) {
        $sql = "SELECT a.*, u.pseudo as evaluateur_pseudo, c.ville_depart, c.ville_arrivee
                FROM avis a
                JOIN utilisateur u ON a.evaluateur_id = u.id
                JOIN covoiturage c ON a.covoiturage_id = c.id
                WHERE a.evalue_id = :chauffeur_id AND a.valide = 1
                ORDER BY a.date_creation DESC
                LIMIT 5";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':chauffeur_id' => $chauffeur_id]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Créer un nouveau covoiturage
     */
    public function create($chauffeur_id, $vehicule_id, $ville_depart, $ville_arrivee, 
                          $date_depart, $heure_depart, $heure_arrivee, $prix, $places_disponibles) {
        
        $sql = "INSERT INTO covoiturage (chauffeur_id, vehicule_id, ville_depart, ville_arrivee,
                                       date_depart, heure_depart, heure_arrivee, prix, places_disponibles, statut)
                VALUES (:chauffeur_id, :vehicule_id, :ville_depart, :ville_arrivee,
                        :date_depart, :heure_depart, :heure_arrivee, :prix, :places_disponibles, 'prevu')";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':chauffeur_id' => $chauffeur_id,
            ':vehicule_id' => $vehicule_id,
            ':ville_depart' => trim($ville_depart),
            ':ville_arrivee' => trim($ville_arrivee),
            ':date_depart' => $date_depart,
            ':heure_depart' => $heure_depart,
            ':heure_arrivee' => $heure_arrivee,
            ':prix' => $prix,
            ':places_disponibles' => $places_disponibles
        ]);
        
        if ($result) {
            return ['success' => true, 'covoiturage_id' => $this->pdo->lastInsertId()];
        } else {
            return ['success' => false, 'error' => 'Erreur lors de la création du covoiturage'];
        }
    }
}
?>