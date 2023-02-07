<?php

namespace Router;

use Exception;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Utility\HttpHelper;
use function FastRoute\simpleDispatcher;

class Router
{
    private const GET_LAST_REPORTS = ['method' => 'GET', 'uri' => '/reports', 'request' => 'getLastReports'];
    private const ADD_REPORT = ['method' => 'POST', 'uri' => '/reports', 'request' => 'addReport'];
    private const GET_REPORT_BY_ID = ['method' => 'GET', 'uri' => '/reports/{id:\d+}', 'request' => 'getReportById'];
    private const DELETE_REPORT = ['method' => 'DELETE', 'uri' => '/reports/{id:\d+}', 'request' => 'deleteReport'];
    private const GET_LAST_REPORT_BY_LOCATION = ['method' => 'GET', 'uri' => '/reports/{location}', 'request' => 'getLastReportByLocation'];
    private const GET_REPORTS_BY_LOCATION_BY_TIME_RANGE = ['method' => 'GET', 'uri' => '/reports/{location}/{timeRange:hourly|daily}', 'request' => 'getReportsByLocationByTimeRange'];
    private const ADD_USER = ['method' => 'POST', 'uri' => '/users', 'request' => 'addUser'];
    private const GET_USER_BY_EMAIL = ['method' => 'GET', 'uri' => '/users/{email}', 'request' => 'getUserByEmail'];
    private const UPDATE_USER = ['method' => 'PUT', 'uri' => '/users/{email}', 'request' => 'updateUser'];
    private const DELETE_USER = ['method' => 'DELETE', 'uri' => '/users/{email}', 'request' => 'deleteUser'];
    private const AUTHENTICATE_USER = ['method' => 'POST', 'uri' => '/login', 'request' => 'authenticateUser'];
    private const CREATE_JWT = ['method' => 'POST', 'uri' => '/token', 'request' => 'createToken'];
    private const GET_LOCATIONS = ['method' => 'GET', 'uri' => '/locations', 'request' => 'getLocations'];
    private const ADD_LOCATION = ['method' => 'POST', 'uri' => '/locations', 'request' => 'addLocation'];
    private const GET_LOCATION_BY_NAME = ['method' => 'GET', 'uri' => '/locations/{location}', 'request' => 'getLocationByName'];
    private const UPDATE_LOCATION = ['method' => 'PUT', 'uri' => '/locations/{location}', 'request' => 'updateLocation'];
    private const DELETE_LOCATION = ['method' => 'DELETE', 'uri' => '/locations/{location}', 'request' => 'deleteLocation'];
    private const GET_DEVICES = ['method' => 'GET', 'uri' => '/devices', 'request' => 'getDevices'];
    private const ADD_DEVICE = ['method' => 'POST', 'uri' => '/devices', 'request' => 'addDevice'];
    private const GET_DEVICE_BY_ID = ['method' => 'GET', 'uri' => '/devices/{id:\d+}', 'request' => 'getDeviceById'];
    private const UPDATE_DEVICE = ['method' => 'PUT', 'uri' => '/devices/{id:\d+}', 'request' => 'updateDevice'];
    private const DELETE_DEVICE = ['method' => 'DELETE', 'uri' => '/devices/{id:\d+}', 'request' => 'deleteDevice'];
    private const OPTIONS_PRE_FLIGHT_CASE_1 = ['method' => 'OPTIONS', 'uri' => '/{any}', 'request' => 'options'];
    private const OPTIONS_PRE_FLIGHT_CASE_2 = ['method' => 'OPTIONS', 'uri' => '/{any1}/{any2}', 'request' => 'options'];
    private const OPTIONS_PRE_FLIGHT_CASE_3 = ['method' => 'OPTIONS', 'uri' => '/{any1}/{any2}/{any3}', 'request' => 'options'];

    private Dispatcher $dispatcher;

    public function __construct()
    {
        $this->dispatcher = $this->addRoute();
    }


