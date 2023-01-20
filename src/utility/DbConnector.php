<?php

namespace Utility;

use Exception;
use Medoo\Medoo;
use PDO;


class DbConnector
{
    private string $dbType;
    private string $dbHost;
    private string $dbName;
    private string $dbUser;
    private string $dbPassword;
    private string $dbPort;

    public function __construct(string $dbType, string $dbHost, string $dbPort, string $dbName, string $dbUser, string $dbPassword)
    {
        $this->dbType = $dbType;
        $this->dbHost = $dbHost;
        $this->dbPort = $dbPort;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
    }

    public function dbConnect(): Medoo
    {
        try {
            $dbConnection =  new Medoo([
                'type' => $this->dbType,
                'host' => $this->dbHost,
                'database' => $this->dbName,
                'username' => $this->dbUser,
                'password' => $this->dbPassword,
                'port' => $this->dbPort,
                'logging' => true,
                'error' => PDO::ERRMODE_SILENT,

            ]);
        } catch (Exception $e) {
            HttpHelper::setHttpResponse(500,'Server Error', true);
            exit();
        }

        return $dbConnection;
    }


}