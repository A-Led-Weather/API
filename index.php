<?php

require_once 'vendor/autoload.php';

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
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

$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    //REPORTS
    $r->addRoute('GET', '/reports', 'getLastReports');
    $r->addRoute('POST', '/reports', 'addReports');
    $r->addRoute('GET', '/reports/{id:\d+}', 'getReportById');
    $r->addRoute('PUT', '/reports/{id:\d+}', 'updateReport');
    $r->addRoute('DELETE', '/reports/{id:\d+}', 'deleteReport');
    $r->addRoute('GET', '/reports/{location}', 'getLastReportByLocation');
    $r->addRoute('GET', '/reports/{location}/{timeRange:hourly|daily}', 'getReportsByLocationByTimeRange');
    //USERS
    $r->addRoute('POST', '/users', 'addUser');
    $r->addRoute('GET', '/users/{email}', 'getUserByEmail');
    $r->addRoute('PUT', '/users/{email}', 'updateUser');
    $r->addRoute('DELETE', '/users/{email}', 'deleteUser');
    $r->addRoute('POST', '/users/login', 'authenticateUser');
});

$routeInfo = $dispatcher->dispatch($requestMethod, $route);

$controllers = [
    'reports' => new ReportController($pdo),
    'users' => new UserController($pdo),
];

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        Middleware::setHTTPResponse(404, 'Route Not Found', true);
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        Middleware::setHTTPResponse(405, 'Method Not Allowed', true);
        break;
    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        $basePath = explode("/", $route);
        $controller = $controllers[$basePath[1]];
        call_user_func_array([$controller, $handler], $vars);
        break;
}
