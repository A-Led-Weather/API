<?php

namespace utility;

use PDO;
use PDOException;

class DbConnector
{
    private string $dbConnection;
    private string $dbHost;
    private string $dbName;
    private string $dbUser;
    private string $dbPassword;

    public function __construct(string $dbConnection, string $dbHost, string $dbName, string $dbUser, string $dbPassword)
    {
        $this->dbConnection = $dbConnection;
        $this->dbHost = $dbHost;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
    }

    public function dbConnection(): PDO
    {
        try {
            $pdo = new PDO($this->dbConnection . ':host=' . $this->dbHost . ';dbname=' . $this->dbName, $this->dbUser, $this->dbPassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error", true);
            exit;
        }
        return $pdo;
    }
}