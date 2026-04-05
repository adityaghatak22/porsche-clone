<?php
session_start();
require_once 'db.php';

$car_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$car_id) {
    header("Location: models.php");
    exit();
}

$save_msg = "";
$save_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $save_msg = "You must <a href='login.php' style='color:#d5001c;'>log in</a> to save a build.";
        $save_type = "error";
    } else {
        $color_name = trim($_POST['color_name']);
        $color_hue  = trim($_POST['color_hue']);
        $user_id    = $_SESSION['user_id'];

        try {
            $stmt = $pdo->prepare("INSERT INTO configurations (user_id, car_id, color_name, color_hue) VALUES (:user_id, :car_id, :color_name, :color_hue)");
            $stmt->execute(['user_id' => $user_id, 'car_id' => $car_id, 'color_name' => $color_name, 'color_hue' => $color_hue]);
            $save_msg  = "Build saved! View it in <a href='my_builds.php' style='color:#d5001c;'>My Builds</a>.";
            $save_type = "success";
        } catch (PDOException $e) {
            $save_msg  = "Error saving build: " . $e->getMessage();
            $save_type = "error";
        }
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = :id");
    $stmt->execute(['id' => $car_id]);
    $car = $stmt->fetch();
    if (!$car) die("Car not found.");
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

include 'includes/header.php';
?>

<!-- ════════════════════════════════════════════
     MARKUP
════════════════════════════════════════════ -->
<main class="cfg-page">

    <!-- ── LEFT: Stage / Visualizer ── -->
    <div class="cfg-stage">
        <span class="stage-label">Exterior Preview</span>

        <div class="car-image-wrap">
            <?php
            $folder = strtolower(str_replace(' ', '-', trim($car['model_name'])));
            $base_img = "images/{$folder}/white.jpg";
            ?>
            <img id="carImage"
                 src="<?php echo htmlspecialchars($base_img); ?>"
                 alt="<?php echo htmlspecialchars($car['model_name']); ?> — White">
        </div>

        <div class="stage-color-tag">
            <span class="stage-color-dot" id="stageDot"></span>
            <span class="stage-color-name" id="stageColorName">White</span>
        </div>
    </div>

    <!-- ── RIGHT: Control Panel ── -->
    <aside class="cfg-panel">
        <div class="panel-accent"></div>

        <div class="panel-inner">

            <!-- Car identity -->
            <div class="panel-hero">
                <p class="panel-eyebrow">Build &amp; Configure</p>
                <h1 class="panel-car-name"><?php echo htmlspecialchars($car['model_name']); ?></h1>
                <div class="panel-price-row">
                    <span class="price-label">Base MSRP</span>
                    <span class="price-value">$<?php echo number_format($car['price'], 2); ?></span>
                </div>
            </div>

            <!-- Alert -->
            <?php if ($save_msg): ?>
                <div class="cfg-alert <?php echo $save_type; ?>"><?php echo $save_msg; ?></div>
            <?php endif; ?>

            <!-- Color selection -->
            <div class="cfg-section">
                <p class="cfg-section-title">Exterior Color</p>

                <div class="swatch-strip">
                    <div class="swatch-row">
                        <?php
                        $colors = [
                            ['file' => 'white',  'name' => 'White',  'bg' => '#e8e8e8'],
                            ['file' => 'black',  'name' => 'Black',  'bg' => '#111111'],
                            ['file' => 'red',    'name' => 'Red',    'bg' => '#990033'],
                            ['file' => 'blue',   'name' => 'Blue',   'bg' => '#003366'],
                            ['file' => 'green',  'name' => 'Green',  'bg' => '#004d00'],
                            ['file' => 'yellow', 'name' => 'Yellow', 'bg' => '#ccaa00'],
                        ];
                        foreach ($colors as $c):
                        ?>
                        <button class="cfg-swatch <?php echo $c['file'] === 'white' ? 'active' : ''; ?>"
                                data-file="<?php echo $c['file']; ?>"
                                data-name="<?php echo htmlspecialchars($c['name']); ?>"
                                data-bg="<?php echo $c['bg']; ?>"
                                style="background:<?php echo $c['bg']; ?>;"
                                title="<?php echo htmlspecialchars($c['name']); ?>">
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Static specs -->
            <div class="cfg-section">
                <p class="cfg-section-title">Specifications</p>
                <div class="spec-list">
                    <div class="spec-row">
                        <span class="spec-key">Drivetrain</span>
                        <span class="spec-val">All-Wheel Drive</span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-key">Transmission</span>
                        <span class="spec-val">8-Speed PDK</span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-key">Warranty</span>
                        <span class="spec-val">4 yr / 50,000 mi</span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-key">Delivery</span>
                        <span class="spec-val">10 – 14 Weeks</span>
                    </div>
                </div>
            </div>

            <!-- Save CTA -->
            <div class="panel-footer">
                <form method="POST" action="configurator.php?id=<?php echo $car_id; ?>" class="cfg-save-form" id="saveForm">
                    <input type="hidden" name="color_name" id="colorNameInput" value="White">
                    <input type="hidden" name="color_hue"  id="colorHueInput"  value="0">
                    <button type="submit" class="btn-save-cfg">Save This Build</button>
                </form>
                <a href="models.php" class="btn-back-cfg">← Back to Models</a>
            </div>

        </div>
    </aside>
</main>

<!-- ════════════════════════════════════════════
     JAVASCRIPT
════════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const swatches       = document.querySelectorAll('.cfg-swatch');
    const carImage       = document.getElementById('carImage');
    const stageDot       = document.getElementById('stageDot');
    const stageColorName = document.getElementById('stageColorName');
    const colorNameInput = document.getElementById('colorNameInput');
    const colorHueInput  = document.getElementById('colorHueInput');

    // Folder is derived from car model name — must match PHP logic
    const folder = <?php echo json_encode($folder); ?>;

    swatches.forEach(swatch => {
        swatch.addEventListener('click', () => {
            swatches.forEach(s => s.classList.remove('active'));
            swatch.classList.add('active');

            const file = swatch.dataset.file;
            const name = swatch.dataset.name;
            const bg   = swatch.dataset.bg;

            // Crossfade: shrink slightly, swap src, grow back
            carImage.classList.add('changing');
            setTimeout(() => {
                image.src = `/porsche-clone-php/images/${folder}/${color}.jpg`;
                carImage.alt = carImage.alt.replace(/— \w+$/, `— ${name}`);
                // Reset filter (no more hue-rotate)
                carImage.style.filter = 'drop-shadow(0 20px 60px rgba(0,0,0,0.8))';
                carImage.classList.remove('changing');
            }, 200);

            // Update stage label + dot
            stageColorName.textContent = name;
            stageDot.style.background  = bg;

            // Update form inputs
            colorNameInput.value = name;
            colorHueInput.value  = file; // store file name as "hue" for DB compatibility
        });
    });

    // Set initial dot colour to white swatch bg
    stageDot.style.background = '#e8e8e8';
});
</script>

<?php include 'includes/footer.php'; ?>