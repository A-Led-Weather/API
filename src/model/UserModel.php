<?php

namespace model;

use Medoo\Medoo;
use Exception;
use utility\Middleware;

class UserModel
{
    private Medoo $dbConnection;

    private const TABLE_NAME = 'USER';

    public function __construct(Medoo $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function getUserByEmail(string $email): void
    {
        echo $email.PHP_EOL;
        try {
            $results = $this->dbConnection->select(self::TABLE_NAME, "*", ["userEmail" => $email]);
            if (empty($results)) {
                Middleware::setHTTPResponse(400, "User Not Found", true);
                exit();
            }
            Middleware::setHTTPResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            Middleware::setHTTPResponse(500, "Server Error", true);
        }
    }

    public function addUser(): void
    {
        try {
            $payload = json_decode(file_get_contents("php://input"), true);
            if (isset($payload['userName']) && isset($payload['userEmail']) && isset($payload['userPassword'])) {
                $userName = $payload['userName'];
                $userEmail = $payload['userEmail'];
                $userPassword = $payload['userPassword'];
                $hashedPassword = Middleware::hashPassword($userPassword);

                $this->dbConnection->insert(self::TABLE_NAME, [
                    "userName" => $userName,
                    "userEmail" => $userEmail,
                    "userPassword" => $hashedPassword
                ]);
                Middleware::setHTTPResponse(200, "Success", true);
            } else {
                Middleware::setHTTPResponse(400, "Wrong Parameters", true);
            }
        } catch (Exception $e) {
            Middleware::setHTTPResponse(500, "Server Error", true);
        }
    }

    public function authenticateUser(): void
    {
        try {
            $payload = json_decode(file_get_contents("php://input"), true);
            if (isset($payload['userEmail']) && isset($payload['userPassword'])) {
                $userEmail = $payload['userEmail'];
                $userPassword = $payload['userPassword'];

                $results = $this->dbConnection->select(self::TABLE_NAME, "*", ["userEmail" => $userEmail]);
                if (empty($results)) {
                    throw new Exception("User not found", 404);
                }
                $hashedPassword = $results[0]['userPassword'];
                if (Middleware::isValidPassword($userPassword, $hashedPassword)) {
                    Middleware::setHTTPResponse(200, true, true);
                } else {
                    Middleware::setHTTPResponse(200, false, true);
                }
            } else {
                Middleware::setHTTPResponse(400, "Wrong Parameters", true);
            }
        } catch (Exception $e) {
            Middleware::setHTTPResponse(500, "Server Error", true);
        }
    }

    public function updateUser(string $email): void
    {
        try {
            $payload = json_decode(file_get_contents("php://input"), true);
            if (isset($payload['userName']) && isset($payload['userEmail']) && isset($payload['userPassword'])) {
                $userName = $payload['userName'];
                $userEmail = $payload['userEmail'];
                $userPassword = $payload['userPassword'];
                $hashedPassword = Middleware::hashPassword($userPassword);

                $this->dbConnection->update(self::TABLE_NAME, [
                    "userName" => $userName,
                    "userEmail" => $userEmail,
                    "userPassword" => $hashedPassword
                ], ["userEmail" => $email]);
                Middleware::setHTTPResponse(200, "Success", true);
            } else {
                Middleware::setHTTPResponse(400, "Wrong Parameters", true);
            }
        } catch (Exception $e) {
            Middleware::setHTTPResponse(500, "Server Error", true);
        }
    }

    public function deleteUser(string $email): void
    {
        try {
            $this->dbConnection->delete(self::TABLE_NAME, ["userEmail" => $email]);
            Middleware::setHTTPResponse(200, "Success", true);
        } catch (Exception $e) {
            Middleware::setHTTPResponse(500, "Server error", true);
        }
    }
}


