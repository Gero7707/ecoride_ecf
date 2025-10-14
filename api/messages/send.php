<?php
/**
 * API - Envoyer un message
 * Endpoint appelé par JavaScript pour envoyer un message en temps réel
 * 
 * Méthode : POST
 * Paramètres :
 *   - conversation_id : ID MongoDB de la conversation
 *   - content : Contenu du message
 * 
 * Retourne : JSON
 */

// Démarrer la session
session_start();

// Headers pour l'API JSON
header('Content-Type: application/json');

// Vérifier que c'est bien une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'success' => false,
        'error' => 'Méthode non autorisée'
    ]);
    exit();
}

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
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
    // Récupérer les données envoyées par JavaScript
    // $_POST pour les formulaires, json_decode pour fetch()
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Si pas de JSON, essayer $_POST (compatibilité)
    if (!$input) {
        $input = $_POST;
    }
    
    $conversationId = $input['conversation_id'] ?? null;
    $content = $input['content'] ?? null;
    
    // Validation
    if (!$conversationId || !$content) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'error' => 'Paramètres manquants'
        ]);
        exit();
    }
    
    // Nettoyer le contenu
    $content = trim($content);
    
    if (empty($content)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Le message ne peut pas être vide'
        ]);
        exit();
    }
    
    // Limiter la longueur du message
    if (strlen($content) > 1000) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Le message est trop long (max 1000 caractères)'
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
        http_response_code(403); // Forbidden
        echo json_encode([
            'success' => false,
            'error' => 'Vous n\'avez pas accès à cette conversation'
        ]);
        exit();
    }
    
    // Déterminer le rôle de l'utilisateur
    $userRole = $isDriver ? 'chauffeur' : 'passager';
    
    // Envoyer le message
    $message = $messageModel->envoyerMessage(
        $conversationId,
        $userId,
        $userRole,
        $content
    );
    
    // Récupérer les infos de l'utilisateur pour l'affichage
    $stmt = $pdo->prepare("SELECT pseudo, photo FROM utilisateur WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Ajouter les infos utilisateur au message
    $message['sender_pseudo'] = $user['pseudo'];
    $message['sender_photo'] = $user['photo'];
    
    // Formater la date
    $message['created_at_formatted'] = date('H:i', $message['created_at']);
    
    // Retourner le succès avec le message
    echo json_encode([
        'success' => true,
        'message' => $message
    ]);
    
} catch (Exception $e) {
    // Log l'erreur (en production, utilise un système de logs)
    error_log("Erreur API send.php : " . $e->getMessage());
    
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur lors de l\'envoi du message'
    ]);
}