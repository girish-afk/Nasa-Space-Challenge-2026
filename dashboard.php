<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';


$pageTitle = 'Mission Dashboard';
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'dashboard.php');

// NASA reference values are published planetary facts, not live mission telemetry.
$marsFacts = [
    ['label' => 'Mean radius', 'value' => '3,390', 'unit' => 'km', 'note' => 'NASA planetary reference', 'icon' => 'bi-circle', 'tone' => 'cyan'],
    ['label' => 'Surface gravity', 'value' => '3.71', 'unit' => 'm/s²', 'note' => 'Approx. 0.38 × Earth', 'icon' => 'bi-speedometer2', 'tone' => 'orange'],
    ['label' => 'Length of day', 'value' => '24.6', 'unit' => 'hours', 'note' => 'One Martian sol', 'icon' => 'bi-clock', 'tone' => 'cyan'],
    ['label' => 'Length of year', 'value' => '687', 'unit' => 'Earth days', 'note' => 'One Martian orbit', 'icon' => 'bi-calendar3', 'tone' => 'orange'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<script>
(function () {
    try {
        var p = JSON.parse(localStorage.getItem('projectAstro.settings.v1') || '{}');
        var theme = p.theme === 'light' ? 'light' : 'dark';
        var accent = p.accent === 'cyan' ? 'cyan' : 'orange';
        var root = document.documentElement;
        root.dataset.astroTheme = theme;
        root.dataset.astroAccent = accent;
        root.style.colorScheme = theme;
        root.style.setProperty('--accent', accent === 'cyan' ? '#50C8E8' : '#E66A45');
        root.style.setProperty('--accent-hover', accent === 'cyan' ? '#79D9F0' : '#F18460');
    } catch (e) {}
})();
</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PROJECT ASTRO mission dashboard — explore verified Mars reference data and plan planetary exploration.">
    <title>PROJECT ASTRO | Mission Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
</head>
<body>
<div class="app-layout">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include __DIR__ . '/includes/navbar.php'; ?>

        <div class="dashboard-content astro-dashboard">
            <section class="welcome-section dashboard-welcome">
                <div>
                    <div class="eyebrow"><span class="status-dot"></span> MISSION CONTROL CENTER</div>
                    <h1>Mission Overview</h1>
                    <p>Explore Mars. Understand the environment. Prepare the next frontier.</p>
                </div>
                <div class="welcome-date dashboard-clock-card">
                    <span>LOCAL MISSION CONSOLE TIME</span>
                    <strong id="missionClock" class="mission-clock" aria-live="off">--:--:--</strong>
                    <small id="missionDate">Synchronizing local clock…</small>
                </div>
            </section>

            <section class="dashboard-status-strip" aria-label="Dashboard data status">
                <div><span class="status-indicator"></span><span>LOCAL INTERFACE READY</span></div>
                <span class="status-separator"></span>
                <div><i class="bi bi-database cyan"></i><span>NASA REFERENCE DATASET</span></div>
                <span class="status-separator"></span>
                <div><i class="bi bi-info-circle"></i><span>NOT LIVE ROVER TELEMETRY</span></div>
            </section>

            <section class="stats-grid dashboard-facts" aria-label="Mars planetary facts">
                <?php foreach ($marsFacts as $fact): ?>
                    <article class="stat-card">
                        <div class="stat-top">
                            <span><?= htmlspecialchars($fact['label'], ENT_QUOTES, 'UTF-8') ?></span>
                            <i class="bi <?= htmlspecialchars($fact['icon'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($fact['tone'], ENT_QUOTES, 'UTF-8') ?>"></i>
                        </div>
                        <h2><?= htmlspecialchars($fact['value'], ENT_QUOTES, 'UTF-8') ?> <small><?= htmlspecialchars($fact['unit'], ENT_QUOTES, 'UTF-8') ?></small></h2>
                        <p><?= htmlspecialchars($fact['note'], ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="stat-footer">NASA PLANETARY FACT SHEET</div>
                    </article>
                <?php endforeach; ?>
            </section>

            <section class="dashboard-feature-grid">
                <article class="panel dashboard-hero-panel">
                    <div class="panel-heading">
                        <div>
                            <span class="panel-label">PLANETARY EXPLORATION</span>
                            <h3>Explore the Red Planet</h3>
                        </div>
                        <span class="live-tag">MAP MODULE</span>
                    </div>
                    <div class="dashboard-mars-visual">
                        <img src="https://images-assets.nasa.gov/image/PIA26632/PIA26632~orig.jpg"
                             alt="Mars surface photographed by a NASA rover"
                             loading="lazy"
                             onerror="this.style.display='none'; this.parentElement.classList.add('image-unavailable');">
                        <div class="dashboard-mars-overlay">
                            <span>INTERPLANETARY SURVIVAL GUIDE</span>
                            <h2>Navigate an<br>unfamiliar world.</h2>
                            <p>Open the planetary map to inspect the Martian surface and map controls.</p>
                            <a href="explorer.php" class="btn-primary-custom">Open Mars Explorer <i class="bi bi-arrow-up-right"></i></a>
                        </div>
                        <div class="image-credit">NASA IMAGE · PIA26632</div>
                    </div>
                </article>

                <article class="panel dashboard-objectives-panel">
                    <div class="panel-heading">
                        <div>
                            <span class="panel-label">PROJECT ROADMAP</span>
                            <h3>Mission Objectives</h3>
                        </div>
                        <i class="bi bi-crosshair orange"></i>
                    </div>
                    <div class="objective-item">
                        <div class="objective-icon"><i class="bi bi-map"></i></div>
                        <div class="objective-copy"><strong>Terrain exploration</strong><p>Inspect surface regions and map context</p></div>
                        <span class="objective-status">IN PROGRESS</span>
                    </div>
                    <div class="objective-item">
                        <div class="objective-icon"><i class="bi bi-bullseye"></i></div>
                        <div class="objective-copy"><strong>Landing site analysis</strong><p>Assess terrain suitability and constraints</p></div>
                        <span class="objective-status">PLANNED</span>
                    </div>
                    <div class="objective-item">
                        <div class="objective-icon"><i class="bi bi-shield-exclamation"></i></div>
                        <div class="objective-copy"><strong>Hazard assessment</strong><p>Identify operational surface risks</p></div>
                        <span class="objective-status">PLANNED</span>
                    </div>
                    <div class="objective-item">
                        <div class="objective-icon"><i class="bi bi-signpost-split"></i></div>
                        <div class="objective-copy"><strong>Mission route planning</strong><p>Build a rover traverse and mission plan</p></div>
                        <span class="objective-status">PLANNED</span>
                    </div>
                    <p class="dashboard-note"><i class="bi bi-info-circle"></i> Status labels describe this project's development roadmap, not NASA mission status.</p>
                </article>
            </section>

            <section class="dashboard-analytics-grid" aria-label="Mars scientific reference charts">
                <article class="panel analytics-panel">
                    <div class="panel-heading">
                        <div>
                            <span class="panel-label">ATMOSPHERIC SCIENCE</span>
                            <h3>Martian Atmosphere Composition</h3>
                        </div>
                        <span class="chart-unit">% BY VOLUME</span>
                    </div>
                    <div class="chart-canvas-wrap chart-doughnut-wrap">
                        <canvas id="atmosphereChart" role="img" aria-label="Doughnut chart of major Martian atmospheric gases by volume"></canvas>
                    </div>
                    <div class="chart-footnote">Major gases shown. Trace constituents are omitted from this chart; values are approximate reference percentages.</div>
                </article>

                <article class="panel analytics-panel">
                    <div class="panel-heading">
                        <div>
                            <span class="panel-label">PLANETARY COMPARISON</span>
                            <h3>Time on Mars vs Earth</h3>
                        </div>
                        <span class="chart-unit">DURATION</span>
                    </div>
                    <div class="chart-canvas-wrap chart-bar-wrap">
                        <canvas id="orbitalChart" role="img" aria-label="Bar chart comparing day length and year length on Mars and Earth"></canvas>
                    </div>
                    <div class="chart-footnote">A Martian day is about 24.6 hours; a Martian year is about 687 Earth days.</div>
                </article>
            </section>

            <section class="quick-access-section dashboard-modules-section">
                <div class="section-heading">
                    <div><span class="panel-label">MISSION OPERATIONS</span><h3>Exploration Modules</h3></div>
                    <span class="muted-text">MODULE AVAILABILITY</span>
                </div>
                <div class="modules-grid dashboard-modules-grid">
                    <a href="explorer.php" class="module-card">
                        <div class="module-icon orange-bg"><i class="bi bi-globe"></i></div>
                        <h4>Martian Explorer</h4>
                        <p>Explore the planetary map interface and inspect available map controls.</p>
                        <span>OPEN MODULE <i class="bi bi-arrow-up-right"></i></span>
                    </a>
                    <article class="module-card module-card-planned" aria-label="Landing Zone Analyzer planned module">
                        <div class="module-icon cyan-bg"><i class="bi bi-bullseye"></i></div>
                        <h4>Landing Zone Analyzer</h4>
                        <p>Evaluate terrain properties and potential landing locations.</p>
                        <span><i class="bi bi-lock"></i> PLANNED MODULE</span>
                    </article>
                    <article class="module-card module-card-planned" aria-label="Hazard Detection planned module">
                        <div class="module-icon warning-bg"><i class="bi bi-shield-exclamation"></i></div>
                        <h4>Hazard Detection</h4>
                        <p>Investigate terrain hazards and operational constraints.</p>
                        <span><i class="bi bi-lock"></i> PLANNED MODULE</span>
                    </article>
                    <article class="module-card module-card-planned" aria-label="Water and Resources planned module">
                        <div class="module-icon green-bg"><i class="bi bi-droplet"></i></div>
                        <h4>Water &amp; Resources</h4>
                        <p>Review scientific evidence for water ice and other resources.</p>
                        <span><i class="bi bi-lock"></i> PLANNED MODULE</span>
                    </article>
                </div>
            </section>

            <section class="panel data-panel dashboard-sources-panel">
                <div class="panel-heading">
                    <div><span class="panel-label">SCIENTIFIC INFORMATION</span><h3>Research &amp; Data Sources</h3></div>
                    <i class="bi bi-database cyan"></i>
                </div>
                <div class="data-source">
                    <div class="source-icon"><i class="bi bi-globe2"></i></div>
                    <div><strong>NASA Mars Facts</strong><p>Planetary size, orbit, rotation, surface and atmosphere overview.</p></div>
                    <a href="https://science.nasa.gov/mars/facts/" target="_blank" rel="noopener noreferrer">Source <i class="bi bi-arrow-up-right"></i></a>
                </div>
                <div class="data-source">
                    <div class="source-icon"><i class="bi bi-activity"></i></div>
                    <div><strong>NASA / NSSDC Mars Fact Sheet</strong><p>Reference parameters and atmospheric composition by volume.</p></div>
                    <a href="https://nssdc.gsfc.nasa.gov/planetary/factsheet/marsfact.html" target="_blank" rel="noopener noreferrer">Source <i class="bi bi-arrow-up-right"></i></a>
                </div>
                <div class="data-source">
                    <div class="source-icon"><i class="bi bi-map"></i></div>
                    <div><strong>NASA Mars Trek</strong><p>Planetary visualization and Mars exploration mapping resource.</p></div>
                    <a href="https://trek.nasa.gov/mars/" target="_blank" rel="noopener noreferrer">Source <i class="bi bi-arrow-up-right"></i></a>
                </div>
                <div class="dashboard-data-disclaimer"><i class="bi bi-exclamation-circle"></i> Dashboard charts use published reference values for education and planning context. They are not live measurements, landing safety ratings or mission telemetry.</div>
            </section>

            <footer class="app-footer">
                <span>PROJECT ASTRO © <?= date('Y') ?></span>
                <span>INTERPLANETARY SURVIVAL GUIDE · MARTIAN MAP</span>
            </footer>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/dashboard.js"></script>
</body>
</html>
