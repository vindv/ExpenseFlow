<?php
declare(strict_types=1);

use ExpenseFlow\database\Connection;
use ExpenseFlow\repositories\UserRepository;
use ExpenseFlow\services\AuthService;
use ExpenseFlow\controllers\AuthController;

use ExpenseFlow\exceptions\{
    MethodNotAllowedException,
    ValidationException,
    NotFoundException
};

use ExpenseFlow\http\{
    JsonResponse,
    Request,
    Router
};

require __DIR__ . '/../vendor/autoload.php';


try {
    $config = require __DIR__ . '/../config/database.php';

    $pdo = (new Connection($config))->getConnection();
    $users = new UserRepository($pdo);
    $auth = new AuthService($users);

    $authController = new AuthController($auth);

    $request = new Request();
    $router = new Router();

    $router->post('/register', [$authController, 'register']);

    $router->post('/login', function (Request $request): void {
        JsonResponse::send(200, ['data' => 'ok']);
    });

    $router->dispatch($request);

}catch(NotFoundException $e) {
    JsonResponse::send(404, [
        'error' => 'The requested resource was not found.'
    ]);
}catch(MethodNotAllowedException $e) {
    JsonResponse::send(405, [
        'error' => 'The requested HTTP method is not allowed for this resource.'
    ]);
}catch(ValidationException $e) {
    JsonResponse::send(422, [
        'error' => $e->getMessage()
    ]);
}catch(Throwable $e) {
    error_log((string)$e);

    JsonResponse::send(500, [
        'error' => 'An unexpected server error occurred.'
    ]);
}

