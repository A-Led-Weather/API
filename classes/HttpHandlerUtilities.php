<?php

abstract class HttpHandlerUtilities
{
    public static function fetchRoutePathParameters(string $route): array
    {

        $routeExplode = explode('/', $route);

        if (!in_array($routeExplode[1], ["reports", "devices", "locations", "users"])) {
            // Route invalide
            header("HTTP/1.0 404 Not Found");
            self::setHTTPResponse(404, false);
            exit();
        }

        if (count($routeExplode) == 4) {
            $routeArray['route_base'] = $routeExplode[1];
            $routeArray['location'] = $routeExplode[2];
            $routeArray['range'] = $routeExplode[3];
        } elseif (count($routeExplode) == 3) {
            $routeArray['route_base'] = $routeExplode[1];
            if (is_numeric($routeExplode[2])) {
                $routeArray['id'] = $routeExplode[2];
            } else {
                $routeArray['location'] = $routeExplode[2];
            }
        } elseif (count($routeExplode) == 2) {
            $routeArray['route_base'] = $routeExplode[1];
        } else {
            // Route invalide
            header("HTTP/1.0 404 Not Found");
            self::setHTTPResponse(404, false);
            exit();
        }

        return $routeArray;
    }

    public static function setHTTPResponse(int $httpCode, bool $state): void
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo json_encode(['Success' => $state]);
    }
}