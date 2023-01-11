<?php

require "vendor/autoload.php";
require "dbConnection.php";
require "Classes/AuthenticationApiRequests.php";
require "Classes/ReportsApiRequests.php";
require "Classes/HttpHandlerUtilities.php";

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

$weatherReportsRequest = new ReportsApiRequests($pdo);
$authenticationRequest = new AuthenticationApiRequests($pdo);

$routeExplode = explode('/', $route);

if (count($routeExplode) == 3) {
    if ($routeExplode[1] == 'reports') {
        preg_match('/^(\/\w+)\/(\d+)$/', $route, $matches);
        $route_base = $matches[1];
        $id = $matches[2];
    }
} elseif (count($routeExplode) == 2) {
    if ($routeExplode[1] == 'reports') {
        $route_base = $route;
    }
} else {
    // Route invalide
    header("HTTP/1.0 404 Not Found");
    HttpHandlerUtilities::setHTTPResponse(404, false);
    exit();
}


switch ($route_base) {

    case '/reports':
        switch ($request_method) {
            case "GET":
                $weatherReportsRequest->getLastsReports();
                break;
            case "POST":
                $weatherReportsRequest->addReport();
                break;
            case 'PUT':
                $weatherReportsRequest->updateReport($id);
                break;
            case 'DELETE':
                $weatherReportsRequest->deleteReport($id);
                break;
            default:
                header("HTTP/1.0 405 Method Not Allowed");
                HttpHandlerUtilities::setHTTPResponse(405, false);
                break;
        }
        break;
    default:
        // Route invalide
        header("HTTP/1.0 404 Not Found");
        HttpHandlerUtilities::setHTTPResponse(404, false);
        break;

}
