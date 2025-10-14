<?php
/**
 * API - Récupérer les nouveaux messages
 * Endpoint appelé régulièrement par JavaScript pour vérifier les nouveaux messages
 * 
 * Méthode : GET
 * Paramètres :
 *   - conversation_id : ID MongoDB de la conversation
 *   - since : Timestamp du dernier message connu (optionnel)
 * 
 * Retourne : JSON avec les nouveaux messages
 */

// Démarrer la session
session_start();

// Headers pour l'API JSON
header('Content-Type: application/json');

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Vous devez être connecté'
    ]);
    exit();
}

// Charger les dépendances
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/models/Message.php';

try {
    // Récupérer les paramètres GET
    $conversationId = $_GET['conversation_id'] ?? null;
    $since = $_GET['since'] ?? null;
    
    // Validation
    if (!$conversationId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'ID de conversation manquant'
        ]);
        exit();
    }
    
    // Initialiser le model
    $messageModel = new Message();
    
    // Récupérer la conversation pour vérifier les permissions
    $conversation = $messageModel->getConversation($conversationId);
    
    if (!$conversation) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Conversation introuvable'
        ]);
        exit();
    }
    
    // SÉCURITÉ : Vérifier que l'utilisateur fait partie de cette conversation
    $userId = $_SESSION['user_id'];
    $isDriver = ($conversation['chauffeur_id'] === $userId);
    $isPassenger = ($conversation['passager_id'] === $userId);
    
    if (!$isDriver && !$isPassenger) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'Vous n\'avez pas accès à cette conversation'
        ]);
        exit();
    }
    
    // Récupérer les messages
    // Si $since est fourni, on récupère seulement les nouveaux
    $messages = $messageModel->getMessages($conversationId, 50, $since);
    
    // Pour chaque message, enrichir avec les infos utilisateur
    $messagesEnriches = [];
    
    // Récupérer tous les IDs utilisateurs uniques
    $userIds = array_unique(array_column($messages, 'sender_id'));
    
    // Requête optimisée pour récupérer tous les utilisateurs d'un coup
    if (!empty($userIds)) {
        $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
        $stmt = $pdo->prepare("
            SELECT id, pseudo, photo 
            FROM utilisateur 
            WHERE id IN ($placeholders)
        ");
        $stmt->execute($userIds);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Créer un tableau associatif pour un accès rapide
        $usersMap = [];
        foreach ($users as $user) {
            $usersMap[$user['id']] = $user;
        }
        
        // Enrichir chaque message
        foreach ($messages as $message) {
            $user = $usersMap[$message['sender_id']] ?? null;
            
            $messagesEnriches[] = [
                'id' => $message['id'],
                'conversation_id' => $message['conversation_id'],
                'sender_id' => $message['sender_id'],
                'sender_role' => $message['sender_role'],
                'sender_pseudo' => $user['pseudo'] ?? 'Utilisateur',
                'sender_photo' => $user['photo'] ?? null,
                'content' => $message['content'],
                'read' => $message['read'],
                'created_at' => $message['created_at'],
                'created_at_formatted' => date('H:i', $message['created_at']),
                'is_own_message' => ($message['sender_id'] === $userId)
            ];
        }
    }
    
    // Retourner les messages
    echo json_encode([
        'success' => true,
        'messages' => $messagesEnriches,
        'count' => count($messagesEnriches)
    ]);
    
} catch (Exception $e) {
    error_log("Erreur API get_messages.php : " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur'
    ]);
}