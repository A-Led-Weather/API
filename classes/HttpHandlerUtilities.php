<?php

abstract class HttpHandlerUtilities
{
    public static function setHTTPResponse(int $httpCode, bool $state): void
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo json_encode(['Success' => $state]);
    }
}