    private function addRoute(): Dispatcher
    {

        return simpleDispatcher(function (RouteCollector $r) {
            $r->addRoute(self::GET_LAST_REPORTS['method'],
                self::GET_LAST_REPORTS['uri'],
                self::GET_LAST_REPORTS['request']);
            $r->addRoute(self::ADD_REPORT['method'],
                self::ADD_REPORT['uri'],
                self::ADD_REPORT['request']);
            $r->addRoute(self::GET_REPORT_BY_ID['method'],
                self::GET_REPORT_BY_ID['uri'],
                self::GET_REPORT_BY_ID['request']);
            $r->addRoute(self::DELETE_REPORT['method'],
                self::DELETE_REPORT['uri'],
                self::DELETE_REPORT['request']);
            $r->addRoute(self::GET_LAST_REPORT_BY_LOCATION['method'],
                self::GET_LAST_REPORT_BY_LOCATION['uri'],
                self::GET_LAST_REPORT_BY_LOCATION['request']);
            $r->addRoute(self::GET_REPORTS_BY_LOCATION_BY_TIME_RANGE['method'],
                self::GET_REPORTS_BY_LOCATION_BY_TIME_RANGE['uri'],
                self::GET_REPORTS_BY_LOCATION_BY_TIME_RANGE['request']);
            $r->addRoute(self::ADD_USER['method'],
                self::ADD_USER['uri'],
                self::ADD_USER['request']);
            $r->addRoute(self::GET_USER_BY_EMAIL['method'],
                self::GET_USER_BY_EMAIL['uri'],
                self::GET_USER_BY_EMAIL['request']);
            $r->addRoute(self::UPDATE_USER['method'],
                self::UPDATE_USER['uri'],
                self::UPDATE_USER['request']);
            $r->addRoute(self::DELETE_USER['method'],
                self::DELETE_USER['uri'],
                self::DELETE_USER['request']);
            $r->addRoute(self::AUTHENTICATE_USER['method'],
                self::AUTHENTICATE_USER['uri'],
                self::AUTHENTICATE_USER['request']);
            $r->addRoute(self::CREATE_JWT['method'],
                self::CREATE_JWT['uri'],
                self::CREATE_JWT['request']);
            $r->addRoute(self::GET_LOCATIONS['method'],
                self::GET_LOCATIONS['uri'],
                self::GET_LOCATIONS['request']);
            $r->addRoute(self::ADD_LOCATION['method'],
                self::ADD_LOCATION['uri'],
                self::ADD_LOCATION['request']);
            $r->addRoute(self::GET_LOCATION_BY_NAME['method'],
                self::GET_LOCATION_BY_NAME['uri'],
                self::GET_LOCATION_BY_NAME['request']);
            $r->addRoute(self::UPDATE_LOCATION['method'],
                self::UPDATE_LOCATION['uri'],
                self::UPDATE_LOCATION['request']);
            $r->addRoute(self::DELETE_LOCATION['method'],
                self::DELETE_LOCATION['uri'],
                self::DELETE_LOCATION['request']);
            $r->addRoute(self::GET_DEVICES['method'],
                self::GET_DEVICES['uri'],
                self::GET_DEVICES['request']);
            $r->addRoute(self::ADD_DEVICE['method'],
                self::ADD_DEVICE['uri'],
                self::ADD_DEVICE['request']);
            $r->addRoute(self::GET_DEVICE_BY_ID['method'],
                self::GET_DEVICE_BY_ID['uri'],
                self::GET_DEVICE_BY_ID['request']);
            $r->addRoute(self::UPDATE_DEVICE['method'],
                self::UPDATE_DEVICE['uri'],
                self::UPDATE_DEVICE['request']);
            $r->addRoute(self::DELETE_DEVICE['method'],
                self::DELETE_DEVICE['uri'],
                self::DELETE_DEVICE['request']);
            $r->addRoute(self::OPTIONS_PRE_FLIGHT_CASE_1['method'],
                self::OPTIONS_PRE_FLIGHT_CASE_1['uri'],
                self::OPTIONS_PRE_FLIGHT_CASE_1['request']);
            $r->addRoute(self::OPTIONS_PRE_FLIGHT_CASE_2['method'],
                self::OPTIONS_PRE_FLIGHT_CASE_2['uri'],
                self::OPTIONS_PRE_FLIGHT_CASE_2['request']);
            $r->addRoute(self::OPTIONS_PRE_FLIGHT_CASE_3['method'],
                self::OPTIONS_PRE_FLIGHT_CASE_3['uri'],
                self::OPTIONS_PRE_FLIGHT_CASE_3['request']);
        });
    }

    public function dispatch(string $requestMethod, string $route): array
    {
        return $this->dispatcher->dispatch($requestMethod, $route);
    }

    public function trigRequest(array $routeComponents, string $route, array $controllers): void
    {

        switch ($routeComponents[0]) {
            case Dispatcher::NOT_FOUND:
                HttpHelper::setResponse(404, 'Route Not Found', true);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                HttpHelper::setResponse(405, 'Method Not Allowed', true);
                break;
            case Dispatcher::FOUND:
                $handler = $routeComponents[1];
                $vars = $routeComponents[2];
                if ($handler == 'options') {
                    HttpHelper::setResponse(200, 'Welcome to the API', true);
                    exit;
                }
                $basePath = $route == '/token' || $route == "/login" ? ['', 'users'] : explode("/", $route);
                $controller = $controllers[$basePath[1]];
                call_user_func_array([$controller, $handler], $vars);
                break;
            default:
                HttpHelper::setResponse(400, 'Unexpected Error', true);
        }
    }
}