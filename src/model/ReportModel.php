<?php

namespace Model;

use Medoo\Medoo;

class ReportModel
{
    private const TABLE_NAME = 'REPORT';
    private const SERVER_TIME_ADJUST = "+1 hour";
    private Medoo $dbConnection;

    public function __construct(Medoo $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function getReportById($id): ?array
    {
        return $this->dbConnection->select(self::TABLE_NAME, "*", ["reportId" => $id]);
    }

    public function getLastReportByLocation(string $location): ?array
    {
        return $this->dbConnection->select(self::TABLE_NAME, "*", ["locationName" => ucfirst($location), "ORDER" => ["reportId" => "DESC"], "LIMIT" => 1]);
    }

    public function getReportsByLocationByTimeRange(string $location, int $limit): ?array
    {
        return $this->dbConnection->select(self::TABLE_NAME, "*", ["locationName" => ucfirst($location), "ORDER" => ["reportId" => "DESC"], "LIMIT" => $limit]);
    }

    public function getLastsReports(): ?array
    {
        return $this->dbConnection->select(self::TABLE_NAME, "*", ["ORDER" => ["reportId" => "DESC"], "LIMIT" => 10]);
    }

    public function addReport($payload): void
    {
        $timeAdjust = strtotime(self::SERVER_TIME_ADJUST);

        $this->dbConnection->insert(self::TABLE_NAME, [
            "temperature" => $payload['temperature'],
            "humidity" => $payload['humidity'],
            "dateTime" => date("Y-m-d H:i:s", $timeAdjust),
            "deviceUuid" => $payload['deviceUuid'],
            "locationName" => $payload['locationName']
        ]);

    }

    public function deleteReport($id): void
    {
        $this->dbConnection->delete(self::TABLE_NAME, [
            "reportId" => $id
        ]);
    }

}

