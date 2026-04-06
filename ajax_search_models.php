<?php
// ajax_search_models.php
// This file acts as an API endpoint. It fetches cars matching a query and returns them as JSON.
require_once 'db.php';

// Ensure the response is treated as JSON
header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$price_range = isset($_GET['p']) ? $_GET['p'] : 'all';

try {
    $sql = "SELECT * FROM cars WHERE 1=1";
    $params = [];

    if (!empty($query)) {
        $sql .= " AND (model_name LIKE :q OR description LIKE :q)";
        $params[':q'] = '%' . $query . '%';
    }

    if ($price_range === 'under80') {
        $sql .= " AND price < 80000";
    } elseif ($price_range === '80to120') {
        $sql .= " AND price >= 80000 AND price <= 120000";
    } elseif ($price_range === 'over120') {
        $sql .= " AND price > 120000";
    }

    $sql .= " ORDER BY price ASC"; // Useful to sort by price when filtering by it
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the result as a JSON string
    echo json_encode($cars);

} catch (PDOException $e) {
    // In an API, return a 500 status code and a JSON error message if things fail
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
