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

    public function getAverageReportByLocationByTimeRange(string $location, int $limit): ?array
    {
        $temp = $this->dbConnection->avg(self::TABLE_NAME, "temperature", ["locationName" => ucfirst($location), "ORDER" => ["reportId" => "DESC"], "LIMIT" => $limit]);
        $hum = $this->dbConnection->avg(self::TABLE_NAME, "humidity", ["locationName" => ucfirst($location), "ORDER" => ["reportId" => "DESC"], "LIMIT" => $limit]);
        $period = $limit == 60 ? "hourly" : "daily";
        $deviceUuid = $this->dbConnection->select(self::TABLE_NAME, "deviceUuid", ["locationName" => ucfirst($location), "ORDER" => ["reportId" => "DESC"], "LIMIT" => 1]);
        $locationName = $this->dbConnection->select(self::TABLE_NAME, "locationName", ["locationName" => ucfirst($location), "ORDER" => ["reportId" => "DESC"], "LIMIT" => 1]);

        return [
            "temperature" => round($temp, 2),
            "humidity" => round($hum, 2),
            "period" => $period,
            "deviceUuid" => $deviceUuid[0],
            "locationName" => $locationName[0]
        ];
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

