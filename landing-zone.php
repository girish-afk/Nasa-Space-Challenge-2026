
<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';


$pageTitle = 'Landing Zone Analyzer';
$currentPage = 'landing-zone.php';
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
    <meta name="description" content="Preliminary Mars landing site suitability assessment — PROJECT ASTRO.">
    <title>Landing Zone Analyzer | PROJECT ASTRO</title>

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

        <div class="dashboard-content module-content">

            <!-- PAGE INTRO -->
            <section class="welcome-section">
                <div>
                    <div class="eyebrow">
                        <span class="status-dot"></span>
                        PLANETARY SURFACE ASSESSMENT
                    </div>
                    <h1>Landing Zone Analyzer</h1>
                    <p>
                        Evaluate proposed Martian landing locations using
                        terrain and environmental screening criteria.
                    </p>
                </div>

                <div class="module-header-status">
                    <span class="module-status-dot"></span>
                    SCREENING ENGINE READY
                </div>
            </section>

            <!-- INFORMATION STRIP -->
            <section class="module-info-strip">
                <div>
                    <i class="bi bi-info-circle"></i>
                    <span>PRELIMINARY LANDING SUITABILITY MODEL</span>
                </div>
                <span>USER-PROVIDED INPUTS · LOCAL CALCULATION</span>
            </section>

            <!-- MAIN GRID -->
            <section class="landing-grid">

                <!-- INPUT PANEL -->
                <div class="panel landing-input-panel">

                    <div class="panel-heading">
                        <div>
                            <span class="panel-label">ASSESSMENT CONFIGURATION</span>
                            <h3>Landing Site Parameters</h3>
                        </div>
                        <i class="bi bi-sliders cyan"></i>
                    </div>

                    <form id="landingAssessmentForm">
                        <p id="explorerImportNotice" class="module-disclaimer" role="status" hidden></p>

                        <div class="form-section-heading">
                            <i class="bi bi-geo-alt"></i>
                            <span>LOCATION INFORMATION</span>
                        </div>

                        <div class="module-field">
                            <label for="siteName">Landing Site Name</label>
                            <input type="text" id="siteName"
                                   maxlength="60"
                                   placeholder="Example: Candidate Site Alpha"
                                   required>
                        </div>

                        <div class="module-field-grid">

                            <div class="module-field">
                                <label for="siteLatitude">Latitude (°)</label>
                                <input type="number" id="siteLatitude"
                                       min="-90" max="90" step="any"
                                       placeholder="e.g. 18.4" required>
                            </div>

                            <div class="module-field">
                                <label for="siteLongitude">Longitude (°)</label>
                                <input type="number" id="siteLongitude"
                                       min="-180" max="180" step="any"
                                       placeholder="e.g. 77.5" required>
                            </div>

                        </div>

                        <div class="module-disclaimer">
                            <i class="bi bi-exclamation-triangle"></i>
                            Coordinates must be supplied in a documented
                            Mars coordinate reference system before scientific
                            interpretation. This calculator does not validate
                            planetary coordinates.
                        </div>

                        <div class="form-section-heading">
                            <i class="bi bi-mountain"></i>
                            <span>TERRAIN CHARACTERISTICS</span>
                        </div>

                        <div class="module-field">
                            <label for="slope">
                                Surface Slope
                                <span>Degrees (°)</span>
                            </label>
                            <input type="number" id="slope"
                                   min="0" max="90" step="0.1"
                                   placeholder="Enter measured slope"
                                   required>
                            <small>Lower slope generally favors landing stability.</small>
                        </div>

                        <div class="module-field">
                            <label for="elevationVariation">
                                Local Elevation Variation
                                <span>Meters (m)</span>
                            </label>
                            <input type="number" id="elevationVariation"
                                   min="0" max="10000" step="0.1"
                                   placeholder="Enter elevation variation"
                                   required>
                            <small>Enter the elevation range across the assessed area.</small>
                        </div>

                        <div class="module-field">
                            <label for="rockCoverage">
                                Estimated Rock Coverage
                                <span>Percentage (%)</span>
                            </label>
                            <input type="number" id="rockCoverage"
                                   min="0" max="100" step="0.1"
                                   placeholder="0–100"
                                   required>
                            <small>Use a documented observation or dataset-derived estimate.</small>
                        </div>

                        <div class="form-section-heading">
                            <i class="bi bi-thermometer-half"></i>
                            <span>ENVIRONMENTAL CONDITIONS</span>
                        </div>

                        <div class="module-field-grid">

                            <div class="module-field">
                                <label for="minTemperature">Minimum Temperature</label>
                                <div class="input-with-unit">
                                    <input type="number" id="minTemperature"
                                           min="-200" max="100" step="0.1"
                                           placeholder="-80" required>
                                    <span>°C</span>
                                </div>
                            </div>

                            <div class="module-field">
                                <label for="maxTemperature">Maximum Temperature</label>
                                <div class="input-with-unit">
                                    <input type="number" id="maxTemperature"
                                           min="-200" max="100" step="0.1"
                                           placeholder="-20" required>
                                    <span>°C</span>
                                </div>
                            </div>

                        </div>

                        <div class="module-disclaimer">
                            <i class="bi bi-database"></i>
                            Enter observations for the same location and
                            relevant assessment period. These values are not
                            automatically retrieved from NASA.
                        </div>

                        <div class="module-form-actions">
                            <button type="submit" class="btn-primary-custom">
                                <i class="bi bi-calculator"></i>
                                Analyze Landing Site
                            </button>

                            <button type="reset" class="btn-secondary-custom"
                                    id="resetAssessment">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                Reset
                            </button>
                        </div>

                    </form>
                </div>

                <!-- RESULTS PANEL -->
                <aside class="landing-results-column">

                    <div class="panel landing-result-panel">

                        <div class="panel-heading">
                            <div>
                                <span class="panel-label">MISSION SUITABILITY</span>
                                <h3>Assessment Result</h3>
                            </div>
                            <i class="bi bi-clipboard-data cyan"></i>
                        </div>

                        <div id="emptyResult" class="empty-result">
                            <div class="empty-result-icon">
                                <i class="bi bi-radar"></i>
                            </div>
                            <h4>Awaiting Site Analysis</h4>
                            <p>
                                Enter the landing site parameters and run
                                the assessment to view the results.
                            </p>
                        </div>

                        <div id="assessmentResult" hidden>

                            <div class="suitability-score">
                                <div class="score-ring" id="scoreRing">
                                    <div>
                                        <strong id="totalScore">--</strong>
                                        <span>OUT OF 100</span>
                                    </div>
                                </div>

                                <span class="score-category" id="scoreCategory">
                                    NOT ASSESSED
                                </span>
                                <p id="scoreSummary"></p>
                            </div>

                            <div class="result-divider"></div>

                            <div class="result-site-details">
                                <span>ASSESSED LOCATION</span>
                                <strong id="resultSiteName">—</strong>
                                <small id="resultCoordinates">—</small>
                            </div>

                            <div class="result-factors">
                                <div class="panel-label">FACTOR BREAKDOWN</div>

                                <div class="factor-row">
                                    <div class="factor-top">
                                        <span>Surface Slope</span>
                                        <strong id="slopeScore">—</strong>
                                    </div>
                                    <div class="factor-track">
                                        <div id="slopeBar"></div>
                                    </div>
                                </div>

                                <div class="factor-row">
                                    <div class="factor-top">
                                        <span>Elevation Variation</span>
                                        <strong id="elevationScore">—</strong>
                                    </div>
                                    <div class="factor-track">
                                        <div id="elevationBar"></div>
                                    </div>
                                </div>

                                <div class="factor-row">
                                    <div class="factor-top">
                                        <span>Rock Coverage</span>
                                        <strong id="rockScore">—</strong>
                                    </div>
                                    <div class="factor-track">
                                        <div id="rockBar"></div>
                                    </div>
                                </div>

                                <div class="factor-row">
                                    <div class="factor-top">
                                        <span>Temperature Range</span>
                                        <strong id="temperatureScore">—</strong>
                                    </div>
                                    <div class="factor-track">
                                        <div id="temperatureBar"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="result-divider"></div>

                            <div class="recommendation-block">
                                <span class="panel-label">ASSESSMENT FINDINGS</span>
                                <ul id="assessmentFindings"></ul>
                            </div>

                            <button type="button" id="downloadAssessment"
                                    class="btn-secondary-custom report-download">
                                <i class="bi bi-file-earmark-arrow-down"></i>
                                Download Assessment Report
                            </button>

                        </div>
                    </div>

                    <div class="panel methodology-panel">
                        <div class="panel-heading">
                            <div>
                                <span class="panel-label">MODEL TRANSPARENCY</span>
                                <h3>Scoring Methodology</h3>
                            </div>
                            <i class="bi bi-diagram-3 cyan"></i>
                        </div>

                        <div class="methodology-row">
                            <span>Surface slope</span><strong>35%</strong>
                        </div>
                        <div class="methodology-row">
                            <span>Elevation variation</span><strong>25%</strong>
                        </div>
                        <div class="methodology-row">
                            <span>Rock coverage</span><strong>20%</strong>
                        </div>
                        <div class="methodology-row">
                            <span>Temperature range</span><strong>20%</strong>
                        </div>

                        <p class="methodology-note">
                            These are configurable educational screening
                            assumptions, not official NASA or mission
                            engineering acceptance thresholds.
                        </p>
                    </div>

                </aside>

            </section>

            <footer class="app-footer">
                <span>PROJECT ASTRO © 2026</span>
                <span>LANDING ZONE ANALYSIS · PRELIMINARY SCREENING</span>
            </footer>

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/landing-zone.js"></script>

</body>
</html>
