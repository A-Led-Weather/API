<?php

abstract class Middleware
{
    public function createToken(): string
    {
        $tokenBytes = random_bytes(16);
        return bin2hex($tokenBytes);
    }

    public static function fetchRoutePathParameters(string $route): array
    {

        $routeExplode = explode('/', $route);

        if (!in_array($routeExplode[1], ["reports", "devices", "locations", "users"])) {
            self::setHTTPResponse(404, "Route not found", "HTTP/1.0 404 Not Found", true);
            exit();
        }

        switch (count($routeExplode)) {
            case 4:
                $routeArray['route_base'] = $routeExplode[1];
                $routeArray['location'] = $routeExplode[2];
                $routeArray['range'] = $routeExplode[3];
                break;
            case 3:
                $routeArray['route_base'] = $routeExplode[1];
                if (is_numeric($routeExplode[2])) {
                    $routeArray['id'] = $routeExplode[2];
                } else {
                    $routeArray['location'] = $routeExplode[2];
                }
                break;
            case 2:
                $routeArray['route_base'] = $routeExplode[1];
                break;
            default:
                self::setHTTPResponse(404, "Route not found", "HTTP/1.0 404 Not Found", true);
                exit();
        }
        return $routeArray;
    }

    public static function setHTTPResponse(int $httpCode, string $state, string $header, bool $sendRequestState): void
    {
        http_response_code($httpCode);
        header($header);
        header('Content-Type: application/json');
        if ($sendRequestState) {
            echo json_encode(['result' => $state]);
        }
    }
}