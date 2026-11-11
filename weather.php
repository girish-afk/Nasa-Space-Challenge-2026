<?php

declare(strict_types=1);
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';


require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Weather & Environment';
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'weather.php');
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
    <meta name="description" content="Mars atmospheric observations and environmental intelligence.">
    <title>PROJECT ASTRO | Weather &amp; Environment</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/modules.css">
    <link rel="stylesheet" href="assets/css/theme.css">
</head>
<body>
<div class="app-layout">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include __DIR__ . '/includes/navbar.php'; ?>
    <div class="page-header">
        <div>
            <span class="page-eyebrow">PLANETARY ENVIRONMENT</span>
            <h1>Mars Weather & Environment</h1>
            <p>Atmospheric observations and environmental intelligence.</p>
        </div>

        <button class="btn btn-primary" id="refreshWeather">
            <i class="bi bi-arrow-clockwise"></i>
            Refresh Data
        </button>
    </div>

    <div class="weather-source-banner">
        <i class="bi bi-info-circle"></i>
        <div>
            <strong>Scientific Data Source</strong>
            <p>Archived NASA InSight observations. The mission ended on December 15, 2022.
            The dashboard displays the latest archived readings it can retrieve; this is not live Mars weather.</p>
        </div>
    </div>

    <div class="weather-status-row">
        <span id="weatherStatus" class="weather-status">
            <span class="status-dot"></span>
            Connecting to weather service...
        </span>
        <span id="weatherUpdated" class="weather-updated">
            Awaiting observation
        </span>
    </div>

    <section class="weather-grid">

        <article class="astro-card weather-metric">
            <div class="metric-top">
                <span>Air Temperature</span>
                <i class="bi bi-thermometer-half"></i>
            </div>
            <div class="metric-value" id="temperature">--</div>
            <div class="metric-unit">Degrees Celsius</div>
            <div class="metric-description" id="temperatureRange">
                Waiting for observation
            </div>
        </article>

        <article class="astro-card weather-metric">
            <div class="metric-top">
                <span>Atmospheric Pressure</span>
                <i class="bi bi-speedometer2"></i>
            </div>
            <div class="metric-value" id="pressure">--</div>
            <div class="metric-unit">Pascals (Pa)</div>
            <div class="metric-description">Surface pressure</div>
        </article>

        <article class="astro-card weather-metric">
            <div class="metric-top">
                <span>Wind Speed</span>
                <i class="bi bi-wind"></i>
            </div>
            <div class="metric-value" id="windSpeed">--</div>
            <div class="metric-unit">Meters per second</div>
            <div class="metric-description">Where available</div>
        </article>

        <article class="astro-card weather-metric">
            <div class="metric-top">
                <span>Dust Conditions</span>
                <i class="bi bi-cloud-haze2"></i>
            </div>
            <div class="metric-value metric-text" id="dustCondition">
                --
            </div>
            <div class="metric-unit">Atmospheric opacity</div>
            <div class="metric-description" id="dustDescription">
                Awaiting observation
            </div>
        </article>

    </section>

    <section class="weather-layout">

        <article class="astro-card weather-chart-card">
            <div class="section-heading">
                <div>
                    <h3>Temperature History</h3>
                    <p>Historical atmospheric temperature observations</p>
                </div>
                <span class="chart-tag">NASA DATA</span>
            </div>

            <div class="weather-chart-container">
                <canvas id="temperatureChart"></canvas>
            </div>
        </article>

        <article class="astro-card weather-chart-card">
            <div class="section-heading">
                <div>
                    <h3>Pressure History</h3>
                    <p>Historical surface pressure observations</p>
                </div>
                <span class="chart-tag">NASA DATA</span>
            </div>

            <div class="weather-chart-container">
                <canvas id="pressureChart"></canvas>
            </div>
        </article>

    </section>

    <section class="astro-card weather-history-card">
        <div class="section-heading">
            <div>
                <h3>Latest Available Observations</h3>
                <p>Records returned by the selected data source</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table weather-table">
                <thead>
                    <tr>
                        <th>Sol / Date</th>
                        <th>Temperature</th>
                        <th>Pressure</th>
                        <th>Wind</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody id="weatherHistory">
                    <tr>
                        <td colspan="5">Loading observations...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="weather-alert-grid">

        <article class="astro-card">
            <div class="section-heading">
                <h3>Environmental Monitoring</h3>
            </div>

            <div class="environment-item">
                <span>Temperature observations</span>
                <strong id="temperatureState">Pending</strong>
            </div>

            <div class="environment-item">
                <span>Pressure observations</span>
                <strong id="pressureState">Pending</strong>
            </div>

            <div class="environment-item">
                <span>Wind observations</span>
                <strong id="windState">Pending</strong>
            </div>

            <div class="environment-item">
                <span>Dust observations</span>
                <strong id="dustState">Pending</strong>
            </div>
        </article>

        <article class="astro-card">
            <div class="section-heading">
                <h3>Mission Weather Intelligence</h3>
            </div>

            <div id="missionInsight" class="mission-insight">
                Waiting for verified observations before generating
                environmental insights.
            </div>

            <p class="weather-disclaimer">
                Environmental indicators are informational.
                They do not constitute certified landing or
                rover-operation safety assessments.
            </p>
        </article>

    </section>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/weather.js"></script>
<?php include __DIR__ . '/includes/footer.php'; ?>
