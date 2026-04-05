<?php
require 'db.php';
require 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='manage-container'><h1>Please log in to view your test drives</h1><a href='login.php' class='btn'>Log In</a></div>"; exit;
}

$user_id = $_SESSION['user_id'];
$test_drives = [];
$error_message = "";

try {
    $stmt = $pdo->prepare("SELECT td.id, td.first_name, td.last_name, td.phone, td.preferred_date, td.notes, td.created_at, c.model_name AS car_name FROM test_drives td JOIN cars c ON td.car_model_id = c.id WHERE td.user_id = :user_id ORDER BY td.preferred_date ASC");
    $stmt->execute([':user_id' => $user_id]);
    $test_drives = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error fetching test drives: " . $e->getMessage();
}
?>

<main>
    <div class="manage-container">
        <div class="manage-header">
            <h1>My Test Drives</h1>
            <a href="book_test_drive.php" class="btn-book-new">Book New</a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="td-alert-success">Test drive deleted successfully.</div>
        <?php endif; ?>
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
            <div class="td-alert-success">Test drive updated successfully.</div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="td-alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (count($test_drives) > 0): ?>
            <div style="overflow-x:auto;">
                <table class="manage-table">
                    <thead>
                        <tr>
                            <th>Date</th><th>Car Model</th><th>Name</th><th>Phone</th><th style="text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($test_drives as $drive): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($drive['preferred_date']); ?></td>
                            <td><strong><?php echo htmlspecialchars($drive['car_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($drive['first_name'] . ' ' . $drive['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($drive['phone']); ?></td>
                            <td style="text-align:center;white-space:nowrap;">
                                <a href="edit_test_drive.php?id=<?php echo $drive['id']; ?>" class="action-edit">Edit</a>
                                <a href="delete_test_drive.php?id=<?php echo $drive['id']; ?>" class="action-cancel" onclick="return confirm('Are you sure you want to cancel this test drive?');">Cancel</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="text-align:center;padding:2rem;color:#6c757d;font-size:1.1rem;">You have not booked any test drives yet.</p>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>