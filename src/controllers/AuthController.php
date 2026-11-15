<?php
declare(strict_types=1);

namespace ExpenseFlow\controllers;

use ExpenseFlow\exceptions\ValidationException;
use ExpenseFlow\http\JsonResponse;
use ExpenseFlow\http\Request;
use ExpenseFlow\services\AuthService;

class AuthController
{

    public function __construct(private AuthService $service)
    {
    }

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

}