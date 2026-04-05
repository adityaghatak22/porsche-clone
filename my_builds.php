<?php
session_start();
require_once 'db.php';

// If the user is not logged in, redirect them to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle DELETE action
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    // Only delete builds that belong to this user (security!)
    $stmt = $pdo->prepare("DELETE FROM configurations WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $delete_id, 'user_id' => $_SESSION['user_id']]);
    header("Location: my_builds.php");
    exit();
}

try {
    // JOIN: This is a powerful SQL concept. We combine data from 3 tables in one query.
    // configurations has car_id -> we JOIN it with cars to get the car's name and image.
    $stmt = $pdo->prepare("
        SELECT 
            configurations.id AS config_id,
            configurations.color_name,
            configurations.color_hue,
            configurations.created_at,
            cars.model_name,
            cars.image_url,
            cars.price
        FROM configurations
        JOIN cars ON configurations.car_id = cars.id
        WHERE configurations.user_id = :user_id
        ORDER BY configurations.created_at DESC
    ");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $builds = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching builds: " . $e->getMessage());
}

include 'includes/header.php';
?>

<main class="builds-page">
    <div class="builds-header">
        <h2>My Builds</h2>
        <p>Your saved configurations, <?php echo htmlspecialchars($_SESSION['user_name']); ?>.</p>
    </div>

    <?php if (count($builds) === 0): ?>
        <div class="empty-state">
            <h3>No saved builds yet</h3>
            <p>Head to the <a href="models.php" style="color:#d5001c;">Models</a> page and configure your dream car!</p>
        </div>
    <?php else: ?>
        <div class="builds-grid">
            <?php foreach ($builds as $build): ?>
                <div class="build-card">
                    <div class="build-image-wrapper">
                        <?php
                        $folder = strtolower(str_replace(' ', '-', trim($build['model_name'])));
                        $colorFile = strtolower($build['color_hue']); // color_hue now stores file name e.g. 'white','red'
                        $imgSrc = "images/{$folder}/{$colorFile}.jpg";
                        ?>
                        <img src="<?php echo htmlspecialchars($imgSrc); ?>"
                             alt="<?php echo htmlspecialchars($build['model_name']); ?>"
                             onerror="this.src='<?php echo htmlspecialchars($build['image_url']); ?>'">
                    </div>
                    <div class="build-details">
                        <h3><?php echo htmlspecialchars($build['model_name']); ?></h3>
                        <div class="build-spec">
                            <span class="spec-label">Exterior</span>
                            <span class="spec-value">
                                <?php
                                $colorMap = ['white'=>'#e8e8e8','black'=>'#111','red'=>'#990033','blue'=>'#003366','green'=>'#004d00','yellow'=>'#ccaa00'];
                                $swatchColor = isset($colorMap[$colorFile]) ? $colorMap[$colorFile] : '#888';
                                ?>
                                <span class="mini-swatch" style="background:<?php echo $swatchColor; ?>;"></span>
                                <?php echo htmlspecialchars(ucfirst($build['color_name'])); ?>
                            </span>
                        </div>
                        <div class="build-spec">
                            <span class="spec-label">Base Price</span>
                            <span class="spec-value">$<?php echo number_format($build['price'], 2); ?></span>
                        </div>
                        <div class="build-spec">
                            <span class="spec-label">Saved On</span>
                            <span class="spec-value"><?php echo date('M d, Y', strtotime($build['created_at'])); ?></span>
                        </div>
                        <a href="my_builds.php?delete=<?php echo $build['config_id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this build?')">Delete Build</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>


<?php include 'includes/footer.php'; ?>