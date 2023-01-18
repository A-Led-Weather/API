<?php

namespace model;
use PDO;
use PDOException;
use utility\Middleware;

class ReportModel
{

    private object $pdo;

    public function __construct(object $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getReportById(string $id): void
    {
        $query = $this->pdo->prepare('SELECT * FROM REPORT WHERE (reportId = :reportId)');

        try {
            $query->execute(['reportId' => $id]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            Middleware::setHTTPResponse(200, "Success",false);
            echo json_encode($results);
        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error", "HTTP/1.0 500 Internal Error", true);
        }
    }

    public function getLastReportByLocation(string $location): void
    {

        $query = $this->pdo->prepare('SELECT * FROM REPORT WHERE locationName = :locationName ORDER BY reportId DESC LIMIT 1;');

        try {
            $query->execute(['locationName' => ucfirst($location)]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            if (empty($results)) {
                Middleware::setHTTPResponse(404, "Location not found",true);
                exit();
            }
            Middleware::setHTTPResponse(200, "Success",false);
            echo json_encode($results);

        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error",true);
        }

    }

    public function getLastHourReportsByLocation(string $location): void
    {

        $query = $this->pdo->prepare('SELECT * FROM REPORT WHERE locationName = :locationName ORDER BY reportId DESC LIMIT 60;');

        try {
            $query->execute(['locationName' => ucfirst($location)]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            if (empty($results)) {
                Middleware::setHTTPResponse(404, "Location not found",true);
                exit();
            }
            Middleware::setHTTPResponse(200, "Success",false);
            echo json_encode($results);

        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error", true);
        }


    }

    public function getLastDayReportsByLocation(string $location): void
    {

        $query = $this->pdo->prepare('SELECT * FROM REPORT WHERE locationName = :locationName ORDER BY reportId DESC LIMIT 1440;');

        try {
            $query->execute(['locationName' => ucfirst($location)]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            if (empty($results)) {
                Middleware::setHTTPResponse(404, "Location not found",true);
                exit();
            }
            Middleware::setHTTPResponse(200, "Success",false);
            echo json_encode($results);

        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error", true);
        }


    }

    public function getLastsReports(): void
    {

        $query = $this->pdo->prepare('SELECT * FROM REPORT ORDER BY reportId DESC LIMIT 10;');

        try {
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            Middleware::setHTTPResponse(200, "Success",false);
            echo json_encode($results);
        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error",true);
        }
    }

    public function addReport(): void
    {

        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['temperature']) && isset($payload['humidity']) && isset($payload['deviceUuid']) && isset($payload['locationName'])) {
            $temperature = $payload['temperature'];
            $humidity = $payload['humidity'];
            $deviceUuid = $payload['deviceUuid'];
            $locationName = $payload['locationName'];

            $query = $this->pdo->prepare('INSERT INTO REPORT (temperature, humidity, dateTime, deviceUuid, locationName) VALUES (:temperature, :humidity, NOW(), :deviceUuid, :locationName)');

            $query->bindParam(':temperature', $temperature);
            $query->bindParam(':humidity', $humidity);
            $query->bindParam(':deviceUuid', $deviceUuid);
            $query->bindParam(':locationName', $locationName);

            try {
                $query->execute();
                Middleware::setHTTPResponse(200, "Success", true);
            } catch (PDOException $e) {
                Middleware::setHTTPResponse(500, "Server error",true);
            }
        } else {
            Middleware::setHTTPResponse(400, "Wrong parameters",true);
        }

    }

    public function updateReport(string $id): void
    {

        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['temperature']) && isset($payload['humidity']) && isset($payload['id'])) {
            $temperature = $payload['temperature'];
            $humidity = $payload['humidity'];

            $query = $this->pdo->prepare('UPDATE REPORT SET temperature = :temperature, humidity = :humidity WHERE reportId = :reportId');

            try {
                $query->execute(['temperature' => $temperature, 'humidity' => $humidity, 'reportId' => $id]);
                Middleware::setHTTPResponse(200, "Success",true);
            } catch (PDOException $e) {
                Middleware::setHTTPResponse(500, "Server error",true);
            }
        } else {
            Middleware::setHTTPResponse(400, "Wrong parameters",true);
        }

    }

    public function deleteReport(string $id): void
    {

        $query = $this->pdo->prepare('DELETE FROM REPORT WHERE reportId = :reportId');

        try {

            $query->execute(['reportId' => $id]);
            Middleware::setHTTPResponse(200, "Success",true);

        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error",true);
        }
    }

}


