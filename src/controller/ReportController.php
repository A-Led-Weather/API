<?php

namespace Controller;

use Exception;
use Model\ReportModel;
use Utility\HttpHelper;
use Medoo\Medoo;

class ReportController
{

    private Medoo $dbConnection;
    private ReportModel $reportModel;
    private const HOUR_LIMIT = 60;
    private const DAY_LIMIT = 1440;


    public function __construct(Medoo $dbConnection)
    {
        $this->dbConnection = $dbConnection;
        $this->reportModel = new ReportModel($this->dbConnection);
    }


    public function getLastReports(): void
    {
        try {
            $results = $this->reportModel->getLastsReports();
            if (empty($results)) {
                HttpHelper::setHttpResponse(404, 'No Data', true);
                exit;
            }
            HttpHelper::setHttpResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            HttpHelper::setHttpResponse(500, "Server Error", true);
        }
    }

    public function addReport(): void
    {
        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['temperature']) && isset($payload['humidity']) && isset($payload['deviceUuid']) && isset($payload['locationName'])) {
            try {
                $this->reportModel->addReport($payload);
                HttpHelper::setHttpResponse(200, "Success", true);
            } catch (Exception $e) {
                HttpHelper::setHttpResponse(500, "Server Error", true);
            }
        } else {
            HttpHelper::setHttpResponse(400, "Missing Parameters", true);
        }
    }

    public function getReportById($id): void
    {
        if (!is_numeric($id)) {
            HttpHelper::setHttpResponse(400, 'Invalid Values', true);
            exit;
        }

        try {
            $results = $this->reportModel->getReportById($id);
            if (empty($results)) {
                HttpHelper::setHttpResponse(404, "Report Not Found", true);
                exit;
            }
            HttpHelper::setHttpResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            HttpHelper::setHttpResponse(500, "Server Error", true);
        }
    }

    public function deleteReport($id): void
    {
        if (!is_numeric($id)) {
            HttpHelper::setHttpResponse(400, 'Invalid Values', true);
            exit;
        }
        try {
            $this->reportModel->deleteReport($id);
            HttpHelper::setHttpResponse(200, "Success", true);
            exit;
        } catch (Exception $e) {
            HttpHelper::setHttpResponse(500, "Server Error", true);
            exit;
        }
    }

    public function getLastReportByLocation($location): void
    {
        try {
            $results = $this->reportModel->getLastReportByLocation($location);
            if (empty($results)) {
                HttpHelper::setHttpResponse(404, "Location Not Found", true);
            }
            HttpHelper::setHttpResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            HttpHelper::setHttpResponse(500, "Server Error", true);
        }

    }

    public function getReportsByLocationByTimeRange($location, $timeRange): void
    {
        if ($timeRange === 'hourly') {
            $limit = self::HOUR_LIMIT;
        } elseif ($timeRange === 'daily') {
            $limit = self::DAY_LIMIT;
        } else {
            HttpHelper::setHttpResponse(400, 'Wrong Time Range', true);
            exit;
        }

        try {
            $results = $this->reportModel->getReportsByLocationByTimeRange($location, $limit);
            if (empty($results)) {
                HttpHelper::setHttpResponse(404, "Location Not Found", true);
                exit;
            }
            HttpHelper::setHttpResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            HttpHelper::setHttpResponse(500, "Server Error", true);
        }

    }

}