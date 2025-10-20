<?php
/**
 * Contrôleur de messagerie
 * Gère l'affichage et les interactions avec la messagerie
 */

require_once 'app/models/Message.php';
require_once 'app/models/User.php';

class MessageController {
    private $pdo;
    private $messageModel;
    private $userModel;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->messageModel = new Message();
        $this->userModel = new User($pdo);
    }
    
    /**
     * Vérifier si l'utilisateur est connecté
     * Redirige vers la page de connexion si non connecté
     */
    private function verifierConnexion() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour accéder à la messagerie';
            header('Location: /connexion');
            exit();
        }
    }
    
    /**
     * Afficher la page principale de messagerie
     * Liste toutes les conversations de l'utilisateur
     */
    public function index() {
        $this->verifierConnexion();
        
        $userId = $_SESSION['user_id'];
        
        // Récupérer toutes les conversations de l'utilisateur depuis MongoDB
        $conversations = $this->messageModel->getConversations($userId);
        
        // Pour chaque conversation, enrichir avec les données MySQL
        $conversationsEnrichies = [];
        
        foreach ($conversations as $conversation) {
            // Déterminer qui est l'autre personne dans la conversation
            $isDriver = ($conversation['chauffeur_id'] === $userId);
            $otherUserId = $isDriver ? $conversation['passager_id'] : $conversation['chauffeur_id'];
            
            // Récupérer les infos de l'autre utilisateur depuis MySQL
            $stmt = $this->pdo->prepare("
                SELECT id, pseudo, photo, statut 
                FROM utilisateur 
                WHERE id = ?
            ");
            $stmt->execute([$otherUserId]);
            $otherUser = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Récupérer les infos du trajet depuis MySQL
            $stmt = $this->pdo->prepare("
                SELECT id, ville_depart, ville_arrivee, date_depart, heure_depart, statut
                FROM covoiturage 
                WHERE id = ?
            ");
            $stmt->execute([$conversation['covoiturage_id']]);
            $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Compter les messages non lus pour cet utilisateur
            $unreadCount = $isDriver 
                ? $conversation['unread_count_chauffeur'] 
                : $conversation['unread_count_passager'];
            
            // Ajouter toutes ces infos à la conversation
            $conversationsEnrichies[] = [
                'conversation' => $conversation,
                'other_user' => $otherUser,
                'trajet' => $trajet,
                'is_driver' => $isDriver,
                'unread_count' => $unreadCount,
                'last_message_date' => date('d/m/Y H:i', $conversation['last_message_at'])
            ];
        }
        
        // Trier par date de dernier message (déjà trié par MongoDB, mais on s'assure)
        usort($conversationsEnrichies, function($a, $b) {
            return $b['conversation']['last_message_at'] - $a['conversation']['last_message_at'];
        });
        
        // Compter le total de messages non lus
        $totalUnread = $this->messageModel->countUnreadMessages($userId);
        
        // Envoyer à la vue
        $data = [
            'conversations' => $conversationsEnrichies,
            'totalUnread' => $totalUnread
        ];
        
        include 'app/views/messagerie/index.php';
    }
    
    /**
     * Afficher une conversation spécifique
     * 
     * @param string $conversationId - ID MongoDB de la conversation
     */
    public function conversation($conversationId) {
        $this->verifierConnexion();
        
        $userId = $_SESSION['user_id'];
        
        // Récupérer la conversation depuis MongoDB
        $conversation = $this->messageModel->getConversation($conversationId);
        
        if (!$conversation) {
            $_SESSION['error'] = 'Conversation introuvable';
            header('Location: /messagerie');
            exit();
        }
        
        // SÉCURITÉ : Vérifier que l'utilisateur fait partie de cette conversation
        if ($conversation['chauffeur_id'] !== $userId && $conversation['passager_id'] !== $userId) {
            $_SESSION['error'] = 'Vous n\'avez pas accès à cette conversation';
            header('Location: /messagerie');
            exit();
        }
        
        // Déterminer le rôle de l'utilisateur
        $isDriver = ($conversation['chauffeur_id'] === $userId);
        $userRole = $isDriver ? 'chauffeur' : 'passager';
        $otherUserId = $isDriver ? $conversation['passager_id'] : $conversation['chauffeur_id'];
        
        // Récupérer l'autre utilisateur depuis MySQL
        $stmt = $this->pdo->prepare("
            SELECT id, pseudo, photo, statut 
            FROM utilisateur 
            WHERE id = ?
        ");
        $stmt->execute([$otherUserId]);
        $otherUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Récupérer le trajet depuis MySQL
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.pseudo as chauffeur_pseudo, v.marque, v.modele, v.couleur
            FROM covoiturage c
            JOIN utilisateur u ON c.chauffeur_id = u.id
            LEFT JOIN vehicule v ON c.vehicule_id = v.id
            WHERE c.id = ?
        ");
        $stmt->execute([$conversation['covoiturage_id']]);
        $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Récupérer tous les messages de la conversation depuis MongoDB
        $messages = $this->messageModel->getMessages($conversationId);
        
        // Marquer les messages comme lus
        $this->messageModel->marquerCommeLus($conversationId, $userId, $userRole);
        
        // Préparer les données pour la vue
        $data = [
            'conversation' => $conversation,
            'conversation_id' => $conversationId,
            'messages' => $messages,
            'other_user' => $otherUser,
            'trajet' => $trajet,
            'is_driver' => $isDriver,
            'user_role' => $userRole,
            'current_user_id' => $userId
        ];
        
        include 'app/views/messagerie/conversation.php';
    }
    
    /**
     * Créer une nouvelle conversation
     * Peut être appelé par le chauffeur OU le passager
     * 
     * @param int $covoiturageId - ID du trajet
     */
    public function creerConversation($covoiturageId) {
        $this->verifierConnexion();
        
        $userId = $_SESSION['user_id'];

        
        
        // Récupérer les infos du trajet
        $stmt = $this->pdo->prepare("
            SELECT chauffeur_id, date_depart 
            FROM covoiturage 
            WHERE id = ?
        ");
        $stmt->execute([$covoiturageId]);
        $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 🔍 DEBUG
        error_log("Trajet trouvé: " . ($trajet ? 'OUI' : 'NON'));
        if (!$trajet) {
            $_SESSION['error'] = 'Trajet introuvable';
            header('Location: /');
            exit();
        }
        
        // Déterminer si l'utilisateur est le chauffeur ou un passager
        $isChauffeur = ($trajet['chauffeur_id'] === $userId);
        
        if ($isChauffeur) {
            // CAS 1 : L'utilisateur est le CHAUFFEUR
            // Il veut contacter un de ses passagers
            
            // Récupérer l'ID du passager depuis l'URL (?passager=37)
            $passagerId = $_GET['passager'] ?? null;
            
            if (!$passagerId) {
                $_SESSION['error'] = 'Passager non spécifié';
                header('Location: /covoiturage/' . $covoiturageId);
                exit();
            }
            
            // Vérifier que ce passager a bien réservé ce trajet
            $stmt = $this->pdo->prepare("
                SELECT id 
                FROM reservation 
                WHERE covoiturage_id = ? AND passager_id = ? AND statut IN ('en_attente', 'confirmee')
            ");
            $stmt->execute([$covoiturageId, $passagerId]);
            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$reservation) {
                $_SESSION['error'] = 'Ce passager n\'a pas réservé ce trajet';
                header('Location: /covoiturage/' . $covoiturageId . '/passagers');
                exit();
            }
            
            // Créer ou récupérer la conversation
            $conversation = $this->messageModel->creerOuRecupererConversation(
                $covoiturageId,
                $userId,           // chauffeur_id
                $passagerId,       // passager_id
                $trajet['date_depart']
            );
            
        } else {
            // CAS 2 : L'utilisateur est un PASSAGER
            // Il veut contacter le chauffeur
            
            // Vérifier que le passager a bien réservé ce trajet
            $stmt = $this->pdo->prepare("
                SELECT id 
                FROM reservation 
                WHERE covoiturage_id = ? AND passager_id = ? AND statut = 'confirmee'
            ");
            $stmt->execute([$covoiturageId, $userId]);
            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$reservation) {
                $_SESSION['error'] = 'Vous devez avoir une réservation confirmée pour créer une conversation';
                header('Location: /covoiturage/' . $covoiturageId);
                exit();
            }
            
            // Créer ou récupérer la conversation
            $conversation = $this->messageModel->creerOuRecupererConversation(
                $covoiturageId,
                $trajet['chauffeur_id'],  // chauffeur_id
                $userId,                   // passager_id
                $trajet['date_depart']
            );
        }
        
        // Rediriger vers la conversation
        header('Location: /messagerie/conversation/' . $conversation['id']);
        exit();
    }
    
    /**
     * Récupérer le nombre de messages non lus (pour le badge de notification)
     * Retourne du JSON
     */
    public function getUnreadCount() {
        $this->verifierConnexion();
        
        $userId = $_SESSION['user_id'];
        $unreadCount = $this->messageModel->countUnreadMessages($userId);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
        exit();
    }
}