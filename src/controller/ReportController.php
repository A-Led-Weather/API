<?php


class ReportController
{

    private object $pdo;
    private ReportModel $reportModel;

    public function __construct(object $pdo)
    {
        $this->pdo = $pdo;
        $this->reportModel = new ReportModel($this->pdo);
    }
    public function requestSelector(string $requestMethod, array $routeInfoArray): void
    {
        switch ($requestMethod) {
            case "GET":
                if (isset($routeInfoArray['id'])) {
                    $this->reportModel->getReportById($routeInfoArray['id']);
                } elseif (isset($routeInfoArray['location']) && !isset($routeInfoArray['range'])) {
                    $this->reportModel->getLastReportByLocation($routeInfoArray['location']);
                } elseif (isset($routeInfoArray['range']) && $routeInfoArray['range'] === 'hourly') {
                    $this->reportModel->getLastHourReportsByLocation($routeInfoArray['location']);
                } elseif (isset($routeInfoArray['range']) && $routeInfoArray['range'] === 'daily') {
                    $this->reportModel->getLastDayReportsByLocation($routeInfoArray['location']);
                } else {
                    $this->reportModel->getLastsReports();
                }
                break;
            case "POST":
                $this->reportModel->addReport();
                break;
            case 'PUT':
                if (isset($routeInfoArray['id'])) {
                    $this->reportModel->updateReport($routeInfoArray['id']);
                } else {
                    Middleware::setHTTPResponse(404, "Route not found",true);
                }
                break;
            case 'DELETE':
                if (isset($routeInfoArray['id'])) {
                    $this->reportModel->deleteReport($routeInfoArray['id']);
                } else {
                    Middleware::setHTTPResponse(404, "Route not found",true);
                }
                break;
            default:
                Middleware::setHTTPResponse(405, "Method not allowed",true);
                break;
        }
    }
}