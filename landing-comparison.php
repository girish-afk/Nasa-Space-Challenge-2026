<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';


$pageTitle = 'Landing Site Comparison';
$currentPage = 'landing-comparison.php';
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
    <meta name="description" content="Compare saved Mars landing-site screening assessments and locally recorded hazards in PROJECT ASTRO.">
    <title>Landing Site Comparison | PROJECT ASTRO</title>
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

        <div class="dashboard-content module-content comparison-content">
            <section class="welcome-section">
                <div>
                    <div class="eyebrow"><span class="status-dot"></span> MISSION PLANNING / LANDING ANALYSIS</div>
                    <h1>Landing Site Comparison</h1>
                    <p>Compare saved landing-site screening assessments and locally recorded terrain hazards using one transparent comparison view.</p>
                </div>
                <div class="comparison-header-actions">
                    <button type="button" class="btn-secondary-custom" id="refreshComparison"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
                    <button type="button" class="btn-primary-custom" id="exportComparison"><i class="bi bi-download"></i> Export CSV</button>
                </div>
            </section>

            <section class="module-info-strip">
                <div><i class="bi bi-info-circle"></i><span>LOCAL SCREENING DATA · SIDE-BY-SIDE REVIEW</span></div>
                <span>NOT A CERTIFIED LANDING DECISION</span>
            </section>

            <section class="panel comparison-status-panel">
                <div>
                    <span class="panel-label">COMPARISON ENGINE</span>
                    <p id="comparisonStatus" role="status" aria-live="polite">Waiting for saved assessment data...</p>
                </div>
                <span class="comparison-count" id="candidateCount">0 Candidates</span>
            </section>

            <section class="comparison-summary-grid">
                <article class="panel comparison-stat-card">
                    <span class="panel-label">TOTAL CANDIDATES</span>
                    <strong id="totalCandidates">0</strong>
                    <small>Saved landing assessments</small>
                </article>
                <article class="panel comparison-stat-card">
                    <span class="panel-label">BEST SCREENING SCORE</span>
                    <strong id="bestScore">--</strong>
                    <small id="bestSiteName">No candidate selected</small>
                </article>
                <article class="panel comparison-stat-card">
                    <span class="panel-label">RECORDED HAZARDS</span>
                    <strong id="totalHazards">0</strong>
                    <small>Locally recorded observations</small>
                </article>
            </section>

            <section class="panel comparison-selection-panel">
                <div class="panel-heading">
                    <div><span class="panel-label">CANDIDATE SELECTION</span><h3>Choose Two Landing Sites</h3></div>
                    <i class="bi bi-sliders cyan"></i>
                </div>
                <div class="comparison-pair-grid">
                    <div class="module-field">
                        <label for="candidateA">Candidate A</label>
                        <select id="candidateA" class="form-control-custom"><option value="">Select candidate</option></select>
                    </div>
                    <div class="module-field">
                        <label for="candidateB">Candidate B</label>
                        <select id="candidateB" class="form-control-custom"><option value="">Select candidate</option></select>
                    </div>
                </div>
                <button type="button" class="btn-primary-custom mt-3" id="compareSites"><i class="bi bi-bar-chart-line"></i> Compare Candidates</button>
            </section>

            <section class="panel comparison-results-panel">
                <div class="panel-heading">
                    <div><span class="panel-label">PARAMETER REVIEW</span><h3>Side-by-Side Analysis</h3></div>
                    <span class="comparison-unit-note">Values from saved assessments</span>
                </div>
                <div class="table-responsive">
                    <table class="table comparison-table align-middle mb-0">
                        <thead><tr><th scope="col">Parameter</th><th scope="col" id="headingA">Candidate A</th><th scope="col" id="headingB">Candidate B</th></tr></thead>
                        <tbody id="comparisonTable"><tr><td colspan="3" class="comparison-empty">Select two saved candidates to compare.</td></tr></tbody>
                    </table>
                </div>
            </section>

            <section class="panel comparison-chart-panel">
                <div class="panel-heading">
                    <div><span class="panel-label">SCORING OVERVIEW</span><h3>Suitability Score Comparison</h3></div>
                    <i class="bi bi-graph-up cyan"></i>
                </div>
                <div class="comparison-chart-wrap"><canvas id="comparisonChart" aria-label="Bar chart comparing the selected candidates' screening scores" role="img"></canvas></div>
            </section>

            <section class="panel comparison-recommendation-panel" id="recommendationCard">
                <div class="panel-heading">
                    <div><span class="panel-label">DECISION SUPPORT</span><h3>Landing Site Recommendation</h3></div>
                    <i class="bi bi-clipboard2-check cyan"></i>
                </div>
                <div id="recommendationContent" class="comparison-recommendation-content">Select two candidates to generate a comparison.</div>
            </section>

            <section class="panel comparison-method-panel">
                <div class="panel-heading">
                    <div><span class="panel-label">TRANSPARENT SCORING</span><h3>Screening Methodology</h3></div>
                    <i class="bi bi-calculator cyan"></i>
                </div>
                <p class="comparison-method-intro">Candidate scores are inherited from the Landing Zone Analyzer's educational screening model.</p>
                <div class="comparison-weight-grid">
                    <div><span>Surface Slope</span><strong>35%</strong></div>
                    <div><span>Elevation Variation</span><strong>25%</strong></div>
                    <div><span>Rock Coverage</span><strong>20%</strong></div>
                    <div><span>Temperature Range</span><strong>20%</strong></div>
                </div>
                <p class="comparison-method-note">These are project-defined screening weights and illustrative thresholds, not official NASA landing certification criteria. Hazard counts include only observations saved in this browser and within the comparison's 5 km proximity rule. Missing or unverified scientific measurements must not be interpreted as safe conditions.</p>
            </section>

            <footer class="app-footer"><span>PROJECT ASTRO © 2026</span><span>LANDING SITE COMPARISON · PRELIMINARY SCREENING</span></footer>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/landing-comparison.js"></script>
</body>
</html>
