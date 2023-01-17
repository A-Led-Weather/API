<?php

class ReportsApiRequests
{

    private object $pdo;

    function __construct(object $pdo)
    {
        $this->pdo = $pdo;
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
                HttpHandlerUtilities::setHTTPResponse(200, True);
            } catch (PDOException $e) {
                HttpHandlerUtilities::setHTTPResponse(500, False);
            }
        } else {
            HttpHandlerUtilities::setHTTPResponse(401, False);
        }

    }

    public function getReport(string $id): void
    {
        $query = $this->pdo->prepare('SELECT * FROM REPORT WHERE (reportId = :reportId)');

        try {
            $query->execute(['reportId' => $id]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode($results);
        } catch (PDOException $e) {
            HttpHandlerUtilities::setHTTPResponse(500, False);
        }
    }

    public function getLastsReports(): void
    {

        $query = $this->pdo->prepare('SELECT * FROM REPORT ORDER BY reportId DESC LIMIT 10;');

        try {
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode($results);
        } catch (PDOException $e) {
            HttpHandlerUtilities::setHTTPResponse(500, False);
        }
    }

    public function getLastHourReportsByLocation(string $location): void
    {

        $query = $this->pdo->prepare('SELECT * FROM REPORT WHERE locationName = :locationName ORDER BY reportId DESC LIMIT 120;');

        try {
            $query->execute(['locationName' => ucfirst($location)]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            if (empty($results)) {
                HttpHandlerUtilities::setHTTPResponse(404, False);
                exit();
            }
            http_response_code(200);
            echo json_encode($results);

        } catch (PDOException $e) {
            HttpHandlerUtilities::setHTTPResponse(500, False);
        }


    }

    public function getLastReportByLocation(string $location): void
    {

        $query = $this->pdo->prepare('SELECT * FROM REPORT WHERE locationName = :locationName ORDER BY reportId DESC LIMIT 1;');

        try {
            $query->execute(['locationName' => ucfirst($location)]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            if (empty($results)) {
                HttpHandlerUtilities::setHTTPResponse(404, False);
                exit();
            }
            http_response_code(200);
            echo json_encode($results);

        } catch (PDOException $e) {
            HttpHandlerUtilities::setHTTPResponse(500, False);
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

                HttpHandlerUtilities::setHTTPResponse(200, True);

            } catch (PDOException $e) {
                HttpHandlerUtilities::setHTTPResponse(500, False);
            }

        } else {
            HttpHandlerUtilities::setHTTPResponse(400, False);
        }

    }

    public function deleteReport(string $id): void
    {

        $query = $this->pdo->prepare('DELETE FROM REPORT WHERE reportId = :reportId');

        try {

            $query->execute(['reportId' => $id]);
            HttpHandlerUtilities::setHTTPResponse(200, True);

        } catch (PDOException $e) {
            HttpHandlerUtilities::setHTTPResponse(500, False);
        }
    }

    public function routeSwitcher(string $request_method, array $routeInfoArray) : void
    {
        switch ($request_method) {
            case "GET":
                if (isset($routeInfoArray['id'])) {
                    $this->getReport($routeInfoArray['id']);
                } elseif (isset($routeInfoArray['location']) && !isset($routeInfoArray['range'])) {
                    $this->getLastReportByLocation($routeInfoArray['location']);
                } elseif (isset($routeInfoArray['range']) && $routeInfoArray['range'] === 'hourly') {
                    $this->getLastHourReportsByLocation($routeInfoArray['location']);
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
                header("HTTP/1.0 405 Method Not Allowed");
                HttpHandlerUtilities::setHTTPResponse(405, false);
                break;
        }
    }

}


