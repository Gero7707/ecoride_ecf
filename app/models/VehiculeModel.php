<?php
class VehiculeModel {
    private $db;
    
    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }
    
    public function create($data) {
        try {
            $sql = "INSERT INTO vehicule (utilisateur_id, marque, modele, couleur, 
                    plaque_immatriculation, date_premiere_immatriculation, nombre_places, energie) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['utilisateur_id'],
                $data['marque'],
                $data['modele'],
                $data['couleur'],
                $data['plaque_immatriculation'],
                $data['date_premiere_immatriculation'],
                $data['nombre_places'],
                $data['energie']
            ]);
        } catch (PDOException $e) {
            error_log("Erreur create vehicule: " . $e->getMessage());
            return false;
        }
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM vehicule WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE vehicule SET marque = ?, modele = ?, couleur = ?, 
                nombre_places = ?, energie = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['marque'], $data['modele'], $data['couleur'],
            $data['nombre_places'], $data['energie'], $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM vehicule WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function plaqueExists($plaque, $excludeUserId = null) {
        $sql = "SELECT COUNT(*) as count FROM vehicule WHERE plaque_immatriculation = ?";
        $params = [$plaque];
        
        if ($excludeUserId) {
            $sql .= " AND utilisateur_id != ?";
            $params[] = $excludeUserId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    public function hasActiveTrips($vehiculeId) {
        $sql = "SELECT COUNT(*) as count FROM covoiturage 
                WHERE vehicule_id = ? AND statut IN ('prevu', 'en_cours')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$vehiculeId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}