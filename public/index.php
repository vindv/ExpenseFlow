<?php
declare(strict_types=1);

use ExpenseFlow\database\Connection;
use ExpenseFlow\exceptions\AuthenticationException;
use ExpenseFlow\exceptions\ValidationException;
use ExpenseFlow\repositories\UserRepository;
use ExpenseFlow\services\AuthService;

require __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json; charset=utf-8');

function respondJson(int $status, array $data): never
{
    http_response_code($status);

    $json = json_encode($data, JSON_PRETTY_PRINT);

    echo $json;
    exit;
}

try {

    $config = require __DIR__ . '/../config/database.php';

    $pdo = (new Connection($config))->getConnection();
    $users = new UserRepository($pdo);
    $auth = new AuthService($users);

    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

    if(!is_string($path)) {
        respondJson(404, ['error' => 'Not found']);
    }
    if($method !== 'POST' || !in_array($path, ['/register', '/login'], true)) {
        respondJson(405, ['error' => 'Not found']);
    }

    try {
        $input = json_decode(
            file_get_contents('php://input'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }catch(JsonException $e) {
        respondJson(422, ['error' => 'Invalid JSON body']);
    }

    if(!is_array($input)) {
        respondJson(422, ['error' => 'Invalid JSON body']);
    }

    if($path === '/register') {
        $name = $input['name'] ?? null;
        $email = $input['email'] ?? null;
        $password = $input['password'] ?? null;

        if(!is_string($name) || !is_string($email) || !is_string($password)) {
            throw new ValidationException('Name, email, and password are required.');
        }

        $userId = $auth->register($name, $email, $password);

        respondJson(201, [
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

    respondJson(200, ['data' => $user]);
}catch(AuthenticationException) {
    respondJson(401, ['error' => 'Invalid credentials']);
}catch(ValidationException $exception) {
    respondJson(422, ['error' => $exception->getMessage()]);
}catch(Throwable $exception) {

    error_log((string)$exception);
    respondJson(500, ['error' => 'Internal server error']);
}
