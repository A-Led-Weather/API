<?php

namespace model;

use Exception;
use Medoo\Medoo;
use utility\Middleware;

class ReportModel
{
    private Medoo $dbConnection;
    
    private const TABLE_NAME = 'REPORT';
    private const SERVER_TIME_ADJUST = "+1 hour";

    public function __construct(Medoo $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function getReportById(string $id): void
    {
        try {
            $results = $this->dbConnection->select(self::TABLE_NAME, "*", ["reportId" => $id]);
            if (empty($results)) {
                Middleware::setHTTPResponse(404, "Report Not Found", true);
            }
            Middleware::setHTTPResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            Middleware::setHTTPResponse(500, "Server Error", true);
        }
    }

    public function getLastReportByLocation(string $location): void
    {
        try {
            $results = $this->dbConnection->select(self::TABLE_NAME, "*", ["locationName" => ucfirst($location), "ORDER" => ["reportId" => "DESC"], "LIMIT" => 1]);
            if (empty($results)) {
                Middleware::setHTTPResponse(404, "Location Not Found", true);
            }
            Middleware::setHTTPResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            Middleware::setHTTPResponse(500, "Server Error", true);
        }
    }

    public function getReportsByLocationByTimeRange(string $location, string $timeRange): void
    {
        try {
            if ($timeRange === 'hourly') {
                $limit = 60;
            } elseif ($timeRange === 'daily') {
                $limit = 1440;
            } else {
                Middleware::setHTTPResponse(400, 'Wrong Time Range', true);
                exit();
            }
            $results = $this->dbConnection->select(self::TABLE_NAME, "*", ["locationName" => ucfirst($location), "ORDER" => ["reportId" => "DESC"], "LIMIT" => $limit]);
            if (empty($results)) {
                Middleware::setHTTPResponse(404, "Location Not Found", true);
                exit();
            }
            Middleware::setHTTPResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            Middleware::setHTTPResponse(500, "Server Error", true);
        }
    }

    public function getLastsReports(): void
    {
        try {
            $results = $this->dbConnection->select(self::TABLE_NAME, "*",["ORDER" => ["reportId" => "DESC"], "LIMIT" => 10]);
            if (empty($results)) {
                Middleware::setHTTPResponse(404, 'No Data', true);
                exit();
            }
            Middleware::setHTTPResponse(200, "Success", false);
            echo json_encode($results);
        } catch (Exception $e) {
            Middleware::setHTTPResponse(500, "Server Error", true);
        }
    }

    public function addReport(): void
    {
        $timeAdjust = strtotime(self::SERVER_TIME_ADJUST);

        try {
            $payload = json_decode(file_get_contents("php://input"), true);
            if (isset($payload['temperature']) && isset($payload['humidity']) && isset($payload['deviceUuid']) && isset($payload['locationName'])) {
                $this->dbConnection->insert(self::TABLE_NAME, [
                    "temperature" => $payload['temperature'],
                    "humidity" => $payload['humidity'],
                    "dateTime" => date("Y-m-d H:i:s", $timeAdjust),
                    "deviceUuid" => $payload['deviceUuid'],
                    "locationName" => $payload['locationName']
                ]);
                Middleware::setHTTPResponse(200, "Success", true);
            } else {
                Middleware::setHTTPResponse(400, "Missing Parameters", true);
            }
        } catch (Exception $e) {
            Middleware::setHTTPResponse(500, "Server Error", true);
        }
    }

    public function deleteReport(string $id): void
    {
        try {
            $this->dbConnection->delete(self::TABLE_NAME, [
                "reportId" => $id
            ]);
            Middleware::setHTTPResponse(200, "Success", true);
        } catch (Exception $e) {
            Middleware::setHTTPResponse(500, "Server Error", true);
        }
    }

}

