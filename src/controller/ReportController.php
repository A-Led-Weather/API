<?php

namespace Controller;

use Exception;
use Medoo\Medoo;
use Model\ReportModel;
use Model\UserModel;
use Utility\AuthHelper;
use Utility\HttpHelper;


class ReportController
{

    private const HOUR_LIMIT = 60;
    private const DAY_LIMIT = 1440;
    private Medoo $dbConnection;
    private ReportModel $reportModel;
    private UserModel $userModel;
    private string $jwtKey;

    private array $headers;

    private string|false $jwt;

    public function __construct(Medoo $dbConnection, string $jwtKey, array $headers)
    {
        $this->dbConnection = $dbConnection;
        $this->reportModel = new ReportModel($this->dbConnection);
        $this->userModel = new UserModel($this->dbConnection);
        $this->jwtKey = $jwtKey;
        $this->headers = $headers;
        $this->jwt = HttpHelper::getAuthHeaderValue($this->headers);
    }

    public function getLastReports(): void
    {
        $this->authenticateRequest();

        try {
            $results = $this->reportModel->getLastsReports();
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

        if (empty($this->userModel->getEmailFromToken($this->jwt))) {
            HttpHelper::setResponse(403, "Token Doesn't match any profile", true);
            exit;
        }
    }

    public function addReport(): void
    {
        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['temperature']) && isset($payload['humidity']) && isset($payload['deviceUuid']) && isset($payload['locationName'])) {
            try {
                $this->reportModel->addReport($payload);
                HttpHelper::setResponse(200, "Report Created", true);
            } catch (Exception $e) {
                HttpHelper::setResponse(500, "Server Error", true);
            }
        } else {
            HttpHelper::setResponse(400, "Invalid or Missing Parameters", true);
        }
    }

    public function getReportById($id): void
    {
        $this->authenticateRequest();

        if (!is_numeric($id)) {
            HttpHelper::setResponse(400, 'Invalid Path Value', true);
            exit;
        }

        try {
            $results = $this->reportModel->getReportById($id);
            if (empty($results)) {
                HttpHelper::setResponse(404, "Report Not Found", true);
                exit;
            }
            HttpHelper::setResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            HttpHelper::setResponse(500, "Server Error", true);
        }
    }

    public function deleteReport($id): void
    {
        $this->authenticateRequest();

        if (!is_numeric($id)) {
            HttpHelper::setResponse(400, 'Invalid Path Values', true);
            exit;
        }
        try {
            $this->reportModel->deleteReport($id);
            HttpHelper::setResponse(200, "Report Deleted", true);
            exit;
        } catch (Exception $e) {
            HttpHelper::setResponse(500, "Server Error", true);
            exit;
        }
    }

    public function getLastReportByLocation($location): void
    {
        $this->authenticateRequest();

        try {
            $results = $this->reportModel->getLastReportByLocation($location);
            if (empty($results)) {
                HttpHelper::setResponse(404, "Location Not Found", true);
            }
            HttpHelper::setResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            HttpHelper::setResponse(500, "Server Error", true);
        }

    }

    public function getReportsByLocationByTimeRange($location, $timeRange): void
    {
        $this->authenticateRequest();

        if ($timeRange === 'hourly') {
            $limit = self::HOUR_LIMIT;
        } elseif ($timeRange === 'daily') {
            $limit = self::DAY_LIMIT;
        } else {
            HttpHelper::setResponse(400, 'Wrong Period', true);
            exit;
        }

        try {
            $results = $this->reportModel->getAverageReportByLocationByTimeRange($location, $limit);
            if (empty($results)) {
                HttpHelper::setResponse(404, "Location Not Found", true);
                exit;
            }
            HttpHelper::setResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            HttpHelper::setResponse(500, "Server Error", true);
        }

    }

}