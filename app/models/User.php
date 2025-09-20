<?php
class User {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function login($identifier, $mot_de_passe) {
        $sql = "SELECT * FROM utilisateur WHERE (pseudo = ? OR email = ?) AND suspendu = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$identifier, $identifier]);
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            return ['success' => true, 'user' => $user];
        } else {
            return ['success' => false, 'error' => 'Identifiants incorrects'];
        }
    }
    
    public function create($pseudo, $email, $mot_de_passe, $telephone = null) {
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO utilisateur (pseudo, email, mot_de_passe, telephone, credits, statut) 
                VALUES (?, ?, ?, ?, 20, 'passager')";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([$pseudo, $email, $mot_de_passe_hash, $telephone]);
        
        if ($result) {
            return ['success' => true, 'user_id' => $this->pdo->lastInsertId()];
        } else {
            return ['success' => false, 'error' => 'Erreur lors de la création'];
        }
    }
}
?>