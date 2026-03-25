<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

require_once 'config.php';
$conn = getConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        $category = isset($_GET['category']) ? $_GET['category'] : null;
        $limit    = isset($_GET['limit'])    ? (int)$_GET['limit'] : 50;

        if ($category) {
            $stmt = $conn->prepare(
                "SELECT * FROM expenses WHERE category = ? ORDER BY created_at DESC LIMIT ?"
            );
            $stmt->bind_param('si', $category, $limit);
        } else {
            $stmt = $conn->prepare(
                "SELECT * FROM expenses ORDER BY created_at DESC LIMIT ?"
            );
            $stmt->bind_param('i', $limit);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $expenses = [];
        while ($row = $result->fetch_assoc()) $expenses[] = $row;
        echo json_encode(['success' => true, 'data' => $expenses]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $title    = trim($data['title']    ?? '');
        $amount   = floatval($data['amount']   ?? 0);
        $category = trim($data['category'] ?? '');
        $note     = trim($data['note']     ?? '');

        if (!$title || $amount <= 0 || !$category) {
            http_response_code(400);
            echo json_encode(['error' => 'Title, amount, and category are required.']);
            break;
        }

        $stmt = $conn->prepare(
            "INSERT INTO expenses (title, amount, category, note) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('sdss', $title, $amount, $category, $note);
        $stmt->execute();
        $newId = $conn->insert_id;

        // Check budget alert
        $alert = null;
        $budgetStmt = $conn->prepare("SELECT budget_limit FROM budgets WHERE category = ?");
        $budgetStmt->bind_param('s', $category);
        $budgetStmt->execute();
        $budgetResult = $budgetStmt->get_result()->fetch_assoc();

        if ($budgetResult) {
            $sumStmt = $conn->prepare(
                "SELECT SUM(amount) as total FROM expenses
                 WHERE category = ? AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())"
            );
            $sumStmt->bind_param('s', $category);
            $sumStmt->execute();
            $total = $sumStmt->get_result()->fetch_assoc()['total'];

            if ($total >= $budgetResult['budget_limit']) {
                $alert = "⚠️ Budget exceeded for {$category}! Spent ₹{$total} of ₹{$budgetResult['budget_limit']} limit.";
            } elseif ($total >= $budgetResult['budget_limit'] * 0.8) {
                $alert = "🔔 80% of {$category} budget used. Spent ₹{$total} of ₹{$budgetResult['budget_limit']}.";
            }
        }

        echo json_encode(['success' => true, 'id' => $newId, 'alert' => $alert]);
        break;

    case 'DELETE':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID required']);
            break;
        }
        $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        echo json_encode(['success' => true, 'deleted' => $conn->affected_rows]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

$conn->close();
?>
