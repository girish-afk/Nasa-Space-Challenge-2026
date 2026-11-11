<?php
declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PROJECT ASTRO — explore Mars, evaluate landing sites, monitor the environment and plan mission operations.">
    <meta name="theme-color" content="#090D14">
    <title>PROJECT ASTRO | Mission Gateway</title>

    <script>
    (function () {
        try {
            var settings = JSON.parse(localStorage.getItem('projectAstro.settings.v1') || '{}');
            var theme = settings.theme === 'light' ? 'light' : 'dark';
            var accent = settings.accent === 'cyan' ? 'cyan' : 'orange';
            var root = document.documentElement;
            root.dataset.astroTheme = theme;
            root.dataset.astroAccent = accent;
            root.style.colorScheme = theme;
            root.style.setProperty('--accent', accent === 'cyan' ? '#50C8E8' : '#E66A45');
            root.style.setProperty('--accent-hover', accent === 'cyan' ? '#79D9F0' : '#F18460');
        } catch (error) {
            document.documentElement.dataset.astroTheme = 'dark';
            document.documentElement.dataset.astroAccent = 'orange';
        }
    })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/modules.css">
    <link rel="stylesheet" href="assets/css/gateway.css?v=4">
    <meta name="color-scheme" content="dark light">
</head>
<body class="gateway-body">
<a class="gateway-skip-link" href="#main-content">Skip to main content</a>

<header class="gateway-nav">
    <a href="index.php" class="gateway-brand" aria-label="PROJECT ASTRO home">
        <span class="brand-icon"><i class="bi bi-globe-americas" aria-hidden="true"></i></span>
        <span>
            <strong>PROJECT <em>ASTRO</em></strong>
            <small>PLANETARY EXPLORATION</small>
        </span>
    </a>

    <button class="gateway-menu-toggle" id="gatewayMenuToggle" type="button"
            aria-expanded="false" aria-controls="gatewayNavigation" aria-label="Open navigation">
        <i class="bi bi-list" aria-hidden="true"></i>
    </button>

    <nav class="gateway-links" id="gatewayNavigation" aria-label="Main navigation">
        <a href="#mission">Mission</a>
        <a href="#modules">Modules</a>
        <a href="explorer.php">Mars Explorer</a>
        <a href="weather.php">Weather</a>
    </nav>

    <div class="gateway-nav-cta-group">
        <a href="dashboard.php" class="btn-primary-custom gateway-nav-cta">
            Mission Dashboard <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
        </a>
    </div>
</header>

