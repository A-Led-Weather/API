<?php

namespace Utility;
abstract class HttpHelper
{
    public static function setResponse(int $httpCode, mixed $state, bool $sendRequestResultState): void
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        if ($sendRequestResultState) {
            echo json_encode(['result' => $state]);
        }
    }

    public static function getAuthHeader(): array|false
    {
        $headers = $_SERVER;
        if (isset($headers['Authorization'])) {
            $bearerToken = $headers['Authorization'];
            return explode(' ', $bearerToken)[1];
        } else {
            return false;
        }
    }

}