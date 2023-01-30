<?php

namespace Model;

use Medoo\Medoo;

class LocationModel
{
    private const TABLE_NAME = 'LOCATION';
    private Medoo $dbConnection;

    public function __construct(Medoo $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function getLocationByName(string $name): ?array
    {
        return $this->dbConnection->select(self::TABLE_NAME, "*", ["locationName" => ucfirst($name)]);
    }

    public function getLocations(): ?array
    {
        return $this->dbConnection->select(self::TABLE_NAME, "*");
    }

    public function addLocation($payload): void
    {
        $locationName = $payload['locationName'];
        $latitude = $payload['latitude'];
        $longitude = $payload['longitude'];

        $this->dbConnection->insert(self::TABLE_NAME, [
            "locationName" => $locationName,
            "latitude" => $latitude,
            "longitude" => $longitude
        ]);
    }

    public function updateLocation($payload, string $location): void
    {
        $locationName = $payload['locationName'];
        $latitude = $payload['latitude'];
        $longitude = $payload['longitude'];

        $this->dbConnection->update(self::TABLE_NAME, [
            "locationName" => $locationName,
            "latitude" => $latitude,
            "longitude" => $longitude
        ], [
            "locationName" => ucfirst($location)
        ]);
    }

    public function deleteLocation(string $location): void
    {
        $this->dbConnection->delete(self::TABLE_NAME, [
            "locationName" => $location
        ]);
    }


}