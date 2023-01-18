<?php

class UsersRequests
{
    private object $pdo;

    public function __construct(object $pdo)
    {
        $this->pdo = $pdo;
    }

    public function requestSelector(string $requestMethod, array $routeInfoArray): void
    {
        switch ($requestMethod) {
            case "GET":
                if (isset($routeInfoArray['id'])) {
                    $this->getUserById($routeInfoArray['id']);
                } elseif (isset($routeInfoArray['email'])) {
                    $this->getUserByEmail($routeInfoArray['email']);
                } else {
                    Middleware::setHTTPResponse(404, "Route not found",true);
                }
                break;
            case "POST":
                if (isset($routeInfoArray['login'])) {
                    $this->authenticateUser();
                } else {
                    $this->addUser();
                }
                break;
            case 'PUT':
                if (isset($routeInfoArray['email'])) {
                    $this->updateUser($routeInfoArray['email']);
                } else {
                    Middleware::setHTTPResponse(404, "Route not found",true);
                }
                break;
            case 'DELETE':
                if (isset($routeInfoArray['email'])) {
                    $this->deleteUser($routeInfoArray['email']);
                } else {
                    Middleware::setHTTPResponse(404, "Route not found",true);
                }
                break;
            default:
                Middleware::setHTTPResponse(405, "Method not allowed",true);
                break;
        }
    }

    public function getUserById(string $id): void
    {
        $query = $this->pdo->prepare('SELECT * FROM USER WHERE (userId = :userId)');

        try {
            $query->execute(['userId' => $id]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            Middleware::setHTTPResponse(200, "Success",false);
            echo json_encode($results);
        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error",true);
        }
    }

    public function getUserByEmail(string $email): void
    {
        $query = $this->pdo->prepare('SELECT * FROM USER WHERE (userEmail = :userEmail)');

        try {
            $query->execute(['userEmail' => $email]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            Middleware::setHTTPResponse(200, "Success",false);
            echo json_encode($results);
        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error",true);
        }
    }

    public function addUser(): void
    {

        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['userName']) && isset($payload['userEmail']) && isset($payload['userPassword'])) {
            $userName = $payload['userName'];
            $userEmail = $payload['userEmail'];
            $userPassword = $payload['userPassword'];
            $hashedPassword = Middleware::hashPassword($userPassword);

            $query = $this->pdo->prepare('INSERT INTO USER (userName, userEmail, userPassword) VALUES (:userName, :userEmail, :userPassword)');

            $query->bindParam(':userName', $userName);
            $query->bindParam(':userEmail', $userEmail);
            $query->bindParam(':userPassword', $hashedPassword);

            try {
                $query->execute();
                Middleware::setHTTPResponse(200, "Success",true);
            } catch (PDOException $e) {
                Middleware::setHTTPResponse(500, "Server error",true);
                echo $e->getMessage();
            }
        } else {
            Middleware::setHTTPResponse(400, "Wrong parameters",true);
        }

    }

    public function authenticateUser(): void
    {

        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['userEmail']) && isset($payload['userPassword'])) {

            $userEmail = $payload['userEmail'];
            $userPassword = $payload['userPassword'];

            $query = $this->pdo->prepare('SELECT * FROM USER WHERE (userEmail = :userEmail)');

            try {
                $query->execute(['userEmail' => $userEmail]);
                $results = $query->fetchAll(PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                Middleware::setHTTPResponse(500, "Server error",true);
                exit();
            }
        } else {
            Middleware::setHTTPResponse(400, "Wrong parameters",true);
            exit();
        }

        $hashedPassword = $results[0]['userPassword'];

        if (Middleware::isValidPassword($userPassword, $hashedPassword)) {
            Middleware::setHTTPResponse(200, true,true);
        } else {
            Middleware::setHTTPResponse(200, false,true );
        }

    }

    public function updateUser(string $email): void
    {

        $payload = json_decode(file_get_contents("php://input"), true);

        if (isset($payload['userName']) && isset($payload['userPassword'])) {
            $userName = $payload['userName'];
            $userPassword = $payload['userPassword'];
            $hashedPassword = Middleware::hashPassword($userPassword);

            $query = $this->pdo->prepare('UPDATE USER SET userName = :userName, userPassword = :userPassword WHERE userEmail = :userEmail');

            try {
                $query->execute(['userName' => $userName, 'userPassword' => $hashedPassword, 'userEmail' => $email]);
                Middleware::setHTTPResponse(200, "Success",true);
            } catch (PDOException $e) {
                Middleware::setHTTPResponse(500, "Server error",true);
            }
        } else {
            Middleware::setHTTPResponse(400, "Wrong parameters",true);
        }

    }

    public function deleteUser(string $email): void
    {

        $query = $this->pdo->prepare('DELETE FROM USER WHERE userEmail = :userEmail');

        try {

            $query->execute(['userEmail' => $email]);
            Middleware::setHTTPResponse(200, "Success",true);

        } catch (PDOException $e) {
            Middleware::setHTTPResponse(500, "Server error",true);
        }
    }

}