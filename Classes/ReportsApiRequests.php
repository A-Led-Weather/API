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

        if (isset($reportValues['temperature']) && isset($reportValues['humidity'])) {
            // Récupération des données envoyées par l'esp8266
            $temperature = $reportValues['temperature'];
            $humidity = $reportValues['humidity'];

            // Préparation de la requête d'insertion
            $query = $this->pdo->prepare('INSERT INTO reports (temperature, humidity) VALUES (:temperature, :humidity)');

            // Liaison des paramètres
            $query->bindParam(':temperature', $temperature);
            $query->bindParam(':humidity', $humidity);

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
            HttpHandlerUtilities::setHTTPResponse(400, False);
        }

    }

    public function getReport(string $id): void
    {
            // Préparation de la requête de sélection
            $query = $this->pdo->prepare('SELECT * FROM reports WHERE (id = :id)');

            // Exécution de la requête
            try {
                $query->execute(['id' => $id]);
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
        $query = $this->pdo->prepare('SELECT * FROM reports LIMIT 10');

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
        $query = $this->pdo->prepare('SELECT * FROM reports');

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
            $query = $this->pdo->prepare('UPDATE reports SET temperature = :temperature, humidity = :humidity WHERE id = :id');

            try {

                $query->execute(['temperature' => $temperature, 'humidity' => $humidity, 'id' => $id]);

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
            $query = $this->pdo->prepare('DELETE FROM reports WHERE id = :id');

            try {

                $query->execute(['id' => $id]);
                // Si la requête s'est bien exécutée, on renvoie un code de succès (200)
                HttpHandlerUtilities::setHTTPResponse(200, True);

            } catch (PDOException $e) {
                // Si la requête échoue, on renvoie un code d'erreur (500)
                HttpHandlerUtilities::setHTTPResponse(500, False);
            }
    }

}


