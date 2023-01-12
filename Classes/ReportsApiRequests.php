<?php

Class ReportsApiRequests {

    private object $pdo;

    function __construct(object $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addReport(): void
    {

        $reportValues = json_decode(file_get_contents("php://input"), true);

        if (isset($reportValues['temperature']) && isset($reportValues['humidity']) && isset($reportValues['deviceUuid']) && isset($reportValues['locationName'])) {
            // Récupération des données envoyées par l'esp8266
            $temperature = $reportValues['temperature'];
            $humidity = $reportValues['humidity'];
            $deviceUuid = $reportValues['deviceUuid'];
            $locationName = $reportValues['locationName'];

            // Préparation de la requête d'insertion
            $query = $this->pdo->prepare('INSERT INTO REPORT (temperature, humidity, dateTime, deviceUuid, locationName) VALUES (:temperature, :humidity, NOW(), :deviceUuid, :locationName)');

            // Liaison des paramètres
            $query->bindParam(':temperature', $temperature);
            $query->bindParam(':humidity', $humidity);
            $query->bindParam(':deviceUuid', $deviceUuid);
            $query->bindParam(':locationName', $locationName);

            // Exécution de la requête
            try {
                $query->execute();
                // Si la requête s'est bien exécutée, on renvoie un code de succès (200)
                HttpHandlerUtilities::setHTTPResponse(200, True);
            } catch (PDOException $e) {
                // Si la requête échoue, on renvoie un code d'erreur (500)
                HttpHandlerUtilities::setHTTPResponse(500, False);
            }
        } else {
            // Si les données sont incomplètes, on renvoie un code d'erreur (400)
            HttpHandlerUtilities::setHTTPResponse(401, False);
        }

    }

    public function getReport(string $id): void
    {
            // Préparation de la requête de sélection
            $query = $this->pdo->prepare('SELECT * FROM REPORT WHERE (reportId = :reportId)');

            // Exécution de la requête
            try {
                $query->execute(['reportId' => $id]);
                // Récupération des résultats de la requête sous forme de tableau associatif
                $results = $query->fetchAll(PDO::FETCH_ASSOC);
                // Si la requête s'est bien exécutée, on renvoie un code de succès (200)
                http_response_code(200);
                // Encodage des résultats en JSON et renvoi de la réponse
                echo json_encode($results);
            } catch (PDOException $e) {
                // Si la requête échoue, on renvoie un code d'erreur (500)
                HttpHandlerUtilities::setHTTPResponse(500, False);
            }
    }

    public function getLastsReports(): void
    {

        // Préparation de la requête de sélection
        $query = $this->pdo->prepare('SELECT * FROM REPORT ORDER BY reportId DESC LIMIT 10;');

        // Exécution de la requête
        try {
            $query->execute();
            // Récupération des résultats de la requête sous forme de tableau associatif
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            // Si la requête s'est bien exécutée, on renvoie un code de succès (200)
            http_response_code(200);
            // Encodage des résultats en JSON et renvoi de la réponse
            echo json_encode($results);
        } catch (PDOException $e) {
            // Si la requête échoue, on renvoie un code d'erreur (500)
            HttpHandlerUtilities::setHTTPResponse(500, False);
        }
    }

    public function getAllReports(): void
    {

        // Préparation de la requête de sélection
        $query = $this->pdo->prepare('SELECT * FROM REPORT');

        // Exécution de la requête
        try {
            $query->execute();
            // Récupération des résultats de la requête sous forme de tableau associatif
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            // Si la requête s'est bien exécutée, on renvoie un code de succès (200)
            http_response_code(200);
            // Encodage des résultats en JSON et renvoi de la réponse
            echo json_encode($results);
        } catch (PDOException $e) {
            // Si la requête échoue, on renvoie un code d'erreur (500)
            HttpHandlerUtilities::setHTTPResponse(500, False);
        }
    }

    public function updateReport(string $id): void
    {

        $_PUT = json_decode(file_get_contents("php://input"), true);

        if (isset($_PUT['temperature']) && isset($_PUT['humidity']) && isset($_PUT['id'])) {
            $temperature = $_PUT['temperature'];
            $humidity = $_PUT['humidity'];

            // Mise à jour du relevé de température et d'humidité dans la base de données
            $query = $this->pdo->prepare('UPDATE REPORT SET temperature = :temperature, humidity = :humidity WHERE reportId = :reportId');

            try {

                $query->execute(['temperature' => $temperature, 'humidity' => $humidity, 'reportId' => $id]);

                // Si la requête s'est bien exécutée, on renvoie un code de succès (200)
                HttpHandlerUtilities::setHTTPResponse(200, True);

            } catch (PDOException $e) {
                // Si la requête échoue, on renvoie un code d'erreur (500)
                HttpHandlerUtilities::setHTTPResponse(500, False);
            }

        } else {
            // Si les données sont incomplètes, on renvoie un code d'erreur (400)
            HttpHandlerUtilities::setHTTPResponse(400, False);
        }

    }

    public function deleteReport(string $id): void
    {

            // Suppression du relevé de température et d'humidité de la base de données
            $query = $this->pdo->prepare('DELETE FROM REPORT WHERE reportId= :reportId');

            try {

                $query->execute(['reportId' => $id]);
                // Si la requête s'est bien exécutée, on renvoie un code de succès (200)
                HttpHandlerUtilities::setHTTPResponse(200, True);

            } catch (PDOException $e) {
                // Si la requête échoue, on renvoie un code d'erreur (500)
                HttpHandlerUtilities::setHTTPResponse(500, False);
            }
    }

}


