<?php

namespace controller;

use model\UserModel;
use utility\Middleware;
use Medoo\Medoo;

class UserController
{

    private Medoo $dbConnection;
    private UserModel $userModel;

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
        $this->userModel = new UserModel($this->dbConnection);
    }

    public function addUser(): void
    {
        // Validation des données reçues
        $this->userModel->addUser();
        // Renvoi de la réponse
    }

    public function getUserByEmail($email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Middleware::setHTTPResponse(400, 'Invalid Email', true);
            exit();
        }
        $this->userModel->getUserByEmail($email);
        // Traitement des données et renvoi des réponses
    }

    public function updateUser($email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Middleware::setHTTPResponse(400, 'Invalid Email', true);
            exit();
        }
        // Validation des données reçues
        $this->userModel->updateUser($email);
        // Renvoi de la réponse
    }

    public function deleteUser($email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Middleware::setHTTPResponse(400, 'Invalid Email', true);
            exit();
        }
        $this->userModel->deleteUser($email);
        // Renvoi de la réponse
    }

    public function authenticateUser(): void
    {
        // Validation des données de connexion reçues
        $this->userModel->authenticateUser();
        // Traitement des données et renvoi des réponses
    }
}