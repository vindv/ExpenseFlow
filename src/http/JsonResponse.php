<?php
declare(strict_types=1);

namespace ExpenseFlow\http;

use JsonException;

class JsonResponse
{

    public static function send(int $status, array $data): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        try {
            $json = json_encode($data, JSON_THROW_ON_ERROR);
        }catch(JsonException ) {
            http_response_code(500);
            $json = '{"error":"Internal server error"}';
        }
        echo $json;

        exit;
    }

}