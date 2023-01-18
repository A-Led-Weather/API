<?php

declare(strict_types=1);

require "vendor/autoload.php";

use controller\ReportController;
use controller\UserController;
use utility\DbConnector;
use utility\Middleware;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbConnection = $_ENV['DB_CONNECTION'];
$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$dbUser = $_ENV['DB_USER'];
$dbPassword = $_ENV['DB_PASSWORD'];

$requestMethod = $_SERVER["REQUEST_METHOD"];
$route = $_SERVER["REQUEST_URI"];

$dbConnector = new DbConnector($dbConnection, $dbHost, $dbName, $dbUser, $dbPassword);
$pdo = $dbConnector->dbConnection();

$routeInfoArray = Middleware::fetchRoutePathParameters($route);

switch ($routeInfoArray['route_base']) {

    case 'reports':
        $reportsRequests = new ReportController($pdo);
        $reportsRequests->requestSelector($requestMethod, $routeInfoArray);
        break;
    case 'users':
        $usersRequests = new UserController($pdo);
        $usersRequests->requestSelector($requestMethod, $routeInfoArray);
        break;
    default:
        Middleware::setHTTPResponse(404, "Route not found", true);
        break;
}
