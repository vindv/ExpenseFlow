<?php
declare(strict_types=1);

use ExpenseFlow\database\Connection;

use ExpenseFlow\exceptions\AuthenticationException;
use ExpenseFlow\exceptions\ValidationException;

use ExpenseFlow\repositories\UserRepository;

use ExpenseFlow\services\AuthService;

use ExpenseFlow\http\JsonResponse;
use ExpenseFlow\http\Request;

require __DIR__ . '/../vendor/autoload.php';


try {

    $config = require __DIR__ . '/../config/database.php';

    $pdo = (new Connection($config))->getConnection();
    $users = new UserRepository($pdo);
    $auth = new AuthService($users);

    $request = new Request();

    $method = $request->getMethod();
    $path = $request->getPath();
    $input = $request->getBody();

    if($path === '/register') {
        $name = $input['name'] ?? null;
        $email = $input['email'] ?? null;
        $password = $input['password'] ?? null;

        if(!is_string($name) || !is_string($email) || !is_string($password)) {
            throw new ValidationException('Name, email, and password are required.');
        }

        $userId = $auth->register($name, $email, $password);

        JsonResponse::send(201, [
            'data' => ['id' => $userId],
        ]);
    }

    $email = $input['email'] ?? null;
    $password = $input['password'] ?? null;

    if(!is_string($email) || !is_string($password)) {
        throw new ValidationException('Email and password are required.');
    }

    $user = $auth->login($email, $password);

    session_start();
    session_regenerate_id(true);
    $_SESSION = ['user_id' => (int)$user['id']];

    JsonResponse::send(200, ['data' => $user]);
}catch(AuthenticationException) {
    JsonResponse::send(401, ['error' => 'Invalid credentials']);
}catch(ValidationException $exception) {
    JsonResponse::send(422, ['error' => $exception->getMessage()]);
}catch(Throwable $exception) {

    error_log((string)$exception);
    JsonResponse::send(500, ['error' => 'Internal server error']);
}
