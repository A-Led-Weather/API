<?php

namespace Model;

use Medoo\Medoo;
use Utility\AccessControl;

class UserModel
{
    private const TABLE_NAME = 'USER';
    private Medoo $dbConnection;

    public function __construct(Medoo $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function getUserByEmail(string $email): ?array
    {
        return $this->dbConnection->select(self::TABLE_NAME, "*", ["userEmail" => $email]);
    }

    public function addUser($payload): void
    {

        $userName = $payload['userName'];
        $userEmail = $payload['userEmail'];
        $userPassword = $payload['userPassword'];
        $hashedPassword = AccessControl::hashPassword($userPassword);

        $this->dbConnection->insert(self::TABLE_NAME, [
            "userName" => $userName,
            "userEmail" => $userEmail,
            "userPassword" => $hashedPassword
        ]);
    }

    public function authenticateUser($payload): ?array
    {
        $userEmail = $payload['userEmail'];
        return $this->dbConnection->select(self::TABLE_NAME, "*", ["userEmail" => $userEmail]);
    }

    public function updateUser(string $email, $payload): void
    {
        $userName = $payload['userName'];
        $userEmail = $payload['userEmail'];
        $userPassword = $payload['userPassword'];
        $hashedPassword = AccessControl::hashPassword($userPassword);

        $this->dbConnection->update(self::TABLE_NAME, [
            "userName" => $userName,
            "userEmail" => $userEmail,
            "userPassword" => $hashedPassword
        ], ["userEmail" => $email]);
    }

    public function deleteUser(string $email): void
    {
        $this->dbConnection->delete(self::TABLE_NAME, ["userEmail" => $email]);
    }
}


