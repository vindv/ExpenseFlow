<?php
declare(strict_types=1);

namespace ExpenseFlow\services;

use ExpenseFlow\exceptions\AuthenticationException;
use ExpenseFlow\exceptions\ValidationException;
use ExpenseFlow\repositories\UserRepository;

use PDOException;

class AuthService
{

    /**
     * @param UserRepository $users
     */
    public function __construct(
        private UserRepository $users
    ){}

    /**
     * @param string $name
     * @param string $email
     * @param string $password
     * @return int
     */
    public function register(string $name, string $email, string $password): int
    {
        $name = trim($name);
        $email = mb_strtolower(trim($email));

        if($name === '') {
            throw new ValidationException("Name can't be empty");
        }
        if(mb_strlen($name) > 100) {
            throw new ValidationException("The name cannot exceed 100 characters.");
        }
        if(mb_strlen($email) > 150) {
            throw new ValidationException("The email cannot exceed 100 characters.");
        }
        if(filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new ValidationException("Invalid email");
        }
        if(mb_strlen($password) < 8) {
            throw new ValidationException("Password can't be less than 8 characters");
        }

        if($this->users->findByEmail($email) !== null) {
            throw new ValidationException("Email already exists");
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            return $this->users->create($name, $email, $passwordHash);
        }catch(PDOException $e) {
            if($e->getCode() === '23000') {
                throw new ValidationException("Email already exists");
            }

            throw $e;
        }
    }

    /**
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login(string $email, string $password): array
    {
        $email = mb_strtolower(trim($email));
        $user = $this->users->findByEmail($email);

        if($user === null || !password_verify($password, $user['password_hash'])) {
            throw new AuthenticationException("Invalid credentials");
        }

        unset($user['password_hash']);

        return $user;
    }

}