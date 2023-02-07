<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use Controller\LocationController;
use Controller\ReportController;
use Controller\UserController;
use Router\Router;
use Utility\DbConnector;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbType = $_ENV['DB_TYPE'];
$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$dbUser = $_ENV['DB_USER'];
$dbPassword = $_ENV['DB_PASSWORD'];
$dbPort = $_ENV['DB_PORT'];

$jwtKey = $_ENV['JWT_KEY'];

$headers = apache_request_headers();

$requestMethod = $_SERVER["REQUEST_METHOD"];
$route = $_SERVER["REQUEST_URI"];

$dbConnector = new DbConnector($dbType, $dbHost, $dbPort, $dbName, $dbUser, $dbPassword);
$dbConnection = $dbConnector->dbConnect();

$controllers = [
    'reports' => new ReportController($dbConnection, $jwtKey, $headers),
    'users' => new UserController($dbConnection, $jwtKey, $headers),
    'locations' => new LocationController($dbConnection, $jwtKey, $headers)
];

$router = new Router();
$routeComponents = $router->dispatch($requestMethod, $route);
$router->trigRequest($routeComponents, $route, $controllers);