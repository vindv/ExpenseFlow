<?php
declare(strict_types=1);

namespace ExpenseFlow\http;

use ExpenseFlow\exceptions\MethodNotAllowedException;
use ExpenseFlow\exceptions\NotFoundException;

class Router
{

    /** @var array */
    private array $routes = [];

    /**
     * @param string $path
     * @param callable $handler
     * @return void
     */
    public function post(string $path, callable $handler): void
    {
        $this->routes[$path]['POST'] = $handler;
    }

    /**
     * @param Request $request
     * @return void
     */
    public function dispatch(Request $request): void
    {
        $path = $request->getPath();
        $method = $request->getMethod();

        if(!isset($this->routes[$path])) {
            throw new NotFoundException('No route is registered for the requested path.');
        }
        if(!isset($this->routes[$path][$method])) {
            throw new MethodNotAllowedException('The requested HTTP method is not supported for this route.');
        }

        $handler = $this->routes[$path][$method];
        $handler($request);
    }

}