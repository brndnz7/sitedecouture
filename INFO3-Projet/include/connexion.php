<?php

// Fichier de configuration avec les login et mots de passe
include('config.php');

function connexion() {
    // Connexion à la base de données et force l'affichage des erreurs SQL
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', 
        DB_LOGIN, 
        DB_PASSWORD
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

    return $pdo;
}
