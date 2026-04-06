<?php
require 'db.php';
require 'includes/header.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$error_message = "";
$drive = null;

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='td-container'><h1>Invalid Test Drive ID</h1></div>"; exit;
}
$test_drive_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name    = trim($_POST['first_name']);
    $last_name     = trim($_POST['last_name']);
    $phone         = trim($_POST['phone']);
    $car_model_id  = $_POST['car_model_id'];
    $preferred_date = $_POST['preferred_date'];
    $notes         = trim($_POST['notes']);

    if (empty($first_name) || empty($last_name) || empty($phone) || empty($car_model_id) || empty($preferred_date)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!preg_match('/^\+\d{1,3}\d{6,12}$/', $phone)) {
        $error_message = "Invalid phone number format. Please use international format (e.g., +1234567890).";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE test_drives SET first_name=:first_name, last_name=:last_name, phone=:phone, car_model_id=:car_model_id, preferred_date=:preferred_date, notes=:notes WHERE id=:id AND user_id=:user_id");
            $stmt->execute([':first_name'=>$first_name,':last_name'=>$last_name,':phone'=>$phone,':car_model_id'=>$car_model_id,':preferred_date'=>$preferred_date,':notes'=>$notes,':id'=>$test_drive_id,':user_id'=>$user_id]);
            header("Location: manage_test_drives.php?msg=updated"); exit;
        } catch (PDOException $e) {
            $error_message = "Error updating test drive: " . $e->getMessage();
        }
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM test_drives WHERE id=:id AND user_id=:user_id");
    $stmt->execute([':id'=>$test_drive_id,':user_id'=>$user_id]);
    $drive = $stmt->fetch();
    if (!$drive) { echo "<div class='td-container'><h1>Test Drive not found or access denied.</h1></div>"; exit; }
} catch (PDOException $e) { echo "Error: " . $e->getMessage(); exit; }

$cars = [];
try {
    $stmt = $pdo->query("SELECT id, model_name FROM cars ORDER BY model_name");
    $cars = $stmt->fetchAll();
} catch (PDOException $e) { $error_message = "Could not fetch car models: " . $e->getMessage(); }
?>

<main>
    <div class="td-container">
        <div class="td-header">
            <h1>Edit Test Drive</h1>
            <a href="manage_test_drives.php">&larr; Back to List</a>
        </div>

        <?php if ($error_message): ?>
            <div class="td-alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="edit_test_drive.php?id=<?php echo htmlspecialchars($test_drive_id); ?>" class="td-form">
            <div><label for="first_name">First Name *</label><input type="text" id="first_name" name="first_name" required value="<?php echo htmlspecialchars($drive['first_name']); ?>"></div>
            <div><label for="last_name">Last Name *</label><input type="text" id="last_name" name="last_name" required value="<?php echo htmlspecialchars($drive['last_name']); ?>"></div>
            <div><label for="phone">Phone Number * (e.g. +4912345678)</label><input type="tel" id="phone" name="phone" placeholder="+CountryCodePhoneNumber" pattern="^\+\d{1,3}\d{6,12}$" title="Please enter a valid phone number with country code (e.g. +4917612345678)" required value="<?php echo htmlspecialchars($drive['phone']); ?>"></div>
            <div>
                <label for="car_model_id">Select Model *</label>
                <select id="car_model_id" name="car_model_id" required>
                    <option value="">-- Choose a Car --</option>
                    <?php foreach ($cars as $car): ?>
                        <option value="<?php echo $car['id']; ?>" <?php echo ($car['id'] == $drive['car_model_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($car['model_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label for="preferred_date">Preferred Date *</label><input type="date" id="preferred_date" name="preferred_date" required value="<?php echo htmlspecialchars($drive['preferred_date']); ?>"></div>
            <div><label for="notes">Additional Notes</label><textarea id="notes" name="notes" rows="4"><?php echo htmlspecialchars($drive['notes']); ?></textarea></div>
            <button type="submit" class="td-btn-submit">Update Booking</button>
        </form>
    </div>
    <script>
        const phoneInput = document.getElementById('phone');
        const countryCodes = {
            '1': [10],      // USA/Canada
            '44': [10],     // UK
            '91': [10],     // India
            '49': [10, 11], // Germany
            '33': [9],      // France
            '61': [9, 10],  // Australia
            '81': [10],     // Japan
            '971': [9],     // UAE
            '7': [10],      // Russia
            '86': [11]      // China
        };

        phoneInput.addEventListener('input', function() {
            let val = this.value.trim();
            if (!val.startsWith('+')) {
                this.setCustomValidity('Phone number must start with +');
                return;
            }

            let numericPart = val.substring(1);
            let matched = false;
            let expectedLengths = [];

            const sortedCodes = Object.keys(countryCodes).sort((a, b) => b.length - a.length);

            for (let code of sortedCodes) {
                if (numericPart.startsWith(code)) {
                    matched = true;
                    expectedLengths = countryCodes[code];
                    let subscriberPart = numericPart.substring(code.length);
                    if (!expectedLengths.includes(subscriberPart.length)) {
                        this.setCustomValidity(`For country code +${code}, the phone number should have ${expectedLengths.join(' or ')} digits. You entered ${subscriberPart.length}.`);
                    } else {
                        this.setCustomValidity('');
                    }
                    break;
                }
            }

            if (!matched) {
                if (numericPart.length < 8 || numericPart.length > 15) {
                    this.setCustomValidity('Please enter a valid international phone number.');
                } else {
                    this.setCustomValidity('');
                }
            }
        });
    </script>
</main>

<?php include 'includes/footer.php'; ?>