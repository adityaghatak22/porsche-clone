<?php
// 1. Include the database connection
require_once 'db.php';

$success_msg = "";
$error_msg = "";

// 2. Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 3. Get and sanitize inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 4. Basic Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error_msg = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $error_msg = "Password must be at least 8 characters long.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error_msg = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error_msg = "Password must contain at least one number.";
    } elseif (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $error_msg = "Password must contain at least one special character.";
    } else {
        // 5. Hash the password (CRITICAL for security)
        // Never store plain text passwords!
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            // 6. Prepare the SQL Statement (Prevents SQL Injection)
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            
            // 7. Execute the statement with the user data
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $hashed_password
            ]);

            $success_msg = "Account created! You can now <a href='login.php' style='color:#d5001c;'>login</a>.";
        } catch (PDOException $e) {
            // If the email already exists, code 23000 is triggered
            if ($e->getCode() == 23000) {
                $error_msg = "Email already registered!";
            } else {
                $error_msg = "Database error: " . $e->getMessage();
            }
        }
    }
}

include 'includes/header.php';
?>

<main class="auth-page">
    <div class="auth-container">
        <h2>Join the Club</h2>
        <p>Create your Porsche account.</p>

        <?php if ($success_msg): ?>
            <div class="alert success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="alert error"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="auth-form">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Ferry Porsche" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="owner@porsche.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required 
                       pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}"
                       title="Must contain at least one uppercase letter, one lowercase letter, one number, and one special character, and be at least 8 characters long.">
                <small class="form-text">Must include 8+ characters, uppercase, number, and symbol.</small>
            </div>

            <button type="submit" class="btn btn-block">Create Account</button>
        </form>

        <p class="auth-footer">Already have an account? <a href="login.php">Log In</a></p>
    </div>
</main>


<?php include 'includes/footer.php'; ?>