<?php
// 1. Start the session (to store user info after login)
session_start();

// 2. Include the database connection
require_once 'db.php';

$error_msg = "";

// 3. Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error_msg = "Please enter both email and password.";
    } else {
        try {
            // 4. Fetch the user by email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            // 5. Verify the password
            // password_verify() takes the plain text input and checks it against the hash
            if ($user && password_verify($password, $user['password'])) {
                // Success! Store user data in the session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];

                // Redirect to the homepage
                header("Location: index.php");
                exit();
            } else {
                $error_msg = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error_msg = "Database error: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<main class="auth-page">
    <div class="auth-container">
        <h2>Welcome Back</h2>
        <p>Log in to your Porsche account.</p>

        <?php if ($error_msg): ?>
            <div class="alert error"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="owner@porsche.com" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-block">Log In</button>
        </form>

        <p class="auth-footer">Don't have an account? <a href="register.php">Sign Up</a></p>
    </div>
</main>


<?php include 'includes/footer.php'; ?>