<main id="main-content">
    <section class="gateway-hero" id="mission">
        <div class="gateway-content">
            <div class="eyebrow">
                <span class="status-dot" aria-hidden="true"></span>
                INTERPLANETARY SURVIVAL GUIDE · MARS
            </div>

            <span class="gateway-kicker">THE NEXT FRONTIER STARTS WITH UNDERSTANDING</span>

            <h1>Explore Mars.<br><span>Plan Beyond Earth.</span></h1>

            <p class="gateway-description">
                Explore the Martian surface, investigate landing locations, monitor
                environmental conditions and plan the resources and routes needed
                for future exploration missions.
            </p>

            <div class="gateway-actions">
                <a href="dashboard.php" class="btn-primary-custom">
                    Enter Mission Control <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
                <a href="explorer.php" class="btn-secondary-custom">
                    <i class="bi bi-globe-americas" aria-hidden="true"></i> Open Mars Explorer
                </a>
            </div>

            <div class="gateway-meta" aria-label="Platform focus">
                <span><i class="bi bi-broadcast-pin" aria-hidden="true"></i> MARS OPERATIONS</span>
                <span><i class="bi bi-database" aria-hidden="true"></i> SCIENCE-LED EXPLORATION</span>
                <span><i class="bi bi-shield-check" aria-hidden="true"></i> MISSION PLANNING</span>
            </div>
        </div>

        <div class="gateway-visual" role="img" aria-label="Stylized view of the planet Mars above a Martian landscape">
            <div class="gateway-planet" aria-hidden="true"></div>
            <div class="gateway-horizon" aria-hidden="true"></div>
            <div class="gateway-visual-overlay" aria-hidden="true"></div>
            <div class="gateway-visual-caption">
                <span class="status-dot" aria-hidden="true"></span>
                PLANETARY TARGET
                <strong>MARS · EXPLORATION ZONE</strong>
            </div>
            <div class="gateway-coordinate" aria-hidden="true">14.6° N&nbsp; / &nbsp;175.3° E</div>
        </div>

        <a class="gateway-scroll" href="#modules">
            <span aria-hidden="true"></span> EXPLORE PLATFORM MODULES
        </a>
    </section>

    <section class="gateway-capabilities" id="modules">
        <div class="gateway-section-heading">
            <div>
                <span class="panel-label">MISSION CAPABILITIES</span>
                <h2>Every tool. One mission workspace.</h2>
            </div>
            <p>
                Open any module below. Each card takes you directly to its existing
                PROJECT ASTRO page.
            </p>
        </div>

        <div class="gateway-grid gateway-module-grid">
            <a href="dashboard.php" class="gateway-card">
                <div class="gateway-icon cyan-bg"><i class="bi bi-speedometer2" aria-hidden="true"></i></div>
                <span class="feature-index">01 / OVERVIEW</span>
                <h3>Mission Dashboard</h3>
                <p>Review the mission overview, key indicators and shortcuts to platform tools.</p>
                <span class="feature-link">OPEN DASHBOARD <i class="bi bi-arrow-up-right"></i></span>
            </a>

            <a href="explorer.php" class="gateway-card">
                <div class="gateway-icon orange-bg"><i class="bi bi-globe-americas" aria-hidden="true"></i></div>
                <span class="feature-index">02 / EXPLORATION</span>
                <h3>Martian Explorer</h3>
                <p>Explore the Mars map, inspect locations and work with planetary coordinates.</p>
                <span class="feature-link">OPEN EXPLORER <i class="bi bi-arrow-up-right"></i></span>
            </a>

            <a href="landing-zone.php" class="gateway-card">
                <div class="gateway-icon cyan-bg"><i class="bi bi-bullseye" aria-hidden="true"></i></div>
                <span class="feature-index">03 / SITE ANALYSIS</span>
                <h3>Landing Zone Analyzer</h3>
                <p>Evaluate candidate landing areas using the available site assessment tools.</p>
                <span class="feature-link">ANALYZE LANDING ZONES <i class="bi bi-arrow-up-right"></i></span>
            </a>

            <a href="landing-comparison.php" class="gateway-card">
                <div class="gateway-icon green-bg"><i class="bi bi-bar-chart-line" aria-hidden="true"></i></div>
                <span class="feature-index">04 / COMPARISON</span>
                <h3>Landing Site Comparison</h3>
                <p>Compare landing candidates and review their displayed evaluation criteria.</p>
                <span class="feature-link">COMPARE SITES <i class="bi bi-arrow-up-right"></i></span>
            </a>

            <a href="hazards.php" class="gateway-card">
                <div class="gateway-icon warning-bg"><i class="bi bi-shield-exclamation" aria-hidden="true"></i></div>
                <span class="feature-index">05 / SAFETY</span>
                <h3>Hazard Detection</h3>
                <p>Review terrain and mission hazards with the project's hazard analysis interface.</p>
                <span class="feature-link">VIEW HAZARDS <i class="bi bi-arrow-up-right"></i></span>
            </a>

            <a href="weather.php" class="gateway-card">
                <div class="gateway-icon cyan-bg"><i class="bi bi-cloud-haze2" aria-hidden="true"></i></div>
                <span class="feature-index">06 / ENVIRONMENT</span>
                <h3>Weather & Environment</h3>
                <p>Check the available Mars weather and environmental information panels.</p>
                <span class="feature-link">CHECK WEATHER <i class="bi bi-arrow-up-right"></i></span>
            </a>

            <a href="resources.php" class="gateway-card">
                <div class="gateway-icon green-bg"><i class="bi bi-droplet-half" aria-hidden="true"></i></div>
                <span class="feature-index">07 / RESOURCES</span>
                <h3>Water & Resources</h3>
                <p>Explore resource information and the resource assessment interface.</p>
                <span class="feature-link">EXPLORE RESOURCES <i class="bi bi-arrow-up-right"></i></span>
            </a>

            <a href="route-planner.php" class="gateway-card">
                <div class="gateway-icon orange-bg"><i class="bi bi-signpost-split" aria-hidden="true"></i></div>
                <span class="feature-index">08 / NAVIGATION</span>
                <h3>Rover Route Planner</h3>
                <p>Plan rover routes and work with saved route information.</p>
                <span class="feature-link">PLAN A ROUTE <i class="bi bi-arrow-up-right"></i></span>
            </a>

            <a href="survival.php" class="gateway-card">
                <div class="gateway-icon warning-bg"><i class="bi bi-heart-pulse" aria-hidden="true"></i></div>
                <span class="feature-index">09 / CREW SUPPORT</span>
                <h3>Survival Planner</h3>
                <p>Review survival planning tools and mission-support calculations.</p>
                <span class="feature-link">OPEN SURVIVAL PLANNER <i class="bi bi-arrow-up-right"></i></span>
            </a>

            <a href="reports.php" class="gateway-card">
                <div class="gateway-icon cyan-bg"><i class="bi bi-file-earmark-text" aria-hidden="true"></i></div>
                <span class="feature-index">10 / DOCUMENTATION</span>
                <h3>Mission Reports</h3>
                <p>Access the mission reporting interface and its available report tools.</p>
                <span class="feature-link">VIEW REPORTS <i class="bi bi-arrow-up-right"></i></span>
            </a>

            <a href="settings.php" class="gateway-card">
                <div class="gateway-icon green-bg"><i class="bi bi-sliders" aria-hidden="true"></i></div>
                <span class="feature-index">11 / PREFERENCES</span>
                <h3>System Settings</h3>
                <p>Adjust interface preferences, appearance and supported local settings.</p>
                <span class="feature-link">OPEN SETTINGS <i class="bi bi-arrow-up-right"></i></span>
            </a>
        </div>
    </section>

    <section class="gateway-ready" aria-labelledby="gateway-ready-title">
        <div>
            <span class="panel-label">READY FOR OPERATIONS</span>
            <h2 id="gateway-ready-title">Start with Mission Control.</h2>
            <p>Get an overview first, then open the tools you need for your exploration workflow.</p>
        </div>
        <a href="dashboard.php" class="btn-primary-custom">
                Go to Mission Dashboard <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
    </section>
