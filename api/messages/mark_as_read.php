<?php
/**
 * API - Marquer les messages comme lus
 * Endpoint appelé quand l'utilisateur ouvre une conversation
 * 
 * Méthode : POST
 * Paramètres :
 *   - conversation_id : ID MongoDB de la conversation
 * 
 * Retourne : JSON
 */

// Démarrer la session
session_start();

// Headers pour l'API JSON
header('Content-Type: application/json');

// Vérifier que c'est bien une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Méthode non autorisée'
    ]);
    exit();
}

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
    // Récupérer les données
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    $conversationId = $input['conversation_id'] ?? null;
    
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
    
    // Récupérer la conversation
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
    
    // Déterminer le rôle
    $userRole = $isDriver ? 'chauffeur' : 'passager';
    
    // Marquer comme lu
    $messageModel->marquerCommeLus($conversationId, $userId, $userRole);
    
    // Retourner le succès
    echo json_encode([
        'success' => true,
        'message' => 'Messages marqués comme lus'
    ]);
    
} catch (Exception $e) {
    error_log("Erreur API mark_as_read.php : " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur'
    ]);
}