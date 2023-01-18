<?php

class ReportsRequests
{

    private object $pdo;

    public function __construct(object $pdo)
    {
        $this->pdo = $pdo;
    }

    public function requestSelector(string $requestMethod, array $routeInfoArray): void
    {
        switch ($requestMethod) {
            case "GET":
                if (isset($routeInfoArray['id'])) {
                    $this->getReport($routeInfoArray['id']);
                } elseif (isset($routeInfoArray['location']) && !isset($routeInfoArray['range'])) {
                    $this->getLastReportByLocation($routeInfoArray['location']);
                } elseif (isset($routeInfoArray['range']) && $routeInfoArray['range'] === 'hourly') {
                    $this->getLastHourReportsByLocation($routeInfoArray['location']);
                } elseif (isset($routeInfoArray['range']) && $routeInfoArray['range'] === 'daily') {
                    $this->getLastDayReportsByLocation($routeInfoArray['location']);
                } else {
                    $this->getLastsReports();
                }
                break;
            case "POST":
                $this->addReport();
                break;
            case 'PUT':
                $this->updateReport($routeInfoArray['id']);
                break;
            case 'DELETE':
                $this->deleteReport($routeInfoArray['id']);
                break;
            default:
                Middleware::setHTTPResponse(405, "Method not allowed", "HTTP/1.0 405 Method Not Allowed", true);
                break;
        }
    }

    public function getReport(string $id): void
    {
        $query = $this->pdo->prepare('SELECT * FROM REPORT WHERE (reportId = :reportId)');

        try {
            $query->execute(['reportId' => $id]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            Middleware::setHTTPResponse(200, "Success", "HTTP/1.0 200 OK", false);
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
                Middleware::setHTTPResponse(404, "Location not found", "HTTP/1.0 404 Not Found", true);
                exit();
            }
            Middleware::setHTTPResponse(200, "Success", "HTTP/1.0 200 OK", false);
            echo json_encode($results);

        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error", "HTTP/1.0 500 Internal Error", true);
        }

    }

    public function getLastHourReportsByLocation(string $location): void
    {

        $query = $this->pdo->prepare('SELECT * FROM REPORT WHERE locationName = :locationName ORDER BY reportId DESC LIMIT 60;');

        try {
            $query->execute(['locationName' => ucfirst($location)]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            if (empty($results)) {
                Middleware::setHTTPResponse(404, "Location not found", "HTTP/1.0 404 Not Found", true);
                exit();
            }
            Middleware::setHTTPResponse(200, "Success", "HTTP/1.0 200 OK", false);
            echo json_encode($results);

        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error", "HTTP/1.0 500 Internal Error", true);
        }


    }

    public function getLastDayReportsByLocation(string $location): void
    {

        $query = $this->pdo->prepare('SELECT * FROM REPORT WHERE locationName = :locationName ORDER BY reportId DESC LIMIT 1440;');

        try {
            $query->execute(['locationName' => ucfirst($location)]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            if (empty($results)) {
                Middleware::setHTTPResponse(404, "Location not found", "HTTP/1.0 404 Not Found", true);
                exit();
            }
            Middleware::setHTTPResponse(200, "Success", "HTTP/1.0 200 OK", false);
            echo json_encode($results);

        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error", "HTTP/1.0 500 Internal Error", false);
        }


    }

    public function getLastsReports(): void
    {

        $query = $this->pdo->prepare('SELECT * FROM REPORT ORDER BY reportId DESC LIMIT 10;');

        try {
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            Middleware::setHTTPResponse(200, "Success", "HTTP/1.0 200 OK", false);
            echo json_encode($results);
        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error", "HTTP/1.0 500 Internal Error", true);
        }
    }

    public function addReport(): void
    {

        $reportValues = json_decode(file_get_contents("php://input"), true);

        if (isset($reportValues['temperature']) && isset($reportValues['humidity']) && isset($reportValues['deviceUuid']) && isset($reportValues['locationName'])) {
            $temperature = $reportValues['temperature'];
            $humidity = $reportValues['humidity'];
            $deviceUuid = $reportValues['deviceUuid'];
            $locationName = $reportValues['locationName'];

            $query = $this->pdo->prepare('INSERT INTO REPORT (temperature, humidity, dateTime, deviceUuid, locationName) VALUES (:temperature, :humidity, NOW(), :deviceUuid, :locationName)');

            $query->bindParam(':temperature', $temperature);
            $query->bindParam(':humidity', $humidity);
            $query->bindParam(':deviceUuid', $deviceUuid);
            $query->bindParam(':locationName', $locationName);

            try {
                $query->execute();
                Middleware::setHTTPResponse(200, "Success", "HTTP/1.0 200 OK", false);
            } catch (PDOException $e) {
                Middleware::setHTTPResponse(500, "Server error", "HTTP/1.0 500 Internal Error", true);
            }
        } else {
            Middleware::setHTTPResponse(400, "Wrong parameters", "HTTP/1.0 400 Bad Request", true);
        }

    }

    public function updateReport(string $id): void
    {

        $_PUT = json_decode(file_get_contents("php://input"), true);

        if (isset($_PUT['temperature']) && isset($_PUT['humidity']) && isset($_PUT['id'])) {
            $temperature = $_PUT['temperature'];
            $humidity = $_PUT['humidity'];

            $query = $this->pdo->prepare('UPDATE REPORT SET temperature = :temperature, humidity = :humidity WHERE reportId = :reportId');

            try {
                $query->execute(['temperature' => $temperature, 'humidity' => $humidity, 'reportId' => $id]);
                Middleware::setHTTPResponse(200, "Success", "HTTP/1.0 200 OK", false);
            } catch (PDOException $e) {
                Middleware::setHTTPResponse(500, "Server error", "HTTP/1.0 500 Internal Error", true);
            }
        } else {
            Middleware::setHTTPResponse(400, "Wrong parameters", "HTTP/1.0 400 Bad Request", true);
        }

    }

    public function deleteReport(string $id): void
    {

        $query = $this->pdo->prepare('DELETE FROM REPORT WHERE reportId = :reportId');

        try {

            $query->execute(['reportId' => $id]);
            Middleware::setHTTPResponse(200, "Success", "HTTP/1.0 200 OK", false);

        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error", "HTTP/1.0 500 Internal Error", true);
        }
    }

}


