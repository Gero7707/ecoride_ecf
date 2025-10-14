<?php
session_start();
require_once 'app/models/Message.php';

echo "<h2>Test du Model Message</h2>";

$messageModel = new Message();

try {
    // Test 1 : Créer une conversation
    echo "<h3>Test 1 : Créer une conversation</h3>";
    $conversation = $messageModel->creerOuRecupererConversation(
        43,           // covoiturage_id
        2,            // chauffeur_id
        37,           // passager_id
        '2025-11-14'  // date_trajet
    );
    echo "✅ Conversation créée : ID = " . $conversation['id'] . "<br><br>";
    
    // Test 2 : Envoyer un message du passager
    echo "<h3>Test 2 : Envoyer un message</h3>";
    $message1 = $messageModel->envoyerMessage(
        $conversation['id'],
        37,
        'passager',
        'Bonjour, à quelle heure exactement ?'
    );
    echo "✅ Message 1 envoyé<br>";
    
    // Test 3 : Réponse du chauffeur
    $message2 = $messageModel->envoyerMessage(
        $conversation['id'],
        2,
        'chauffeur',
        'Bonjour ! On part à 9h00 pile.'
    );
    echo "✅ Message 2 envoyé<br><br>";
    
    // Test 4 : Récupérer les messages
    echo "<h3>Test 3 : Récupérer les messages</h3>";
    $messages = $messageModel->getMessages($conversation['id']);
    foreach ($messages as $msg) {
        echo "- [{$msg['sender_role']}] : {$msg['content']}<br>";
    }
    echo "<br>";
    
    // Test 5 : Compter les non-lus
    echo "<h3>Test 4 : Messages non lus</h3>";
    $conv = $messageModel->getConversation($conversation['id']);
    echo "Messages non lus chauffeur : " . $conv['unread_count_chauffeur'] . "<br>";
    echo "Messages non lus passager : " . $conv['unread_count_passager'] . "<br><br>";
    
    // Test 6 : Marquer comme lu
    echo "<h3>Test 5 : Marquer comme lu</h3>";
    $messageModel->marquerCommeLus($conversation['id'], 2, 'chauffeur');
    echo "✅ Messages marqués comme lus par le chauffeur<br><br>";
    
    echo "<strong style='color: green;'>🎉 Tous les tests passés !</strong>";
    
} catch (Exception $e) {
    echo "<strong style='color: red;'>❌ Erreur : " . $e->getMessage() . "</strong>";
}
?>