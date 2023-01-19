<?php

namespace utility;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
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

    public static function setHTTPResponse(int $httpCode, mixed $state, bool $sendRequestState): void
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        if ($sendRequestState) {
            echo json_encode(['result' => $state]);
        }
    }

}