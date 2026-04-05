<?php
session_start();
require_once 'db.php';
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
        <h1 class="hero-title">DRIVEN<br><span class="dim">BY</span> DREAMS</h1>
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

<!-- ══ 911 SHOWCASE (pinned scroll) ══ -->
<div class="showcase-outer" id="showcase">
    <div class="showcase-sticky" id="showcaseSticky">

        <div class="scene-label">
            <div class="scene-counter" id="sceneCounter">01</div>
            <div class="scene-name"    id="sceneName">Front View</div>
        </div>

        <div class="scene-title-big">
            <h2 id="sceneTitleH">911 Carrera</h2>
            <p  id="sceneTitleP">Front 3/4 perspective</p>
        </div>

        <div class="car-stage">
            <div class="car-glow"></div>
            <img class="car-img active" id="carFront" src="images/911-front.png" alt="Porsche 911 Front">
            <img class="car-img"        id="carSide"  src="images/911-side.png"  alt="Porsche 911 Side">
            <img class="car-img"        id="carRear"  src="images/911-rear.png"  alt="Porsche 911 Rear">
        </div>

        <div class="stats-panel" id="statsPanel"></div>

        <div class="angle-tag">
            <div class="angle-line"></div>
            <span class="angle-text" id="angleText">Front 3/4</span>
        </div>

        <div class="progress-dots">
            <div class="dot active" id="dot0"></div>
            <div class="dot"        id="dot1"></div>
            <div class="dot"        id="dot2"></div>
        </div>

    </div>
</div>

<!-- ══ FOG REVEAL ══ -->
<div class="fog-outer" id="fogOuter">
    <div class="fog-sticky">

        <div class="fog-tagline">
            <span class="fog-tagline-top" id="fogTagTop">Porsche</span>
        </div>

        <img class="fog-cloud fog-cloud-back-l"  id="fogBL" src="images/cloud.png" alt="">
        <img class="fog-cloud fog-cloud-back-r"  id="fogBR" src="images/cloud.png" alt="">
        <img class="fog-car-img" src="images/top.png" alt="Porsche 911">
        <img class="fog-cloud fog-cloud-front-l" id="fogFL" src="images/cloud.png" alt="">
        <img class="fog-cloud fog-cloud-front-r" id="fogFR" src="images/cloud.png" alt="">

        <div class="fog-ground-glow"></div>

        <div class="page-cta">
            <a href="models.php" class="page-cta-btn">
                View All Models
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <a href="book_test_drive.php" class="page-cta-btn page-cta-ghost">
                Book a Test Drive
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>

    </div>
</div>

<script>
/* ── Scene data ── */
const scenes = [
    {
        img: 'carFront', counter: '01', name: 'Front View',
        title: '911 Carrera', sub: 'Front 3/4 perspective', angle: 'Front 3/4',
        stats: [
            { label: 'Engine',     value: '3.0L', unit: 'Twin-Turbo Flat-6' },
            { label: 'Power',      value: '379',  unit: 'hp' },
            { label: '0 – 60 mph', value: '4.0',  unit: 'seconds' },
            { label: 'Top Speed',  value: '182',  unit: 'mph' },
        ]
    },
    {
        img: 'carSide', counter: '02', name: 'Side Profile',
        title: '911 Carrera', sub: 'Pure side silhouette', angle: 'Side Profile',
        stats: [
            { label: 'Wheelbase', value: '2,450', unit: 'mm' },
            { label: 'Length',    value: '4,519', unit: 'mm' },
            { label: 'Height',    value: '1,300', unit: 'mm' },
            { label: 'Drag Cd',   value: '0.30',  unit: 'coefficient' },
        ]
    },
    {
        img: 'carRear', counter: '03', name: 'Rear View',
        title: '911 Carrera', sub: 'Rear 3/4 perspective', angle: 'Rear 3/4',
        stats: [
            { label: 'Transmission', value: '8-Spd', unit: 'PDK' },
            { label: 'Drive',        value: 'RWD',   unit: 'rear-wheel' },
            { label: 'Weight',       value: '1,515', unit: 'kg' },
            { label: 'Fuel Econ.',   value: '22',    unit: 'mpg avg' },
        ]
    }
];

/* ── DOM refs ── */
const showcaseOuter = document.getElementById('showcase');
const sceneCounter  = document.getElementById('sceneCounter');
const sceneNameEl   = document.getElementById('sceneName');
const titleH        = document.getElementById('sceneTitleH');
const titleP        = document.getElementById('sceneTitleP');
const angleText     = document.getElementById('angleText');
const statsPanel    = document.getElementById('statsPanel');
const dots          = [0,1,2].map(i => document.getElementById('dot' + i));
const carImgs       = {
    carFront: document.getElementById('carFront'),
    carSide:  document.getElementById('carSide'),
    carRear:  document.getElementById('carRear'),
};

let currentScene    = 0;
let isTransitioning = false;

/* ── Render stats ── */
function renderStats(scene) {
    statsPanel.innerHTML = scene.stats.map((s, i) => `
        <div class="stat-block" style="transition-delay:${i * 0.07}s">
            <div class="stat-label">${s.label}</div>
            <div class="stat-value">${s.value}<span class="stat-unit">${s.unit}</span></div>
        </div>`).join('');
}

