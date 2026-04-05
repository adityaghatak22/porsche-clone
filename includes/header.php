<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porsche</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

    <!-- MAIN CSS -->
    <link rel="stylesheet" href="css/style.css">
    
</head>
<body>
    <header class="navbar">
        <div class="logo">
            <a href="index.php">PORSCHE</a>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="models.php">Models</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="profile-dropdown" id="profileDropdown">
                        <div class="user-info" onclick="document.getElementById('profileDropdown').classList.toggle('active')">
                            Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            <div class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                        <div class="dropdown-content">
                            <a href="configurator.php">Configurator</a>
                            <a href="book_test_drive.php">Book Test Drive</a>
                            <a href="manage_test_drives.php">My Test Drives</a>
                            <a href="my_builds.php">My Builds</a>
                            <a href="logout.php">Log Out</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="configurator.php">Configurator</a></li>
                    <li><a href="login.php">Log In</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <script>
        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.closest('.profile-dropdown')) {
                var dropdown = document.getElementById("profileDropdown");
                if (dropdown && dropdown.classList.contains('active')) {
                    dropdown.classList.remove('active');
                }
            }
        }
    </script>
