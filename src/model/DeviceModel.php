<?php

namespace Model;

use Medoo\Medoo;

class DeviceModel
{
    private const TABLE_NAME = 'DEVICE';
    private Medoo $dbConnection;

    public function __construct(Medoo $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function getDevices(): array
    {
        return $this->dbConnection->select(self::TABLE_NAME, '*');
    }

    public function getDeviceById(string $id): ?array
    {
        return $this->dbConnection->select(self::TABLE_NAME, "*", ["deviceId" => $id]);
    }

    public function addDevice($payload): void
    {
        $this->dbConnection->insert(self::TABLE_NAME, [
            "deviceUuid" => uniqid(),
            "token" => bin2hex(random_bytes(16)),
            "model" => $payload['model'],
            "locationName" => $payload['locationName'],
        ]);
    }

    public function updateDevice(string $id, $payload): void
    {
        $this->dbConnection->update(self::TABLE_NAME, [
            "deviceUuid" => $payload['deviceUuid'],
            "token" => $payload['token'],
            "model" => $payload['model'],
            "locationName" => $payload['locationName'],
        ], [
            "deviceId" => $id
        ]);
    }

    public function deleteDevice(string $id): void
    {
        $this->dbConnection->delete(self::TABLE_NAME, [
            "deviceId" => $id
        ]);
    }
}