<?php
/**
 * Contrôleur utilisateur
 * Gère l'affichage des profils publics
 */

class UserController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Afficher le profil public d'un utilisateur
     */
    public function showPublicProfile($userId) {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté';
            header('Location: /connexion');
            exit();
        }
        
        // Récupérer les infos de l'utilisateur
        $stmt = $this->pdo->prepare("
            SELECT 
                u.id,
                u.pseudo,
                u.photo,
                u.statut,
                u.date_creation,
                COALESCE(AVG(a.note), 0) as note_moyenne,
                COUNT(DISTINCT a.id) as nombre_avis
            FROM utilisateur u
            LEFT JOIN avis a ON a.evalue_id = u.id AND a.valide = 1
            WHERE u.id = ?
            GROUP BY u.id
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur introuvable';
            header('Location: /');
            exit();
        }
        
        // Récupérer les avis
        $stmt = $this->pdo->prepare("
            SELECT 
                a.*,
                u.pseudo as evaluateur_pseudo,
                c.ville_depart,
                c.ville_arrivee,
                c.date_depart
            FROM avis a
            JOIN utilisateur u ON a.evaluateur_id = u.id
            JOIN covoiturage c ON a.covoiturage_id = c.id
            WHERE a.evalue_id = ? AND a.valide = 1
            ORDER BY a.date_creation DESC
            LIMIT 10
        ");
        $stmt->execute([$userId]);
        $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Statistiques selon le statut
        if ($user['statut'] === 'chauffeur' || $user['statut'] === 'admin') {
            // Nombre de trajets en tant que chauffeur
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as nb_trajets
                FROM covoiturage
                WHERE chauffeur_id = ?
            ");
            $stmt->execute([$userId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            $user['nb_trajets'] = $stats['nb_trajets'];
        } else {
            // Nombre de réservations en tant que passager
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as nb_reservations
                FROM reservation
                WHERE passager_id = ? AND statut = 'confirmee'
            ");
            $stmt->execute([$userId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            $user['nb_reservations'] = $stats['nb_reservations'];
        }
        
        // Calculer l'ancienneté
        $dateCreation = new DateTime($user['date_creation']);
        $now = new DateTime();
        $interval = $dateCreation->diff($now);
        $user['anciennete'] = $interval->format('%y an(s) et %m mois');
        
        // Vérifier s'il y a une conversation existante
        $conversationExists = false;
        if ($_SESSION['user_id'] !== (int)$userId) {
            require_once 'app/models/Message.php';
            $messageModel = new Message();
            
            // Chercher une conversation existante
            $conversations = $messageModel->getConversations($_SESSION['user_id']);
            foreach ($conversations as $conv) {
                if ($conv['chauffeur_id'] === (int)$userId || $conv['passager_id'] === (int)$userId) {
                    $conversationExists = $conv['id'];
                    break;
                }
            }
        }
        
        // Passer les données à la vue
        $data = [
            'user' => $user,
            'avis' => $avis,
            'conversationExists' => $conversationExists,
            'isOwnProfile' => ($_SESSION['user_id'] === (int)$userId)
        ];
        
        include 'app/views/user/profile.php';
    }
}