/* ── Switch scene ── */
function activateScene(idx, direction) {
    if (idx === currentScene) return;

    if (isTransitioning) {
        Object.values(carImgs).forEach(img => {
            img.style.transition = 'none';
            img.style.opacity    = '0';
            img.style.transform  = '';
            img.classList.remove('active');
        });
        isTransitioning = false;
    }

    isTransitioning = true;

    Object.values(carImgs).forEach(img => {
        img.style.transition = 'none';
        img.style.opacity    = '0';
        img.classList.remove('active');
    });

    dots[currentScene].classList.remove('active');
    dots[idx].classList.add('active');
    currentScene = idx;

    const next = scenes[idx];
    sceneCounter.textContent  = next.counter;
    sceneNameEl.textContent   = next.name;
    titleH.textContent        = next.title;
    titleP.textContent        = next.sub;
    angleText.textContent     = next.angle;
    renderStats(next);
    statsPanel.querySelectorAll('.stat-block').forEach(b => b.classList.remove('visible'));

    requestAnimationFrame(() => {
        const nextImg = carImgs[next.img];
        nextImg.style.transition = 'none';
        nextImg.style.opacity    = '0';
        nextImg.style.transform  = `translateX(${direction > 0 ? '40px' : '-40px'}) scale(0.97)`;
        requestAnimationFrame(() => {
            nextImg.style.transition = 'opacity 1.1s cubic-bezier(0.22,1,0.36,1), transform 1.1s cubic-bezier(0.22,1,0.36,1)';
            nextImg.style.opacity    = '1';
            nextImg.style.transform  = 'translateX(0) scale(1)';
            nextImg.classList.add('active');
            setTimeout(() => {
                statsPanel.querySelectorAll('.stat-block')
                    .forEach((b, i) => setTimeout(() => b.classList.add('visible'), i * 120));
                isTransitioning = false;
            }, 350);
        });
    });
}

/* ── Showcase scroll ── */
function onShowcaseScroll() {
    const rect      = showcaseOuter.getBoundingClientRect();
    const scrolled  = Math.max(0, -rect.top);
    const maxScroll = showcaseOuter.offsetHeight - window.innerHeight;
    const progress  = Math.min(1, scrolled / maxScroll);
    const idx       = progress < 0.33 ? 0 : progress < 0.66 ? 1 : 2;
    activateScene(idx, idx - currentScene);
}

/* ── Fog scroll ── */
const fogOuter = document.getElementById('fogOuter');
const fogBL    = document.getElementById('fogBL');
const fogBR    = document.getElementById('fogBR');
const fogFL    = document.getElementById('fogFL');
const fogFR    = document.getElementById('fogFR');
const fogTop   = document.getElementById('fogTagTop');

function onFogScroll() {
    if (window.innerWidth <= 768) return;
    const rect      = fogOuter.getBoundingClientRect();
    const scrolled  = Math.max(0, -rect.top);
    const maxScroll = fogOuter.offsetHeight - window.innerHeight;
    if (maxScroll <= 0) return;
    const p = Math.min(1, scrolled / maxScroll);

    const shiftBack  = p * 120;
    const shiftFront = p * 160;
    fogBL.style.transform = `translateX(calc(-100% - ${shiftBack}vw))`;
    fogBR.style.transform = `translateX(calc(100% + ${shiftBack}vw))`;
    fogFL.style.transform = `translateX(calc(-100% - ${shiftFront}vw))`;
    fogFR.style.transform = `translateX(calc(100% + ${shiftFront}vw))`;

    const cloudOpacity = Math.max(0, 1 - p * 1.4);
    [fogBL, fogBR, fogFL, fogFR].forEach(c => c.style.opacity = cloudOpacity);

    const carAlpha = Math.min(1, Math.max(0, (p - 0.15) / 0.4));
    const fogCarEl = document.querySelector('.fog-car-img');
    if (fogCarEl) {
        fogCarEl.style.opacity   = carAlpha;
        fogCarEl.style.transform = `translate(-50%, -50%) scale(${0.88 + carAlpha * 0.12})`;
    }

    fogTop.style.opacity = Math.min(1, Math.max(0, (p - 0.5) / 0.3));
}

/* ── Hero parallax ── */
function onHeroScroll() {
    const video = document.querySelector('.hero-video');
    const hero  = document.querySelector('.hero');
    if (!video || !hero) return;
    const rect = hero.getBoundingClientRect();
    if (rect.bottom > 0 && rect.top < window.innerHeight) {
        video.style.transform = 'translateY(' + ((window.scrollY - hero.offsetTop) * 0.4) + 'px)';
    }
}

/* ── Init ── */
renderStats(scenes[0]);
setTimeout(() => {
    statsPanel.querySelectorAll('.stat-block')
        .forEach((b, i) => setTimeout(() => b.classList.add('visible'), i * 80));
}, 400);

window.addEventListener('scroll', () => {
    onHeroScroll();
    onShowcaseScroll();
    onFogScroll();
}, { passive: true });
</script>

<?php include 'includes/footer.php'; ?>