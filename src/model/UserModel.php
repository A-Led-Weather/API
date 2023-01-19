<?php

namespace model;

use PDO;
use PDOException;
use utility\Middleware;

class UserModel
{
    private object $pdo;

    public function __construct(object $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getUserByEmail(string $email): void
    {
        $query = $this->pdo->prepare('SELECT * FROM USER WHERE (userEmail = :userEmail)');

        try {
            $query->execute(['userEmail' => $email]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            if (empty($results)) {
                Middleware::setHTTPResponse(404, "User not found", true);
                exit();
            }
            Middleware::setHTTPResponse(200, "Success", false);
            echo json_encode($results);
        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error", true);
        }
    }

    public function addUser(): void
    {

        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['userName']) && isset($payload['userEmail']) && isset($payload['userPassword'])) {
            $userName = $payload['userName'];
            $userEmail = $payload['userEmail'];
            $userPassword = $payload['userPassword'];
            $hashedPassword = Middleware::hashPassword($userPassword);

            $query = $this->pdo->prepare('INSERT INTO USER (userName, userEmail, userPassword) VALUES (:userName, :userEmail, :userPassword)');

            $query->bindParam(':userName', $userName);
            $query->bindParam(':userEmail', $userEmail);
            $query->bindParam(':userPassword', $hashedPassword);

            try {
                $query->execute();
                Middleware::setHTTPResponse(200, "Success", true);
            } catch (PDOException $e) {
                Middleware::setHTTPResponse(500, "Server error", true);
                echo $e->getMessage();
            }
        } else {
            Middleware::setHTTPResponse(400, "Wrong parameters", true);
        }

    }

    public function authenticateUser(): void
    {

        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['userEmail']) && isset($payload['userPassword'])) {

            $userEmail = $payload['userEmail'];
            $userPassword = $payload['userPassword'];

            $query = $this->pdo->prepare('SELECT * FROM USER WHERE (userEmail = :userEmail)');

            try {
                $query->execute(['userEmail' => $userEmail]);
                $results = $query->fetchAll(PDO::FETCH_ASSOC);
                if (empty($results)) {
                    Middleware::setHTTPResponse(404, "User not found", true);
                    exit();
                }

            } catch (PDOException $e) {
                Middleware::setHTTPResponse(500, "Server error", true);
                exit();
            }
        } else {
            Middleware::setHTTPResponse(400, "Wrong parameters", true);
            exit();
        }

        $hashedPassword = $results[0]['userPassword'];

        if (Middleware::isValidPassword($userPassword, $hashedPassword)) {
            Middleware::setHTTPResponse(200, true, true);
        } else {
            Middleware::setHTTPResponse(200, false, true);
        }

    }

    public function updateUser(string $email): void
    {

        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['userName']) && isset($payload['userPassword'])) {
            $userName = $payload['userName'];
            $userPassword = $payload['userPassword'];
            $hashedPassword = Middleware::hashPassword($userPassword);

            $query = $this->pdo->prepare('UPDATE USER SET userName = :userName, userPassword = :userPassword WHERE userEmail = :userEmail');

            try {
                $query->execute(['userName' => $userName, 'userPassword' => $hashedPassword, 'userEmail' => $email]);
                Middleware::setHTTPResponse(200, "Success", true);
            } catch (PDOException $e) {
                Middleware::setHTTPResponse(500, "Server error", true);
            }
        } else {
            Middleware::setHTTPResponse(400, "Wrong parameters", true);
        }

    }

    public function deleteUser(string $email): void
    {

        $query = $this->pdo->prepare('DELETE FROM USER WHERE userEmail = :userEmail');

        try {

            $query->execute(['userEmail' => $email]);
            Middleware::setHTTPResponse(200, "Success", true);

        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error", true);
        }
    }

}