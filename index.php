<?php

declare(strict_types=1);

require "vendor/autoload.php";
require "dbConnection.php";
require "classes/AuthenticationApiRequests.php";
require "classes/ReportsApiRequests.php";
require "classes/HttpHandlerUtilities.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbDriver = $_ENV['DB_CONNECTION'];
$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$dbUser = $_ENV['DB_USER'];
$dbPassword = $_ENV['DB_PASSWORD'];

$request_method = $_SERVER["REQUEST_METHOD"];
$route = $_SERVER["REQUEST_URI"];

$pdo = dbConnection($dbDriver, $dbHost, $dbName, $dbUser, $dbPassword);

$reportsRequests = new ReportsApiRequests($pdo);

$routeInfoArray = HttpHandlerUtilities::fetchRouteAndPathParameters($route);

switch ($routeInfoArray['route_base']) {

    case 'reports':
        $reportsRequests->routeSwitcher($request_method, $routeInfoArray);
        break;
    default:
        // Route invalide
        header("HTTP/1.0 404 Not Found");
        HttpHandlerUtilities::setHTTPResponse(404, false);
        break;

}
