<?php

namespace Controller;

use Exception;
use Medoo\Medoo;
use Model\DeviceModel;
use Model\UserModel;
use Utility\AuthHelper;
use Utility\HttpHelper;

class DeviceController
{
    private Medoo $dbConnection;
    private DeviceModel $deviceModel;
    private string $jwtKey;

    private array $headers;

    private string|false $jwt;
    private UserModel $userModel;

    public function __construct(Medoo $dbConnection, string $jwtKey, array $headers)
    {
        $this->dbConnection = $dbConnection;
        $this->deviceModel = new DeviceModel($this->dbConnection);
        $this->userModel = new UserModel($this->dbConnection);
        $this->jwtKey = $jwtKey;
        $this->headers = $headers;
        $this->jwt = HttpHelper::getAuthHeaderValue($this->headers);
    }

    private function authenticateRequest(): void
    {
        try {
            AuthHelper::authenticateRequestToken($this->jwtKey, $this->jwt);
        } catch (Exception $e) {
            HttpHelper::setResponse(403, "Missing or Invalid Token", true);
            exit;
        }

        if (empty($this->userModel->getEmailFromToken($this->jwt))) {
            HttpHelper::setResponse(403, "Token Doesn't match any profile", true);
            exit;
        }
    }

    public function getDevices(): void
    {
        $this->authenticateRequest();

        try {
            $results = $this->deviceModel->getDevices();
            if (empty($results)) {
                HttpHelper::setResponse(404, 'No Matching Data', true);
                exit;
            }
            HttpHelper::setResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            HttpHelper::setResponse(500, "Server Error", true);
        }
    }

    public function getDeviceById(int $id): void
    {
        $this->authenticateRequest();

        try {
            $results = $this->deviceModel->getDeviceById($id);
            if (empty($results)) {
                HttpHelper::setResponse(404, 'No Matching Data', true);
                exit;
            }
            HttpHelper::setResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            HttpHelper::setResponse(500, "Server Error", true);
        }
    }

    public function addDevice(): void
    {
        $this->authenticateRequest();

        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['model']) && isset($payload['locationName'])) {
            try {
                $this->deviceModel->addDevice($payload);
                HttpHelper::setResponse(201, "Device Created", true);
            } catch (Exception $e) {
                HttpHelper::setResponse(500, "Server Error", true);
            }
        } else {
            HttpHelper::setResponse(400, "Invalid or Missing Parameters", true);
        }
    }

    public function updateDevice(int $id): void
    {
        $this->authenticateRequest();

        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['model']) && isset($payload['locationName'])) {
            try {
                $this->deviceModel->updateDevice($id, $payload);
                HttpHelper::setResponse(201, "Device Updated", true);
            } catch (Exception $e) {
                HttpHelper::setResponse(500, "Server Error", true);
            }
        } else {
            HttpHelper::setResponse(400, "Invalid or Missing Parameters", true);
        }
    }

    public function deleteDevice(int $id): void
    {
        $this->authenticateRequest();

        try {
            $this->deviceModel->deleteDevice($id);
            HttpHelper::setResponse(200, "Device Deleted", true);
        } catch (Exception $e) {
            HttpHelper::setResponse(500, "Server Error", true);
        }
    }
}