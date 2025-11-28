<?php
// Configuration pour InfinityFree
// REMPLACEZ par VOS valeurs notées à l'étape 3
$host = 'sql109.infinityfree.com';  // Votre MySQL Hostname
$user = 'if0_40468647';       // Votre Database Username
$password = 'Jaommdp63ejlrm'; // Votre Database Password
$database = 'if0_40468647_ecoride'; // Votre Database Name
$port = '3306';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    echo "✅ Connexion OK !<br>";
} catch (PDOException $e) {
    die("Erreur de connexion BDD : " . $e->getMessage());
}