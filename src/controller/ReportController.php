<?php

namespace controller;

use model\ReportModel;
use utility\Middleware;

class ReportController
{

    private object $pdo;
    private ReportModel $reportModel;

    public function __construct(object $pdo)
    {
        $this->pdo = $pdo;
        $this->reportModel = new ReportModel($this->pdo);
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
        $this->reportModel->getReportById($id);
        // Traitement des données et renvoi des réponses
    }

    public function updateReport($id): void
    {
        // Validation des données reçues
        $this->reportModel->updateReport($id);
        // Renvoi de la réponse
    }

    public function deleteReport($id): void
    {
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