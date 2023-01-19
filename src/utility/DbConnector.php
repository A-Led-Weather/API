<?php

namespace utility;

use Medoo\Medoo;
use PDO;
use PDOException;


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

    /*public function dbConnection(): PDO
    {
        try {
            $pdo = new PDO($this->dbConnection . ':host=' . $this->dbHost . ';dbname=' . $this->dbName, $this->dbUser, $this->dbPassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error", true);
            exit;
        }
        return $pdo;
    }*/

    public function dbConnection(): Medoo
    {
        try {

            $dbConnection =  new Medoo([
                // [required]
                'type' => $this->dbType,
                'host' => $this->dbHost,
                'database' => $this->dbName,
                'username' => $this->dbUser,
                'password' => $this->dbPassword,
                'port' => $this->dbPort,
                'logging' => true,
                'error' => PDO::ERRMODE_SILENT,

            ]);
        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500,'Server Error', true);
            exit();
        }

        return $dbConnection;
    }


}