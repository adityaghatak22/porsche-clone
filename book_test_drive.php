<?php
require 'db.php';
require 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='td-container'><h1>Please log in to book a test drive</h1><a href='login.php' class='btn'>Log In</a></div>";
    exit;
}

$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id       = $_SESSION['user_id'];
    $first_name    = trim($_POST['first_name']);
    $last_name     = trim($_POST['last_name']);
    $phone         = trim($_POST['phone']);
    $car_model_id  = $_POST['car_model_id'];
    $preferred_date = $_POST['preferred_date'];
    $notes         = trim($_POST['notes']);

    if (empty($first_name) || empty($last_name) || empty($phone) || empty($car_model_id) || empty($preferred_date)) {
        $error_message = "Please fill in all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO test_drives (user_id, first_name, last_name, phone, car_model_id, preferred_date, notes) VALUES (:user_id, :first_name, :last_name, :phone, :car_model_id, :preferred_date, :notes)");
            $stmt->execute([':user_id'=>$user_id,':first_name'=>$first_name,':last_name'=>$last_name,':phone'=>$phone,':car_model_id'=>$car_model_id,':preferred_date'=>$preferred_date,':notes'=>$notes]);
            $success_message = "Test drive booked successfully! Our team will contact you shortly.";
        } catch (PDOException $e) {
            $error_message = "Error booking test drive: " . $e->getMessage();
        }
    }
}

$cars = [];
try {
    $stmt = $pdo->query("SELECT id, model_name FROM cars ORDER BY model_name");
    $cars = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Could not fetch car models: " . $e->getMessage();
}
?>

<main>
    <div class="td-container">
        <h1>Book a Test Drive</h1>

        <?php if ($success_message): ?>
            <div class="td-alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <p style="text-align:center;"><a href="manage_test_drives.php" class="btn">View My Bookings</a></p>
        <?php else: ?>
            <?php if ($error_message): ?>
                <div class="td-alert-error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <form method="POST" action="book_test_drive.php" class="td-form">
                <div><label for="first_name">First Name *</label><input type="text" id="first_name" name="first_name" required></div>
                <div><label for="last_name">Last Name *</label><input type="text" id="last_name" name="last_name" required></div>
                <div><label for="phone">Phone Number *</label><input type="tel" id="phone" name="phone" required></div>
                <div>
                    <label for="car_model_id">Select Model *</label>
                    <select id="car_model_id" name="car_model_id" required>
                        <option value="">-- Choose a Car --</option>
                        <?php foreach ($cars as $car): ?>
                            <option value="<?php echo $car['id']; ?>"><?php echo htmlspecialchars($car['model_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div><label for="preferred_date">Preferred Date *</label><input type="date" id="preferred_date" name="preferred_date" required></div>
                <div><label for="notes">Additional Notes</label><textarea id="notes" name="notes" rows="4"></textarea></div>
                <button type="submit" class="td-btn-submit">Submit Booking</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<?php require 'includes/footer.php'; ?>