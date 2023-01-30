<?php

namespace Controller;

use Exception;
use Medoo\Medoo;
use Model\LocationModel;
use Utility\AuthHelper;
use Utility\HttpHelper;

class LocationController
{
    private array $headers;

    private string|false $jwt;
    private Medoo $dbConnection;
    private string $jwtKey;
    private locationModel $locationModel;

    public function __construct(Medoo $dbConnection, string $jwtKey, array $headers)
    {
        $this->dbConnection = $dbConnection;
        $this->locationModel = new LocationModel($this->dbConnection);
        $this->jwtKey = $jwtKey;
        $this->headers = $headers;
        $this->jwt = HttpHelper::getAuthHeaderValue($this->headers);
    }

    public function getLocationByName(string $location): void
    {
        $this->authenticateRequest();
        try {
            $results = $this->locationModel->getLocationByName($location);
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

    public function getLocations(): void
    {
        $this->authenticateRequest();
        try {
            $results = $this->locationModel->getLocations();
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


    private function authenticateRequest(): void
    {
        try {
            AuthHelper::authenticateRequestToken($this->jwtKey, $this->jwt);
        } catch (Exception $e) {
            HttpHelper::setResponse(403, "Missing or Invalid Token", true);
            exit;
        }
    }

    public function addLocation(): void
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        $this->authenticateRequest();
        if (isset($payload['locationName']) && isset ($payload['latitude']) && isset($payload['longitude'])) {
            try {
                $this->locationModel->addLocation($payload);
                HttpHelper::setResponse(200, "Location Created", true);
            } catch (Exception $e) {
                HttpHelper::setResponse(500, "Server Error", true);
            }
        } else {
            HttpHelper::setResponse(400, "Missing Data", true);
        }
    }
    public function updateLocation(string $location): void
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        $this->authenticateRequest();
        if (isset($payload['locationName']) && isset ($payload['latitude']) && isset($payload['longitude'])) {
            try {
                $this->locationModel->updateLocation($payload, $location);
                HttpHelper::setResponse(200, "Location Updated", true);
            } catch (Exception $e) {
                HttpHelper::setResponse(500, "Server Error", true);
            }
        } else {
            HttpHelper::setResponse(400, "Missing Data", true);
        }
    }

    public function deleteLocation(string $location): void
    {
        $this->authenticateRequest();
        try {
            $this->locationModel->deleteLocation($location);
            HttpHelper::setResponse(200, "Success", true);
        } catch (Exception $e) {
            HttpHelper::setResponse(500, "Server Error", true);
        }
    }
}