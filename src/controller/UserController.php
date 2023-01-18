<?php

namespace controller;
use model\UserModel;
use utility\Middleware;
class UserController
{

    private object $pdo;
    private UserModel $userModel;

    public function __construct(object $pdo)
    {
        $this->pdo = $pdo;
        $this->userModel = new UserModel($this->pdo);
    }

    public function requestSelector(string $requestMethod, array $routeInfoArray): void
    {
        switch ($requestMethod) {
            case "GET":
                if (isset($routeInfoArray['id'])) {
                    $this->userModel->getUserById($routeInfoArray['id']);
                } elseif (isset($routeInfoArray['email'])) {
                    $this->userModel->getUserByEmail($routeInfoArray['email']);
                } else {
                    Middleware::setHTTPResponse(404, "Route not found",true);
                }
                break;
            case "POST":
                if (isset($routeInfoArray['login'])) {
                    $this->userModel->authenticateUser();
                } else {
                    $this->userModel->addUser();
                }
                break;
            case 'PUT':
                if (isset($routeInfoArray['email'])) {
                    $this->userModel->updateUser($routeInfoArray['email']);
                } else {
                    Middleware::setHTTPResponse(404, "Route not found",true);
                }
                break;
            case 'DELETE':
                if (isset($routeInfoArray['email'])) {
                    $this->userModel->deleteUser($routeInfoArray['email']);
                } else {
                    Middleware::setHTTPResponse(404, "Route not found",true);
                }
                break;
            default:
                Middleware::setHTTPResponse(405, "Method not allowed",true);
                break;
        }
    }
}