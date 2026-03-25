<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config.php';
$conn = getConnection();

// Spending by category (this month)
$catStmt = $conn->query(
    "SELECT category, SUM(amount) as total
     FROM expenses
     WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())
     GROUP BY category ORDER BY total DESC"
);
$byCategory = [];
while ($row = $catStmt->fetch_assoc()) $byCategory[] = $row;

// Daily spending (last 14 days)
$dailyStmt = $conn->query(
    "SELECT DATE(created_at) as date, SUM(amount) as total
     FROM expenses
     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
     GROUP BY DATE(created_at) ORDER BY date ASC"
);
$daily = [];
while ($row = $dailyStmt->fetch_assoc()) $daily[] = $row;

// Total this month
$totalStmt = $conn->query(
    "SELECT SUM(amount) as total, COUNT(*) as count
     FROM expenses
     WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())"
);
$totals = $totalStmt->fetch_assoc();

// Budget status
$budgetStmt = $conn->query(
    "SELECT b.category, b.budget_limit,
        COALESCE(SUM(e.amount), 0) as spent
 FROM budgets b
 LEFT JOIN expenses e ON b.category = e.category
     AND MONTH(e.created_at) = MONTH(NOW())
     AND YEAR(e.created_at) = YEAR(NOW())
 GROUP BY b.category, b.budget_limit
 ORDER BY (COALESCE(SUM(e.amount), 0) / b.budget_limit) DESC"
);
$budgets = [];
while ($row = $budgetStmt->fetch_assoc()) $budgets[] = $row;

echo json_encode([
    'success'     => true,
    'by_category' => $byCategory,
    'daily'       => $daily,
    'totals'      => $totals,
    'budgets'     => $budgets
]);

$conn->close();
?>
