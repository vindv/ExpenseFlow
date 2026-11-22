<?php
declare(strict_types=1);

namespace ExpenseFlow\http;

use ExpenseFlow\exceptions\AuthenticationException;

class Session
{

    public static function requireUserId(): int
    {
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;

        if(!is_int($userId)) {
            throw new AuthenticationException('Not authenticated.');
        }

        return $userId;
    }

}