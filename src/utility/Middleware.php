<?php

namespace utility;
abstract class Middleware
{
    public static function createToken(): string
    {
        $tokenBytes = random_bytes(16);
        return bin2hex($tokenBytes);
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function isValidPassword(string $password, string $hashedPassword): bool
    {
        if (password_verify($password, $hashedPassword)) {
            return true;
        } else {
            return false;
        }
    }

    public static function fetchRoutePathParameters(string $route): array
    {

        $routeExplode = explode('/', $route);

        if (!in_array($routeExplode[1], ["reports", "devices", "locations", "users"])) {
            self::setHTTPResponse(404, "Route not found", true);
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
                    if (!strpos($routeExplode[2], '@') && $routeExplode[2] != 'login') {
                        $routeArray['location'] = $routeExplode[2];
                    } elseif ($routeExplode[2] == 'login')
                        $routeArray['login'] = true;
                    {
                        $routeArray['email'] = $routeExplode[2];
                    }
                }
                break;
            case 2:
                $routeArray['route_base'] = $routeExplode[1];
                break;
            default:
                self::setHTTPResponse(404, "Route not found",  true);
                exit();
        }
        return $routeArray;
    }

    public static function setHTTPResponse(int $httpCode, mixed $state, bool $sendRequestState): void
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        if ($sendRequestState) {
            echo json_encode(['result' => $state]);
        }
    }
}