<?php

namespace Controller;

use Exception;
use Medoo\Medoo;
use Model\UserModel;
use Utility\AccessControl;
use Utility\HttpHelper;

class UserController
{

    private Medoo $dbConnection;
    private UserModel $userModel;

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
        $this->userModel = new UserModel($this->dbConnection);
    }

    public function addUser(): void
    {
        $payload = json_decode(file_get_contents("php://input"), true);
        if (isset($payload['userName']) && isset($payload['userEmail']) && isset($payload['userPassword'])) {
            try {
                $this->userModel->addUser($payload);
                HttpHelper::setHttpResponse(200, "Success", true);
            } catch (Exception $e) {
                HttpHelper::setHttpResponse(500, "Server Error", true);
            }
        } else {
            HttpHelper::setHttpResponse(400, "Wrong Parameters", true);
        }
    }

    public function getUserByEmail($email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            HttpHelper::setHttpResponse(400, 'Invalid Email', true);
            exit();
        }

        try {
            $results = $this->userModel->getUserByEmail($email);
            if (empty($results)) {
                HttpHelper::setHttpResponse(400, "User Not Found", true);
                exit();
            }
            HttpHelper::setHttpResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            HttpHelper::setHttpResponse(500, "Server Error", true);
        }

    }

    public function updateUser($email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            HttpHelper::setHttpResponse(400, 'Invalid Email', true);
            exit();
        }

        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['userName']) && isset($payload['userEmail']) && isset($payload['userPassword'])) {
            try {
                $this->userModel->updateUser($email, $payload);
                HttpHelper::setHttpResponse(200, "Success", true);
            } catch (Exception $e) {
                HttpHelper::setHttpResponse(500, "Server Error", true);
            }
        } else {
            HttpHelper::setHttpResponse(400, "Wrong Parameters", true);
        }

    }

    public function deleteUser($email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            HttpHelper::setHttpResponse(400, 'Invalid Email', true);
            exit();
        }

        try {
            $this->userModel->deleteUser($email);
            HttpHelper::setHttpResponse(200, "Success", true);
        } catch (Exception $e) {
            HttpHelper::setHttpResponse(500, "Server error", true);
        }

    }

    public function authenticateUser(): void
    {
        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['userEmail']) && isset($payload['userPassword'])) {
            try {
                $results = $this->userModel->authenticateUser($payload);
                if (empty($results)) {
                    HttpHelper::setHttpResponse(404, 'User Not Found', true);
                    exit;
                }
                $userPassword = $payload['userPassword'];
                $hashedPassword = $results[0]['userPassword'];
                if (AccessControl::isValidPassword($userPassword, $hashedPassword)) {
                    HttpHelper::setHttpResponse(200, true, true);
                } else {
                    HttpHelper::setHttpResponse(200, false, true);
                }
            } catch (Exception $e) {
                HttpHelper::setHttpResponse(500, "Server Error", true);
            }
        } else {
            HttpHelper::setHttpResponse(400, "Wrong Parameters", true);
        }
    }
}