<?php
require_once 'config/mongodb.php';

echo "<h2>Test de connexion MongoDB</h2>";

try {
    // Test 1 : Connexion
    $db = getMongoConnection();
    echo "✅ Connexion MongoDB réussie !<br><br>";
    
    // Test 2 : Insertion simple
    $test = $db->test_collection;
    $result = $test->insertOne([
        'message' => 'Hello MongoDB', 
        'timestamp' => time()
    ]);
    echo "✅ Document inséré avec ID : " . $result->getInsertedId() . "<br><br>";
    
    // Test 3 : Lecture
    $document = $test->findOne(['message' => 'Hello MongoDB']);
    echo "✅ Document récupéré : " . $document['message'] . "<br><br>";
    
    // Test 4 : Création des index
    echo "<h3>Création des index...</h3>";
    createMongoIndexes();
    echo "<br>✅ Index créés !<br><br>";
    
    // Test 5 : Nettoyage
    $test->drop();
    echo "✅ Collection de test supprimée<br>";
    
    echo "<br><strong style='color: green;'>🎉 Tous les tests passés ! MongoDB fonctionne parfaitement.</strong>";
    
} catch (Exception $e) {
    echo "<strong style='color: red;'>❌ Erreur : " . $e->getMessage() . "</strong>";
    echo "<br><br>Détails : <pre>" . $e->getTraceAsString() . "</pre>";
}
?>