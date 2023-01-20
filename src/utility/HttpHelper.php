<?php

namespace Utility;
abstract class HttpHelper
{
    public static function setHttpResponse(int $httpCode, mixed $state, bool $sendRequestState): void
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        if ($sendRequestState) {
            echo json_encode(['result' => $state]);
        }
    }

}