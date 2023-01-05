<?php

function dbConnection()
{
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=weather_reports', 'leo', 'High9405');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // Si la connexion échoue, on renvoie un code d'erreur (500)
        http_response_code(500);
        exit;
    }
    return $pdo;
}

