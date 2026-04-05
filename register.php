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
                <input type="text" id="name" name="name" placeholder="Ferry Porsche" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="owner@porsche.com" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-block">Create Account</button>
        </form>

        <p class="auth-footer">Already have an account? <a href="login.php">Log In</a></p>
    </div>
</main>


<?php include 'includes/footer.php'; ?>