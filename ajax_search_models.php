<?php
// ajax_search_models.php
// This file acts as an API endpoint. It fetches cars matching a query and returns them as JSON.
require_once 'db.php';

// Ensure the response is treated as JSON
header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    if (empty($query)) {
        // If query is empty, simply return all cars
        $stmt = $pdo->query("SELECT * FROM cars ORDER BY model_name");
        $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Use prepared statements to prevent SQL injection!
        $sql = "SELECT * FROM cars WHERE model_name LIKE :q OR description LIKE :q ORDER BY model_name";
        $stmt = $pdo->prepare($sql);
        
        // Add wildcards around the search term for partial matching
        $searchTerm = '%' . $query . '%';
        $stmt->execute([':q' => $searchTerm]);
        $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Return the result as a JSON string
    echo json_encode($cars);

} catch (PDOException $e) {
    // In an API, return a 500 status code and a JSON error message if things fail
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
