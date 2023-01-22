<?php

namespace Controller;

use Exception;
use Medoo\Medoo;
use Model\UserModel;
use Utility\AuthHelper;
use Utility\HttpHelper;


class UserController
{

    private Medoo $dbConnection;
    private UserModel $userModel;
    private string $jwtKey;

    public function __construct($dbConnection, $jwtKey)
    {
        $this->dbConnection = $dbConnection;
        $this->userModel = new UserModel($this->dbConnection);
        $this->jwtKey = $jwtKey;
    }

    public function addUser(): void
    {
        $payload = json_decode(file_get_contents("php://input"), true);
        if (isset($payload['userName']) && isset($payload['userEmail']) && isset($payload['userPassword'])) {
            try {
                $this->userModel->addUser($payload);
                HttpHelper::setResponse(200, "Success", true);
            } catch (Exception $e) {
                HttpHelper::setResponse(500, "Server Error", true);
            }
        } else {
            HttpHelper::setResponse(400, "Wrong Parameters", true);
        }
    }

    public function getUserByEmail($email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            HttpHelper::setResponse(400, 'Invalid Email', true);
            exit();
        }

        try {
            $results = $this->userModel->getUserByEmail($email);
            if (empty($results)) {
                HttpHelper::setResponse(400, "User Not Found", true);
                exit();
            }
            HttpHelper::setResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            HttpHelper::setResponse(500, "Server Error", true);
        }

    }

    public function updateUser($email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            HttpHelper::setResponse(400, 'Invalid Email', true);
            exit();
        }

        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['userName']) && isset($payload['userEmail']) && isset($payload['userPassword'])) {
            try {
                $this->userModel->updateUser($email, $payload);
                HttpHelper::setResponse(200, "Success", true);
            } catch (Exception $e) {
                HttpHelper::setResponse(500, "Server Error", true);
            }
        } else {
            HttpHelper::setResponse(400, "Wrong Parameters", true);
        }

    }

    public function deleteUser($email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            HttpHelper::setResponse(400, 'Invalid Email', true);
            exit();
        }

        try {
            $this->userModel->deleteUser($email);
            HttpHelper::setResponse(200, "Success", true);
        } catch (Exception $e) {
            HttpHelper::setResponse(500, "Server error", true);
        }

    }

    public function authenticateUser(bool $isForJwtCreation = false)
    {
        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['userEmail']) && isset($payload['userPassword'])) {
            try {
                $results = $this->userModel->getUserByEmail($payload['userEmail']);
                if (empty($results)) {
                    HttpHelper::setResponse(404, 'User Not Found', true);
                    exit;
                }
                $userPassword = $payload['userPassword'];
                $hashedPassword = $results[0]['userPassword'];
                if (AuthHelper::isValidPassword($userPassword, $hashedPassword)) {
                    if (!$isForJwtCreation) HttpHelper::setResponse(200, 'Authenticated', true);
                    return true;
                } else {
                    HttpHelper::setResponse(401, 'Wrong Credentials', true);
                    return false;
                }
            } catch (Exception $e) {
                HttpHelper::setResponse(500, "Server Error", true);
            }
        } else {
            HttpHelper::setResponse(400, "Wrong Parameters", true);
        }
    }

    public function createJWT(): void
    {

        $payload = json_decode(file_get_contents("php://input"), true);

            if ($this->authenticateUser(true)) {
                $jwt = AuthHelper::createJWT($payload['userEmail'], $this->jwtKey);
                if (empty($jwt)) {
                    HttpHelper::setResponse(500, 'Server Error', true);
                    exit;
                }
                try {
                    $this->userModel->addJWT($jwt, $payload);
                    HttpHelper::setResponse(200, 'Token Created', false);
                    echo json_encode(['token' => $jwt]);

                } catch (Exception $e) {
                    HttpHelper::setResponse(500, "Server Error", true);
                }
            } else {
                exit;
            }
    }


}