</main>

<footer class="gateway-footer">
    <a href="index.php" class="gateway-brand" aria-label="PROJECT ASTRO home">
        <span class="brand-icon"><i class="bi bi-globe-americas" aria-hidden="true"></i></span>
        <span>
            <strong>PROJECT <em>ASTRO</em></strong>
            <small>INTERPLANETARY SURVIVAL GUIDE</small>
        </span>
    </a>
    <span>INDEPENDENT MARS EXPLORATION PROJECT · 2026</span>
</footer>

<script>
(function () {
    var toggle = document.getElementById('gatewayMenuToggle');
    var navigation = document.getElementById('gatewayNavigation');
    if (!toggle || !navigation) return;

    toggle.addEventListener('click', function () {
        var isOpen = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!isOpen));
        toggle.setAttribute('aria-label', isOpen ? 'Open navigation' : 'Close navigation');
        navigation.classList.toggle('is-open', !isOpen);
        toggle.innerHTML = isOpen
            ? '<i class="bi bi-list" aria-hidden="true"></i>'
            : '<i class="bi bi-x-lg" aria-hidden="true"></i>';
    });

    navigation.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Open navigation');
            navigation.classList.remove('is-open');
            toggle.innerHTML = '<i class="bi bi-list" aria-hidden="true"></i>';
        });
    });
})();
</script>
</body>
</html>
