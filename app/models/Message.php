<?php
/**
 * Model Message
 * Gère toutes les opérations sur les messages et conversations MongoDB
 */

require_once __DIR__ . '/../../config/mongodb.php';

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;



class Message {
    private $db;
    private $conversations;
    private $messages;
    
    public function __construct() {
        // Récupérer la connexion MongoDB
        $this->db = getMongoConnection();
        
        // Accéder aux collections (équivalent des tables en SQL)
        $this->conversations = $this->db->conversations;
        $this->messages = $this->db->messages;
    }
    
    /**
     * Créer ou récupérer une conversation entre un chauffeur et un passager pour un trajet
     * 
     * @param int $covoiturage_id - ID du trajet
     * @param int $chauffeur_id - ID du chauffeur
     * @param int $passager_id - ID du passager
     * @param string $date_trajet - Date du trajet (format Y-m-d)
     * @return array - Conversation créée ou existante
     */
    public function creerOuRecupererConversation($covoiturage_id, $chauffeur_id, $passager_id, $date_trajet) {
        // Vérifier si une conversation existe déjà pour ce trajet entre ces 2 personnes
        $conversationExistante = $this->conversations->findOne([
            'covoiturage_id' => (int)$covoiturage_id,
            'chauffeur_id' => (int)$chauffeur_id,
            'passager_id' => (int)$passager_id
        ]);
        
        // Si elle existe, la retourner
        if ($conversationExistante) {
            return $this->formatConversation($conversationExistante);
        }
        
        // Sinon, créer une nouvelle conversation
        $maintenant = new MongoDB\BSON\UTCDateTime();
        
        // Calculer la date d'expiration : 7 jours après le trajet
        $dateExpiration = strtotime($date_trajet . ' +7 days');
        $expiresAt = new MongoDB\BSON\UTCDateTime($dateExpiration * 1000); // MongoDB utilise les millisecondes
        
        $nouvelleConversation = [
            'covoiturage_id' => (int)$covoiturage_id,
            'chauffeur_id' => (int)$chauffeur_id,
            'passager_id' => (int)$passager_id,
            'created_at' => $maintenant,
            'expires_at' => $expiresAt,
            'last_message_at' => $maintenant,
            'unread_count_chauffeur' => 0,  // Nombre de messages non lus par le chauffeur
            'unread_count_passager' => 0,   // Nombre de messages non lus par le passager
            'statut' => 'active'  // active, archived, deleted
        ];
        
        $result = $this->conversations->insertOne($nouvelleConversation);
        $nouvelleConversation['_id'] = $result->getInsertedId();
        
        return $this->formatConversation($nouvelleConversation);
    }
    
