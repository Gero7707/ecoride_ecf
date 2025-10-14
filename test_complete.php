<?php
session_start();
require_once 'config/database.php';
require_once 'app/models/Message.php';

// Simule une connexion (remplace par un vrai user_id de ta BDD)
$_SESSION['user_id'] = 37;  // ID d'un passager
$_SESSION['user_pseudo'] = 'alex_pass1';
$_SESSION['user_statut'] = 'passager';

$messageModel = new Message();

// Créer une conversation pour le trajet #43
$conversation = $messageModel->creerOuRecupererConversation(
    43,           // covoiturage_id
    2,            // chauffeur_id (marie_paris)
    37,           // passager_id (alex_pass1)
    '2025-11-14'  // date_trajet
);

echo "✅ Conversation créée !<br>";
echo "ID : " . $conversation['id'] . "<br><br>";
echo "<a href='/messagerie'>Voir la messagerie</a>";
?>