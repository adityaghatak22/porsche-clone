<?php
session_start();
require_once 'db.php';
try {
    $stmt = $pdo->query("SELECT * FROM cars ORDER BY FIELD(model_name, '911', 'Taycan', 'Cayenne', '718 Cayman', 'Panamera', 'Macan')");
    $home_cars = $stmt->fetchAll();
} catch (PDOException $e) {
    $home_cars = [];
}
include 'includes/header.php';
?>



<main>

<!-- ══ HERO ══ -->
<section class="hero" id="top">
    <video autoplay muted loop playsinline class="hero-video">
        <source src="images/hero.mp4" type="video/mp4">
    </video>
    <div class="hero-content">
        <p class="hero-eyebrow">Porsche — Est. 1948</p>
        <h1 class="hero-title">
        DRIVEN<br><span class="dim">BY</span> DREAMS
        </h1>
        <p class="hero-sub">The future of sports cars.</p>
        <div style="display:flex; gap:2rem; align-items:center; flex-wrap:wrap;">
        <a href="models.php" class="hero-btn">
            Explore Models
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="book_test_drive.php" style="display:inline-flex;align-items:center;gap:0.6rem;color:rgba(255,255,255,0.55);font-family:'Barlow Condensed',sans-serif;font-size:0.7rem;letter-spacing:0.3em;text-transform:uppercase;text-decoration:none;transition:color 0.3s;" onmouseover="this.style.color='rgba(255,255,255,0.9)'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">
            Book a Test Drive
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        </div>
    </div>
    <div class="scroll-indicator">
        <span>Scroll</span>
        <div class="scroll-line"></div>
    </div>
</section>

</main>

<!-- ══ PINNED SCROLL SHOWCASE ══ (outside <main> so sticky works) -->
<div class="showcase-outer" id="showcase">
    <div class="showcase-sticky" id="showcaseSticky">

        <!-- Ghost scene counter -->
        <div class="scene-label">
            <div class="scene-counter" id="sceneCounter">01</div>
            <div class="scene-name"   id="sceneName">Front View</div>
        </div>

        <!-- Big faded title -->
        <div class="scene-title-big" id="sceneTitleBig">
            <h2 id="sceneTitleH">911 Carrera</h2>
            <p  id="sceneTitleP">Front 3/4 perspective</p>
        </div>

        <!-- Car images — all stacked, one visible at a time -->
        <div class="car-stage">
            <div class="car-glow"></div>
            <img class="car-img active" id="carFront"
                 src="images/911-front.png"
                 alt="Porsche 911 Front">
            <img class="car-img" id="carSide"
                 src="images/911-side.png"
                 alt="Porsche 911 Side">
            <img class="car-img" id="carRear"
                 src="images/911-rear.png"
                 alt="Porsche 911 Rear">
        </div>

        <!-- Stats panel right -->
        <div class="stats-panel" id="statsPanel">
            <!-- populated by JS per scene -->
        </div>

        <!-- Angle tag bottom-left -->
        <div class="angle-tag">
            <div class="angle-line"></div>
            <span class="angle-text" id="angleText">Front 3/4</span>
        </div>

        <!-- Progress dots -->
        <div class="progress-dots">
            <div class="dot active" id="dot0"></div>
            <div class="dot"       id="dot1"></div>
            <div class="dot"       id="dot2"></div>
        </div>

    </div>
</div>

<main>
<!-- ══ MODELS SECTION INTRO ══ -->
<div class="section-intro" id="models">
    <div class="section-intro-left">
        <p class="section-eyebrow">Our Lineup — 2024</p>
        <h2 class="section-title">The Models</h2>
    </div>
    <p class="section-intro-right">Six ways to experience the soul of a sports car. Each one unmistakably Porsche.</p>
</div>

<!-- ══ 3×2 MODEL GRID ══ -->
<section class="models-grid-home">

    <?php foreach ($home_cars as $car): ?>
    <a href="configurator.php?id=<?php echo $car['id']; ?>" class="model-card-home">
        <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="Porsche <?php echo htmlspecialchars($car['model_name']); ?>">
        <div class="model-overlay-home">
            <div class="overlay-text">
                <h2><?php echo htmlspecialchars($car['model_name']); ?></h2>
                <p><?php echo htmlspecialchars($car['description']); ?></p>
            </div>
            <div class="overlay-arrow"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></div>
        </div>
    </a>
    <?php endforeach; ?>

</section>

<!-- ══ CTA STRIP ══ -->
<div class="cta-strip">
    <span class="cta-strip-label">Configure your Porsche</span>
    <a href="models.php" class="cta-strip-link">
        View All Models
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
</div>

</main>

<!-- ══════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════ -->
<script>
/* ── Scene data ── */
const scenes = [
    {
        id:      'front',
        img:     'carFront',
        counter: '01',
        name:    'Front View',
        title:   '911 Carrera',
        sub:     'Front 3/4 perspective',
        angle:   'Front 3/4',
        stats: [
            { label: 'Engine',      value: '3.0L',   unit: 'Twin-Turbo Flat-6' },
            { label: 'Power',       value: '379',    unit: 'hp' },
            { label: '0 – 60 mph',  value: '4.0',    unit: 'seconds' },
            { label: 'Top Speed',   value: '182',    unit: 'mph' },
        ]
    },
    {
        id:      'side',
        img:     'carSide',
        counter: '02',
        name:    'Side Profile',
        title:   '911 Carrera',
        sub:     'Pure side silhouette',
        angle:   'Side Profile',
        stats: [
            { label: 'Wheelbase',   value: '2,450', unit: 'mm' },
            { label: 'Length',      value: '4,519', unit: 'mm' },
            { label: 'Height',      value: '1,300', unit: 'mm' },
            { label: 'Drag Cd',     value: '0.30',  unit: 'coefficient' },
        ]
    },
    {
        id:      'rear',
        img:     'carRear',
        counter: '03',
        name:    'Rear View',
        title:   '911 Carrera',
        sub:     'Rear 3/4 perspective',
        angle:   'Rear 3/4',
        stats: [
            { label: 'Transmission', value: '8-Spd', unit: 'PDK' },
            { label: 'Drive',        value: 'RWD',   unit: 'rear-wheel' },
            { label: 'Weight',       value: '1,515', unit: 'kg' },
            { label: 'Fuel Econ.',   value: '22',    unit: 'mpg avg' },
        ]
    }
];