    /**
     * Envoyer un message dans une conversation
     * 
     * @param string $conversation_id - ID MongoDB de la conversation
     * @param int $sender_id - ID de l'utilisateur qui envoie
     * @param string $sender_role - 'chauffeur' ou 'passager'
     * @param string $content - Contenu du message
     * @return array - Message créé
     */
    public function envoyerMessage($conversation_id, $sender_id, $sender_role, $content) {
        // Vérifier que le contenu n'est pas vide
        $content = trim($content);
        if (empty($content)) {
            throw new Exception("Le message ne peut pas être vide");
        }
        
        $maintenant = new MongoDB\BSON\UTCDateTime();
        
        // Créer le message
        $nouveauMessage = [
            'conversation_id' => new MongoDB\BSON\ObjectId($conversation_id),
            'sender_id' => (int)$sender_id,
            'sender_role' => $sender_role,  // 'chauffeur' ou 'passager'
            'content' => htmlspecialchars($content, ENT_QUOTES, 'UTF-8'),  // Sécurité XSS
            'read' => false,  // Non lu par défaut
            'created_at' => $maintenant
        ];
        
        $result = $this->messages->insertOne($nouveauMessage);
        
        // Mettre à jour la conversation
        $updateData = [
            'last_message_at' => $maintenant
        ];
        
        // Incrémenter le compteur de non-lus pour le destinataire
        if ($sender_role === 'chauffeur') {
            // Si c'est le chauffeur qui envoie, incrémenter le compteur du passager
            $updateData['unread_count_passager'] = ['$inc' => 1];
        } else {
            // Si c'est le passager qui envoie, incrémenter le compteur du chauffeur
            $updateData['unread_count_chauffeur'] = ['$inc' => 1];
        }
        
        $this->conversations->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($conversation_id)],
            [
                '$set' => ['last_message_at' => $maintenant],
                '$inc' => $sender_role === 'chauffeur' 
                    ? ['unread_count_passager' => 1] 
                    : ['unread_count_chauffeur' => 1]
            ]
        );
        
        $nouveauMessage['_id'] = $result->getInsertedId();
        return $this->formatMessage($nouveauMessage);
    }
    
    /**
     * Récupérer toutes les conversations d'un utilisateur
     * 
     * @param int $user_id - ID de l'utilisateur
     * @param string $user_role - 'chauffeur' ou 'passager'
     * @return array - Liste des conversations
     */
    public function getConversations($user_id, $user_role = null) {
        // Construire le filtre de recherche
        $filter = [
            '$or' => [
                ['chauffeur_id' => (int)$user_id],
                ['passager_id' => (int)$user_id]
            ],
            'statut' => 'active'
        ];
        
        // Trier par dernier message (plus récent en premier)
        $options = [
            'sort' => ['last_message_at' => -1]
        ];
        
        $conversationsCursor = $this->conversations->find($filter, $options);
        
        $conversationsArray = [];
        foreach ($conversationsCursor as $conversation) {
            $conversationsArray[] = $this->formatConversation($conversation);
        }
        
        return $conversationsArray;
    }
    
    /**
     * Récupérer les messages d'une conversation
     * 
     * @param string $conversation_id - ID MongoDB de la conversation
     * @param int $limit - Nombre maximum de messages à récupérer
     * @param string $since - Timestamp pour récupérer seulement les nouveaux messages
     * @return array - Liste des messages
     */
    public function getMessages($conversation_id, $limit = 50, $since = null) {
        $filter = [
            'conversation_id' => new MongoDB\BSON\ObjectId($conversation_id)
        ];
        
        // Si on veut seulement les nouveaux messages depuis un timestamp
        if ($since !== null) {
            $sinceDate = new MongoDB\BSON\UTCDateTime((int)$since * 1000);
            $filter['created_at'] = ['$gt' => $sinceDate];
        }
        
        $options = [
            'sort' => ['created_at' => 1],  // Ordre chronologique (plus ancien en premier)
            'limit' => $limit
        ];
        
        $messagesCursor = $this->messages->find($filter, $options);
        
        $messagesArray = [];
        foreach ($messagesCursor as $message) {
            $messagesArray[] = $this->formatMessage($message);
        }
        
        return $messagesArray;
    }
    
    /**
     * Marquer les messages d'une conversation comme lus
     * 
     * @param string $conversation_id - ID MongoDB de la conversation
     * @param int $user_id - ID de l'utilisateur qui lit
     * @param string $user_role - 'chauffeur' ou 'passager'
     * @return bool
     */
    public function marquerCommeLus($conversation_id, $user_id, $user_role) {
        // Marquer tous les messages non lus de l'autre personne comme lus
        $this->messages->updateMany(
            [
                'conversation_id' => new MongoDB\BSON\ObjectId($conversation_id),
                'sender_role' => $user_role === 'chauffeur' ? 'passager' : 'chauffeur',
                'read' => false
            ],
            [
                '$set' => ['read' => true]
            ]
        );
        
        // Réinitialiser le compteur de non-lus dans la conversation
        $updateField = $user_role === 'chauffeur' 
            ? 'unread_count_chauffeur' 
            : 'unread_count_passager';
        
        $this->conversations->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($conversation_id)],
            ['$set' => [$updateField => 0]]
        );
        
        return true;
    }
    
    /**
     * Récupérer une conversation par son ID
     * 
     * @param string $conversation_id - ID MongoDB de la conversation
     * @return array|null
     */
    public function getConversation($conversation_id) {
        try {
            $conversation = $this->conversations->findOne([
                '_id' => new MongoDB\BSON\ObjectId($conversation_id)
            ]);
            
            if (!$conversation) {
                return null;
            }
            
            return $this->formatConversation($conversation);
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Compter le nombre total de messages non lus pour un utilisateur
     * 
     * @param int $user_id - ID de l'utilisateur
     * @return int - Nombre total de messages non lus
     */
    public function countUnreadMessages($user_id) {
        // Récupérer toutes les conversations de l'utilisateur
        $conversations = $this->getConversations($user_id);
        
        $totalUnread = 0;
        foreach ($conversations as $conv) {
            // Déterminer si l'utilisateur est chauffeur ou passager
            if ($conv['chauffeur_id'] === (int)$user_id) {
                $totalUnread += $conv['unread_count_chauffeur'];
            } else {
                $totalUnread += $conv['unread_count_passager'];
            }
        }
        
        return $totalUnread;
    }
    
    /**
     * Supprimer les conversations expirées (7 jours après le trajet)
     * À exécuter périodiquement (cron job)
     * 
     * @return int - Nombre de conversations supprimées
     */
    public function supprimerConversationsExpirees() {
        $maintenant = new MongoDB\BSON\UTCDateTime();
        
        // Trouver les conversations expirées
        $conversationsExpirees = $this->conversations->find([
            'expires_at' => ['$lt' => $maintenant],
            'statut' => 'active'
        ]);
        
        $count = 0;
        foreach ($conversationsExpirees as $conversation) {
            // Supprimer tous les messages de cette conversation
            $this->messages->deleteMany([
                'conversation_id' => $conversation['_id']
            ]);
            
            // Marquer la conversation comme supprimée
            $this->conversations->updateOne(
                ['_id' => $conversation['_id']],
                ['$set' => ['statut' => 'deleted']]
            );
            
            $count++;
        }
        
        return $count;
    }
    
    /**
     * Formater une conversation pour l'affichage
     * Convertit les objets MongoDB en array PHP standard
     */
    private function formatConversation($conversation) {
        return [
            'id' => (string)$conversation['_id'],
            'covoiturage_id' => $conversation['covoiturage_id'],
            'chauffeur_id' => $conversation['chauffeur_id'],
            'passager_id' => $conversation['passager_id'],
            'created_at' => $conversation['created_at']->toDateTime()->getTimestamp(),
            'expires_at' => $conversation['expires_at']->toDateTime()->getTimestamp(),
            'last_message_at' => $conversation['last_message_at']->toDateTime()->getTimestamp(),
            'unread_count_chauffeur' => $conversation['unread_count_chauffeur'],
            'unread_count_passager' => $conversation['unread_count_passager'],
            'statut' => $conversation['statut']
        ];
    }
    
    /**
     * Formater un message pour l'affichage
     * Convertit les objets MongoDB en array PHP standard
     */
    private function formatMessage($message) {
        return [
            'id' => (string)$message['_id'],
            'conversation_id' => (string)$message['conversation_id'],
            'sender_id' => $message['sender_id'],
            'sender_role' => $message['sender_role'],
            'content' => $message['content'],
            'read' => $message['read'],
            'created_at' => $message['created_at']->toDateTime()->getTimestamp()
        ];
    }
}