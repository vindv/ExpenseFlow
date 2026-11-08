<?php
declare(strict_types=1);

namespace ExpenseFlow\repositories;

use PDO;

class UserRepository
{

    /**
     * @param PDO $connection
     */
    public function __construct(private PDO $connection){}

    /**
     * @param string $name
     * @param string $email
     * @param string $passwordHash
     * @return int
     */
    public function create(string $name, string $email, string $passwordHash): int
    {
        $sql = 'INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)';

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => $passwordHash,
            'role' => 'employee'
        ]);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT * FROM users WHERE email = :email LIMIT 1';

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch();

        return $user === false ? null : $user;
    }
}