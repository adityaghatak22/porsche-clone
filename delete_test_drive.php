<?php
require 'db.php';
require 'includes/header.php'; // Required for session start

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $test_drive_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    try {
        // Prepare the DELETE statement
        // Note: verifying user_id ensures users can only delete their own test drives
        $stmt = $pdo->prepare("DELETE FROM test_drives WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            ':id' => $test_drive_id,
            ':user_id' => $user_id
        ]);
        
        // Redirect back to manage page with a success message
        header("Location: manage_test_drives.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        echo "<div class='container' style='padding: 2rem; text-align: center;'>";
        echo "<h2>Error deleting test drive: " . htmlspecialchars($e->getMessage()) . "</h2>";
        echo "<a href='manage_test_drives.php' class='btn btn-primary'>Back to My Test Drives</a>";
        echo "</div>";
    }
} else {
    // If not a GET request or missing ID, redirect back
    header("Location: manage_test_drives.php");
    exit;
}
?>
