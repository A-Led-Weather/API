<?php

namespace Utility;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

abstract class AccessControl
{
    public static function createDeviceToken(): string
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
}