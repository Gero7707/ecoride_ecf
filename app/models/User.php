<?php
class User {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function login($identifier, $mot_de_passe) {
        // Sanitisation de l'entrée
        $identifier = $this->sanitizeInput($identifier);
        
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
        // Sanitisation des entrées
        $pseudo = $this->sanitizeInput($pseudo);
        $email = $this->sanitizeInput($email);
        $telephone = $telephone ? $this->sanitizeInput($telephone) : null;
        
        // Validation des formats
        if (!$this->validatePseudo($pseudo)) {
            return ['success' => false, 'error' => 'Format de pseudo invalide'];
        }
        
        if (!$this->validateEmail($email)) {
            return ['success' => false, 'error' => 'Format d\'email invalide'];
        }
        
        // Validation mot de passe
        if (!$this->validatePassword($mot_de_passe)) {
            return ['success' => false, 'error' => 'Le mot de passe ne respecte pas les critères de sécurité'];
        }
        
        // Vérification unicité
        if ($this->pseudoExists($pseudo)) {
            return ['success' => false, 'error' => 'Ce pseudo est déjà utilisé'];
        }
        
        if ($this->emailExists($email)) {
            return ['success' => false, 'error' => 'Cet email est déjà utilisé'];
        }
        
        // Hachage sécurisé
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
    
    // Validation du pseudo
    public function validatePseudo($pseudo) {
        return preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $pseudo);
    }
    
    // Validation de l'email
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // Validation du mot de passe
    public function validatePassword($password) {
        // Au moins 8 caractères
        if (strlen($password) < 8) return false;
        
        // Au moins une majuscule
        if (!preg_match('/[A-Z]/', $password)) return false;
        
        // Au moins un chiffre
        if (!preg_match('/[0-9]/', $password)) return false;
        
        // Au moins un caractère spécial
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) return false;
        
        return true;
    }
    
    // Vérifier si pseudo existe
    public function pseudoExists($pseudo) {
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE pseudo = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$pseudo]);
        return $stmt->fetchColumn() > 0;
    }
    
    // Vérifier si email existe
    public function emailExists($email) {
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }
    
    // Sanitisation des entrées
    private function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
?>