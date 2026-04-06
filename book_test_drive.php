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
    } elseif (!preg_match('/^\+\d{1,3}\d{6,12}$/', $phone)) {
        $error_message = "Invalid phone number format. Please use international format (e.g., +1234567890).";
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
                <div><label for="phone">Phone Number * (e.g. +4912345678)</label><input type="tel" id="phone" name="phone" placeholder="+CountryCodePhoneNumber" pattern="^\+\d{1,3}\d{6,12}$" title="Please enter a valid phone number with country code (e.g. +4917612345678)" required></div>
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
            <div class="map-container">
                <h3>Our Location</h3>
                <p>Porsche Museum, Porscheplatz 1, 70435 Stuttgart, Germany</p>
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2625.5413346452296!2d9.15049901594921!3d48.835160979285075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4799da945a0b731b%3A0x7d6f5f3e4c4c4c4c!2sPorsche+Museum!5e0!3m2!1sen!2sde!4v1565123456789!5m2!1sen!2sde" 
                    width="100%" 
                    height="350" 
                    style="border:0; border-radius: 4px; margin-top: 1rem;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        <?php endif; ?>
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

            // Sort codes by length descending to match longest prefix first (e.g. +971 before +9)
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
                // If country code not in our list, fall back to basic 6-12 range
                if (numericPart.length < 7 || numericPart.length > 15) {
                    this.setCustomValidity('Please enter a valid international phone number.');
                } else {
                    this.setCustomValidity('');
                }
            }
        });
    </script>
</main>
<?php require 'includes/footer.php'; ?>