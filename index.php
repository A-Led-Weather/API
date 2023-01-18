<?php

declare(strict_types=1);

require "vendor/autoload.php";

require "classes/Middleware.php";
require "classes/ReportsRequests.php";
require "classes/DbConnector.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbConnection = $_ENV['DB_CONNECTION'];
$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$dbUser = $_ENV['DB_USER'];
$dbPassword = $_ENV['DB_PASSWORD'];

$request_method = $_SERVER["REQUEST_METHOD"];
$route = $_SERVER["REQUEST_URI"];

$dbConnector = new DbConnector($dbConnection, $dbHost, $dbName, $dbUser, $dbPassword);
$pdo = $dbConnector->dbConnection();

$routeInfoArray = Middleware::fetchRoutePathParameters($route);

switch ($routeInfoArray['route_base']) {

    case 'reports':
        $reportsRequests = new ReportsRequests($pdo);
        $reportsRequests->requestSelector($request_method, $routeInfoArray);
        break;
    default:
        // Route invalide
        header("HTTP/1.0 404 Not Found");
        Middleware::setHTTPResponse(404, "Route not found", "HTTP/1.0 404 Not Found", true);
        break;
}
