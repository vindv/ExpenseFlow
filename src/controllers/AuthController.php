<?php
declare(strict_types=1);

namespace ExpenseFlow\controllers;

use ExpenseFlow\exceptions\AuthenticationException;
use ExpenseFlow\exceptions\ValidationException;
use ExpenseFlow\http\JsonResponse;
use ExpenseFlow\http\Request;
use ExpenseFlow\services\AuthService;

class AuthController
{

    /**
     * @param AuthService $service
     */
    public function __construct(private AuthService $service){}

    /**
     * @param Request $request
     * @return void
     */
    public function register(Request $request): void
    {
        $body = $request->getBody();

        $name = $body['name'] ?? null;
        $email = $body['email'] ?? null;
        $password = $body['password'] ?? null;

        if(!is_string($name) || !is_string($email) || !is_string($password)){
            throw new ValidationException('Name, email and password are required.');
        }

        $userId = $this->service->register($name, $email, $password);

        JsonResponse::send(201, ['data' => ['id' => $userId]]);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function login(Request $request): void
    {
        $body = $request->getBody();

        $email = $body['email'] ?? null;
        $password = $body['password'] ?? null;

        if(!is_string($email) || !is_string($password)){
            throw new ValidationException('Email and password are required.');
        }

        $user = $this->service->login($email, $password);

        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['id'];

        JsonResponse::send(200, ['data' => $user]);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function me(Request $request): void
    {
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;

        if(!is_int($userId)){
            throw new AuthenticationException('Not authenticated.');
        }

        $user = $this->service->getById($userId);

        if($user === null){
            $_SESSION = [];
            session_destroy();

            throw new AuthenticationException('Not authenticated.');
        }

        JsonResponse::send(200, ['data' => $user]);
    }

}