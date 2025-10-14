<?php
/**
 * Configuration et connexion MongoDB
 * Ce fichier crée une connexion à MongoDB pour la messagerie
 * 
 * Pourquoi MongoDB pour la messagerie ?
 * - Parfait pour stocker des documents (messages) avec des structures flexibles
 * - Très rapide pour les lectures/écritures fréquentes (temps réel)
 * - Pas besoin de jointures complexes comme SQL
 */

require_once __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;

// Configuration de connexion
define('MONGODB_HOST', 'localhost');
define('MONGODB_PORT', 27017);
define('MONGODB_DATABASE', 'ecoride_messages');

/**
 * Fonction pour obtenir une connexion MongoDB
 * @return MongoDB\Database - Instance de la base de données
 */
function getMongoConnection() {
    try {
        // Créer le client MongoDB (équivalent de PDO pour MySQL)
        // mongodb://localhost:27017 = URL de connexion à MongoDB
        $client = new MongoDB\Client("mongodb://" . MONGODB_HOST . ":" . MONGODB_PORT);
        
        // Sélectionner la base de données (équivalent de USE ecoride_messages en SQL)
        $database = $client->selectDatabase(MONGODB_DATABASE);
        
        return $database;
        
    } catch (Exception $e) {
        // En cas d'erreur de connexion, logger et arrêter
        error_log("Erreur connexion MongoDB : " . $e->getMessage());
        die("Erreur de connexion à la base de données de messagerie");
    }
}

/**
 * Fonction pour créer les index nécessaires sur les collections
 * Les index améliorent les performances des recherches
 * À exécuter une seule fois lors de la première installation
 */
function createMongoIndexes() {
    $db = getMongoConnection();
    
    // Collection conversations
    $conversations = $db->conversations;
    
    // Index pour rechercher rapidement les conversations d'un utilisateur
    $conversations->createIndex(['chauffeur_id' => 1]); // 1 = ordre croissant
    $conversations->createIndex(['passager_id' => 1]);
    $conversations->createIndex(['covoiturage_id' => 1]);
    
    // Index pour trier par date du dernier message
    $conversations->createIndex(['last_message_at' => -1]); // -1 = ordre décroissant (plus récent en premier)
    
    // Index pour supprimer les conversations expirées
    $conversations->createIndex(['expires_at' => 1]);
    
    // Collection messages
    $messages = $db->messages;
    
    // Index pour récupérer rapidement les messages d'une conversation
    $messages->createIndex(['conversation_id' => 1, 'created_at' => 1]);
    
    // Index pour compter les messages non lus
    $messages->createIndex(['conversation_id' => 1, 'read' => 1]);
    
    echo "✅ Index MongoDB créés avec succès\n";
}

// Si ce fichier est exécuté directement (pour créer les index)
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    createMongoIndexes();
}