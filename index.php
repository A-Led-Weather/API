<?php

require "dbConnection.php";
require "Classes/AuthenticationApiRequests.php";
require "Classes/ReportsApiRequests.php";
require "Classes/HttpHandlerUtilities.php";

$request_method = $_SERVER["REQUEST_METHOD"];
$route = $_SERVER["REQUEST_URI"];

$pdo = dbConnection();

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
        HttpHandlerUtilities::setHTTPResponse(405, false);
        break;

}
