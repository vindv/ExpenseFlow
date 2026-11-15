<?php
declare(strict_types=1);

use ExpenseFlow\exceptions\MethodNotAllowedException;
use ExpenseFlow\exceptions\ValidationException;
use ExpenseFlow\exceptions\NotFoundException;

use ExpenseFlow\http\JsonResponse;
use ExpenseFlow\http\Request;
use ExpenseFlow\http\Router;

require __DIR__ . '/../vendor/autoload.php';


try {
    $request = new Request();
    $router = new Router();

    $router->post('/register', function (Request $request): void {
        JsonResponse::send(200, ['data' => 'ok']);
    });

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

