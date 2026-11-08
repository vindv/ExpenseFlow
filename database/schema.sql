CREATE DATABASE IF NOT EXISTS expenseflow
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE expenseflow;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('employee', 'supervisor', 'finance') NOT NULL DEFAULT 'employee',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS expenses (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    expense_date DATE NOT NULL,
    description VARCHAR(255) NOT NULL,
    status ENUM('draft', 'submitted', 'approved', 'rejected', 'reimbursed')
           NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_expenses_user
        FOREIGN KEY (user_id) REFERENCES users (id),
    CONSTRAINT fk_expenses_category
        FOREIGN KEY (category_id) REFERENCES categories (id),
    CONSTRAINT chk_expenses_amount CHECK (amount > 0)
) ENGINE=InnoDB;

INSERT IGNORE INTO categories (name) VALUES
       ('Transporte'),
       ('Hospedaje'),
       ('Alimentacion'),
       ('Otros');

