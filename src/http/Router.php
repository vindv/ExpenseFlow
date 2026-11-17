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
     * @param string $method
     * @param string $path
     * @param callable $handler
     * @return void
     */
    private function add(string $method, string $path, callable $handler): void
    {
        $this->routes[$path][$method] = $handler;
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return void
     */
    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return void
     */
    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return void
     */
    public function put(string $path, callable $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return void
     */
    public function delete(string $path, callable $handler): void
    {
        $this->add('DELETE', $path, $handler);
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