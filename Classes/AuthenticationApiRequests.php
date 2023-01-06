<?php

class AuthenticationApiRequests
{
    private object $pdo;

    function __construct(object $pdo)
    {
        $this->pdo = $pdo;
    }
    public function addMachine(): void
    {

        $idValues = json_decode(file_get_contents("php://input"), true);

        if (isset($idValues['password'])) {

            if ($idValues['password'] == "High9405") {

                $uuid = uniqid();

                $tokenBytes = random_bytes(16);
                $token = bin2hex($tokenBytes);

                // Préparation de la requête d'insertion
                $query = $this->pdo->prepare('INSERT INTO machines (token, uuid) VALUES (:token, :uuid)');

                // Liaison des paramètres
                $query->bindParam(':token', $token);
                $query->bindParam(':uuid', $uuid);

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
                HttpHandlerUtilities::setHTTPResponse(401, 'Unauthorized');
            }
        } else {
            // Si les données sont incomplètes, on renvoie un code d'erreur (400)
            HttpHandlerUtilities::setHTTPResponse(400, False);
        }

    }

    public function compareId(): bool
    {
        $idValues = json_decode(file_get_contents("php://input"), true);

        if (isset($idValues['uuid']) && isset($idValues['token'])) {

            $id = $idValues['uuid'];
            $token = $idValues['token'];

            // Préparation de la requête de sélection
            $query = $this->pdo->prepare('SELECT * FROM machines WHERE (uuid = :uuid)');

            // Exécution de la requête
            try {
                $query->execute(['uuid' => $id]);
                // Récupération des résultats de la requête sous forme de tableau associatif
                $results = $query->fetchAll(PDO::FETCH_ASSOC);
                // Si la requête s'est bien exécutée, on renvoie un code de succès (200)
                if ($results['token'] == $token) {
                    HttpHandlerUtilities::setHTTPResponse(200, True);
                    return true;
                } else {
                    HttpHandlerUtilities::setHTTPResponse(401, 'Unauthorized');
                    return false;
                }
            } catch (PDOException $e) {
                // Si la requête échoue, on renvoie un code d'erreur (500)
                HttpHandlerUtilities::setHTTPResponse(500, False);
                return false;
            }
        } else {
            // Si les données sont incomplètes, on renvoie un code d'erreur (400)
            HttpHandlerUtilities::setHTTPResponse(400, False);
            return false;
        }
    }

    public function updateToken(): void
    {

        $_PUT = json_decode(file_get_contents("php://input"), true);

        if (isset($_PUT['uuid']) && isset($_PUT['token'])) {
            $uuid = intval($_PUT['uuid']);
            $token = $_PUT['token'];

            // Mise à jour du relevé de température et d'humidité dans la base de données
            $query = $this->pdo->prepare('UPDATE machines SET token = :token WHERE uuid = :uuid');

            try {

                $query->execute(['token' => $token]);

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

    public function deleteMachine(): void
    {

        $_DELETE = json_decode(file_get_contents("php://input"), true);

        if (isset($_DELETE['uuid'])) {

            $uuid = intval($_DELETE['uuid']);

            // Suppression du relevé de température et d'humidité de la base de données
            $query = $this->pdo->prepare('DELETE FROM machines WHERE uuid = :uuid');

            try {

                $query->execute(['uuid' => $uuid]);
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
}