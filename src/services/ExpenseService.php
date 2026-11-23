<?php
declare(strict_types=1);

namespace ExpenseFlow\services;

use ExpenseFlow\exceptions\ValidationException;
use ExpenseFlow\repositories\ExpenseRepository;

use DateTimeImmutable;

class ExpenseService
{

    public function __construct(
        private ExpenseRepository $expenseRepository
    ){}


    public function register(int $userId, int $categoryId, string $amount, string $expenseDate, string $description): int
    {
        $description = trim($description);

        if($categoryId <= 0) {
            throw new ValidationException('Category id must be greater than 0');
        }

        if(preg_match('/^\d{1,8}(\.\d{1,2})?$/', $amount) !== 1) {
            throw new ValidationException('The amount must be a number with up to 8 digits and 2 decimal places.');
        }
        if((float) $amount <= 0) {
            throw new ValidationException('The amount must be greater than 0');
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $expenseDate);

        if($date === false || $date->format('Y-m-d') !== $expenseDate) {
            throw new ValidationException('The expense date must be a valid date in YYY-MM-DD format.');
        }
        if($date > new DateTimeImmutable('today')) {
            throw new ValidationException('The expense date cannot be in the future.');
        }

        if($description === ''){
            throw new ValidationException('The expense description cannot be empty.');
        }
        if(mb_strlen($description) > 255) {
            throw new ValidationException('The expense description cannot be longer than 255 characters.');
        }

        try {
            return $this->expenseRepository->create($userId, $categoryId, $amount, $expenseDate, $description);
        }catch(\PDOException $e){
            if($e->getCode() === '23000') {
                throw new ValidationException('The category does not exist.', 0, $e);
            }

            throw $e;
        }
    }


}