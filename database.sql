-- Run this in phpMyAdmin or MySQL CLI
CREATE DATABASE IF NOT EXISTS expense_tracker;
USE expense_tracker;

CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    note TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS budgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) UNIQUE NOT NULL,
    budget_limit DECIMAL(10, 2) NOT NULL
);

-- Default budget limits
INSERT INTO budgets (category, budget_limit) VALUES
('Food', 3000.00),
('Transport', 1500.00),
('Entertainment', 2000.00),
('Shopping', 5000.00),
('Health', 2000.00),
('Education', 3000.00),
('Other', 2000.00)
ON DUPLICATE KEY UPDATE budget_limit = VALUES(budget_limit);

-- Sample data
INSERT INTO expenses (title, amount, category, note) VALUES
('Lunch at canteen', 120.00, 'Food', 'Dal rice combo'),
('Bus pass', 500.00, 'Transport', 'Monthly pass'),
('Movie ticket', 250.00, 'Entertainment', 'Spider-Man rerun'),
('Textbook', 450.00, 'Education', 'DSA reference book'),
('Groceries', 800.00, 'Shopping', 'Monthly groceries'),
('Doctor visit', 300.00, 'Health', 'Routine checkup');