/* ── DOM refs ── */
const outer      = document.getElementById('showcase');
const counter    = document.getElementById('sceneCounter');
const sceneName  = document.getElementById('sceneName');
const titleH     = document.getElementById('sceneTitleH');
const titleP     = document.getElementById('sceneTitleP');
const angleText  = document.getElementById('angleText');
const statsPanel = document.getElementById('statsPanel');
const dots       = [
    document.getElementById('dot0'),
    document.getElementById('dot1'),
    document.getElementById('dot2'),
];
const carImgs = {
    carFront: document.getElementById('carFront'),
    carSide:  document.getElementById('carSide'),
    carRear:  document.getElementById('carRear'),
};

let currentScene = 0;

/* ── Build stats HTML ── */
function renderStats(scene) {
    statsPanel.innerHTML = scene.stats.map((s, i) => `
        <div class="stat-block" style="transition-delay:${i * 0.07}s">
            <div class="stat-label">${s.label}</div>
            <div class="stat-value">${s.value}<span class="stat-unit">${s.unit}</span></div>
        </div>
    `).join('');
}

/* ── Switch to a scene index ── */
function activateScene(idx, direction) {
    if (idx === currentScene) return;

    const prev = scenes[currentScene];
    const next = scenes[idx];

    /* Exit current car image */
    const prevImg = carImgs[prev.img];
    prevImg.classList.remove('active');
    prevImg.classList.add(direction > 0 ? 'exit-left' : 'exit-right');

    /* After exit anim, clean up */
    setTimeout(() => {
        prevImg.classList.remove('exit-left', 'exit-right');
    }, 800);

    /* Fade out stats */
    statsPanel.querySelectorAll('.stat-block').forEach(b => b.classList.remove('visible'));

    /* Update text */
    setTimeout(() => {
        counter.textContent   = next.counter;
        sceneName.textContent = next.name;
        titleH.textContent    = next.title;
        titleP.textContent    = next.sub;
        angleText.textContent = next.angle;
        renderStats(next);

        /* Enter new car image — comes from opposite direction */
        const nextImg = carImgs[next.img];
        nextImg.style.transform = `translateX(${direction > 0 ? '50px' : '-50px'}) scale(0.97)`;
        nextImg.style.opacity   = '0';
        nextImg.classList.add('active');

        /* Force reflow then animate in */
        requestAnimationFrame(() => {
            nextImg.style.transition = 'none';
            requestAnimationFrame(() => {
                nextImg.style.transition = '';
                nextImg.style.transform  = '';
                nextImg.style.opacity    = '';
            });
        });

        /* Stagger stats in */
        setTimeout(() => {
            statsPanel.querySelectorAll('.stat-block').forEach((b, i) => {
                setTimeout(() => b.classList.add('visible'), i * 80);
            });
        }, 100);

    }, 150);

    /* Dots */
    dots[currentScene].classList.remove('active');
    dots[idx].classList.add('active');

    currentScene = idx;
}

/* ── Scroll handler ── */
function onScroll() {
    const rect = outer.getBoundingClientRect();

    // how far we've scrolled INTO the section
    const scrolled = Math.max(0, -rect.top);

    const maxScroll = rect.height - window.innerHeight;

    const progress = Math.min(1, scrolled / maxScroll);

    let sceneIdx = 0;

    if (progress < 0.33) {
        sceneIdx = 0;
    } else if (progress < 0.66) {
        sceneIdx = 1;
    } else {
        sceneIdx = 2;
    }

    activateScene(sceneIdx, sceneIdx - currentScene);
}

/* ── Init ── */
renderStats(scenes[0]);
setTimeout(() => {
    statsPanel.querySelectorAll('.stat-block').forEach((b, i) => {
        setTimeout(() => b.classList.add('visible'), i * 80);
    });
}, 400);

window.addEventListener('scroll', onScroll, { passive: true });

/* ── Hero parallax ── */
window.addEventListener("scroll", function () {
    const video = document.querySelector(".hero-video");
    const hero = document.querySelector(".hero");

    if (!video || !hero) return;

    const rect = hero.getBoundingClientRect();

    // Only apply parallax while hero is visible
    if (rect.bottom > 0 && rect.top < window.innerHeight) {
        const offset = window.scrollY - hero.offsetTop;
        video.style.transform = "translateY(" + (offset * 0.4) + "px)";
    }
});

/* ── Model card scroll reveal ── */
const cards = document.querySelectorAll(".model-card-home");
const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const idx = [...cards].indexOf(entry.target);
            setTimeout(() => entry.target.classList.add("show"), idx * 80);
            io.unobserve(entry.target);
        }
    });
}, { threshold: 0.12 });
cards.forEach(c => io.observe(c));
</script>

<?php include 'includes/footer.php'; ?>