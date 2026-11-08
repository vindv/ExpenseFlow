<?php
declare(strict_types=1);

namespace ExpenseFlow\http;

use ExpenseFlow\exceptions\ValidationException;
use RuntimeException;
use JsonException;

class Request
{

    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? '';
    }

    public function getPath(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        if(!is_string($path)) {
            throw new ValidationException('The request URL is malformed.');
        }

        return $path;
    }

    public function getBody(): array
    {
        $body = @file_get_contents('php://input');

        if($body === false) {
            throw new RuntimeException('Unable to read the request body.');
        }

        try {
            $json = json_decode(
                $body,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }catch(JsonException $exception) {
            throw new ValidationException('The request body contains invalid JSON.', 0, $exception);
        }

        if(!is_array($json)) {
            throw new ValidationException('The JSON request body must be an object or an array.');
        }

        return $json;
    }

}