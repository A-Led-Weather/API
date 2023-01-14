<?php

class AuthenticationApiRequests
{
    private object $pdo;

    function __construct(object $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createToken(): string
    {
        $tokenBytes = random_bytes(16);
        return bin2hex($tokenBytes);
    }

}