<?php
session_start();
require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT * FROM cars ORDER BY FIELD(model_name, '911', 'Taycan', 'Cayenne', '718 Cayman', 'Panamera', 'Macan')");
    $cars = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching cars: " . $e->getMessage());
}

include 'includes/header.php';
?>


<main class="models-page">

    <!-- ── Masthead ── -->
    <div class="models-masthead">
        <div class="masthead-left">
            <p class="masthead-eyebrow">Porsche Lineup — 2024</p>
            <h1 class="masthead-title">Models</h1>
        </div>
        <div class="masthead-right">
            <p class="masthead-tagline">Choose your electrified soul or your track weapon.</p>
            <div class="search-wrap">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" id="car-search" placeholder="Search models…">
            </div>
        </div>
    </div>

    <!-- ── Grid ── -->
    <div class="models-grid" id="models-grid">
        <?php foreach ($cars as $i => $car): ?>
            <div class="car-card">
                <div class="card-image-wrap">
                    <span class="card-number"><?php echo str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?></span>
                    <img src="<?php echo htmlspecialchars($car['image_url']); ?>"
                         alt="<?php echo htmlspecialchars($car['model_name']); ?>"
                         loading="lazy">
                </div>
                <div class="card-body">
                    <h2 class="card-model-name"><?php echo htmlspecialchars($car['model_name']); ?></h2>
                    <p class="card-description"><?php echo htmlspecialchars($car['description']); ?></p>
                    <div class="card-footer">
                        <div class="card-price">
                            <span>From</span>
                            <strong>$<?php echo number_format($car['price'], 2); ?></strong>
                        </div>
                        <a href="configurator.php?id=<?php echo $car['id']; ?>" class="card-btn">
                            Configure
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('car-search');
    const grid = document.getElementById('models-grid');
    let timeout = null;

    searchInput.addEventListener('input', function () {
        if (timeout) clearTimeout(timeout);
        timeout = setTimeout(() => {
            const query = this.value.trim();
            fetch('ajax_search_models.php?q=' + encodeURIComponent(query))
                .then(r => r.json())
                .then(cars => {
                    grid.innerHTML = '';
                    if (cars.length === 0) {
                        grid.innerHTML = '<p class="no-results">No models found.</p>';
                        return;
                    }
                    cars.forEach((car, i) => {
                        const price = parseFloat(car.price).toLocaleString('en-US', { minimumFractionDigits: 2 });
                        const num   = String(i + 1).padStart(2, '0');
                        const card  = document.createElement('div');
                        card.className = 'car-card';
                        card.innerHTML = `
                            <div class="card-image-wrap">
                                <span class="card-number">${num}</span>
                                <img src="${car.image_url}" alt="${car.model_name}" loading="lazy">
                            </div>
                            <div class="card-body">
                                <h2 class="card-model-name">${car.model_name}</h2>
                                <p class="card-description">${car.description || ''}</p>
                                <div class="card-footer">
                                    <div class="card-price">
                                        <span>From</span>
                                        <strong>$${price}</strong>
                                    </div>
                                    <a href="configurator.php?id=${car.id}" class="card-btn">
                                        Configure
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                                            <path d="M5 12h14M12 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>`;
                        grid.appendChild(card);
                    });
                })
                .catch(() => {
                    grid.innerHTML = '<p class="no-results">Error loading results.</p>';
                });
        }, 300);
    });
});
</script>

<?php include 'includes/footer.php'; ?>