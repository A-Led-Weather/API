<?php

namespace Router;

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
    private const AUTHENTICATE_USER = ['method' => 'POST', 'uri' => '/users/login', 'request' => 'authenticateUser'];


    public function addRoute(string $requestMethod, string $route): array
    {

        $dispatcher = simpleDispatcher(function (RouteCollector $r) {
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
        });
        return $dispatcher->dispatch($requestMethod, $route);
    }

    public function trigRequest(array $routeInfo, string $route, array $controllers): void
    {

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                HttpHelper::setHttpResponse(404, 'Route Not Found', true);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                HttpHelper::setHttpResponse(405, 'Method Not Allowed', true);
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                $basePath = explode("/", $route);
                $controller = $controllers[$basePath[1]];
                call_user_func_array([$controller, $handler], $vars);
                break;
            default:
                HttpHelper::setHttpResponse(400, 'Unexpected Error', true);
        }
    }
}