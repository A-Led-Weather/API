<?php

namespace controller;

use model\ReportModel;
use utility\Middleware;
use Medoo\Medoo;

class ReportController
{

    private Medoo $dbConnection;
    private ReportModel $reportModel;

    public function __construct(Medoo $dbConnection)
    {
        $this->dbConnection = $dbConnection;
        $this->reportModel = new ReportModel($this->dbConnection);
    }


    public function getLastReports(): void
    {
        $this->reportModel->getLastsReports();
        // Traitement des données et renvoi des réponses
    }

    public function addReports(): void
    {
        // Validation des données reçues
        $this->reportModel->addReport();
        // Renvoi de la réponse
    }

    public function getReportById($id): void
    {
        if (!is_numeric($id)) {
            Middleware::setHTTPResponse(400, 'Invalid Values', true);
            exit();
        }
        $this->reportModel->getReportById($id);
        // Traitement des données et renvoi des réponses
    }

    public function deleteReport($id): void
    {
        if (!is_numeric($id)) {
            Middleware::setHTTPResponse(400, 'Invalid Values', true);
            exit();
        }
        $this->reportModel->deleteReport($id);
        // Renvoi de la réponse
    }

    public function getLastReportByLocation($location): void
    {
        $this->reportModel->getLastReportByLocation($location);
        // Traitement des données et renvoi des réponses
    }

    public function getReportsByLocationByTimeRange($location, $timeRange): void
    {
        $this->reportModel->getReportsByLocationByTimeRange($location, $timeRange);
        // Traitement des données et renvoi des réponses
    }

}