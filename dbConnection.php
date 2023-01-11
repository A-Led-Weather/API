<?php
function dbConnection($dbDriver, $dbHost, $dbName, $dbUser, $dbPassword)
{
    try {
        $pdo = new PDO($dbDriver. ':host=' . $dbHost . ';dbname=' . $dbName, $dbUser, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        http_response_code(500);
        exit;
    }
    return $pdo;
}

