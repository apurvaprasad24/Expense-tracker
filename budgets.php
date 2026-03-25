<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

require_once 'config.php';
$conn = getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM budgets ORDER BY category");
    $budgets = [];
    while ($row = $result->fetch_assoc()) $budgets[] = $row;
    echo json_encode(['success' => true, 'data' => $budgets]);

} elseif ($method === 'PUT') {
    $data  = json_decode(file_get_contents('php://input'), true);
    $cat   = trim($data['category'] ?? '');
    $limit = floatval($data['limit'] ?? 0);

    if (!$cat || $limit <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Category and limit required']);
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO budgets (category, budget_limit) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE budget_limit = VALUES(budget_limit)"
        );
        $stmt->bind_param('sd', $cat, $limit);
        $stmt->execute();
        echo json_encode(['success' => true]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

$conn->close();
?>
