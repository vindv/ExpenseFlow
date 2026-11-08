<?php
declare(strict_types=1);

namespace ExpenseFlow\database;

use PDO;

class Connection
{

    private PDO $connection;

    public function __construct(array $config)
    {
        $dsn = 'mysql:host=' . $config['hostname'] . ';dbname=' . $config['database'] . ';charset=' . $config['charset'];

        $this->connection = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }

    /**
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

}