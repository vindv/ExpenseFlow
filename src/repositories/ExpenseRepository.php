<?php
declare(strict_types=1);

namespace ExpenseFlow\repositories;

use PDO;

class ExpenseRepository
{

    /**
     * @param PDO $connection
     */
    public function __construct(private PDO $connection){}

    public function create(int $userId, int $categoryId, string $amount, string $expenseDate, string $description): int
    {
        $sql = 'INSERT INTO expenses (user_id, category_id, amount, expense_date, description) VALUES (:user_id, :category_id, :amount, :expense_date, :description)';

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            'user_id' => $userId,
            'category_id' => $categoryId,
            'amount' => $amount,
            'expense_date' => $expenseDate,
            'description' => $description
        ]);

        return (int) $this->connection->lastInsertId();
    }

}