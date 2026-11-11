
<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';


$pageTitle = "Hazard Detection";
$currentPage = "hazards.php";
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
    <meta name="description" content="Mars terrain hazard registry and preliminary safe-zone screening with PROJECT ASTRO.">
    <title>Hazard Detection | PROJECT ASTRO</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/modules.css">
    <link rel="stylesheet" href="assets/css/theme.css">
</head>

<body>

<div class="app-layout">

    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <main class="main-content">

        <?php include __DIR__ . '/includes/navbar.php'; ?>

        <div class="dashboard-content module-content hazard-content">

            <!-- PAGE HEADER -->
            <section class="welcome-section">
                <div>
                    <div class="eyebrow">
                        <span class="status-dot"></span>
                        PLANETARY SURFACE SAFETY SYSTEM
                    </div>

                    <h1>Hazard Detection</h1>

                    <p>
                        Identify and classify terrain hazards, inspect
                        potential slope risks and evaluate candidate safe zones.
                    </p>
                </div>

                <div class="module-header-status">
                    <span class="module-status-dot"></span>
                    HAZARD REGISTRY READY
                </div>
            </section>

            <!-- NOTICE -->
            <div class="hazard-notice">
                <i class="bi bi-info-circle"></i>
                <div>
                    <strong>Observation-based hazard registry</strong>
                    <p>
                        Hazards are manually registered from map observations
                        or supplied measurements. Automatic image recognition
                        and verified NASA hazard datasets are not connected.
                    </p>
                </div>
            </div>

            <!-- STATISTICS -->
            <section class="hazard-stats-grid">

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Registered Hazards</span>
                        <i class="bi bi-radar orange"></i>
                    </div>
                    <h2 id="totalHazards">0</h2>
                    <p>Total recorded observations</p>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Critical / High</span>
                        <i class="bi bi-exclamation-octagon" style="color:var(--danger)"></i>
                    </div>
                    <h2 id="highHazards">0</h2>
                    <p>High-priority observations</p>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Crater Hazards</span>
                        <i class="bi bi-circle orange"></i>
                    </div>
                    <h2 id="craterHazards">0</h2>
                    <p>Registered crater observations</p>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Safe Candidates</span>
                        <i class="bi bi-shield-check cyan"></i>
                    </div>
                    <h2 id="safeCandidates">0</h2>
                    <p>Passing configured screening rules</p>
                </div>

            </section>

            <!-- MAP AND REGISTRATION -->
            <section class="hazard-main-grid">

                <!-- MAP -->
                <div class="panel hazard-map-panel">

                    <div class="panel-heading">
                        <div>
                            <span class="panel-label">MARS TERRAIN MONITORING</span>
                            <h3>Hazard Visualization Map</h3>
                        </div>

                        <span class="map-layer-tag">ARCGIS MARS IMAGERY</span>
                    </div>

                    <div class="hazard-map-toolbar">

                        <div class="hazard-map-legend">
                            <span><i class="legend-dot critical"></i> Critical</span>
                            <span><i class="legend-dot high"></i> High</span>
                            <span><i class="legend-dot moderate"></i> Moderate</span>
                            <span><i class="legend-dot low"></i> Low</span>
                            <span><i class="legend-dot safe"></i> Safe candidate</span>
                        </div>

                        <button type="button" class="btn-secondary-custom"
                                id="hazardMapReset">
                            <i class="bi bi-crosshair"></i> Reset Map
                        </button>

                    </div>

                    <div id="hazardMap" aria-label="Mars terrain hazard map"></div>

                    <div class="hazard-map-footer">
                        <span id="hazardMapStatus">
                            <i class="bi bi-circle-fill"></i>
                            INITIALIZING MAP
                        </span>

                        <span id="hazardMapCoordinates">
                            LAT: 0.0000 · LNG: 0.0000
                        </span>
                    </div>

                    <p class="hazard-map-note">
                        Click the map to select a map-service position.
                        Coordinates are not validated Mars planetographic coordinates.
                    </p>

                </div>

                <!-- REGISTER HAZARD -->
                <div class="panel hazard-register-panel">

                    <div class="panel-heading">
                        <div>
                            <span class="panel-label">FIELD OBSERVATION</span>
                            <h3>Register New Hazard</h3>
                        </div>

                        <i class="bi bi-plus-circle cyan"></i>
                    </div>

                    <form id="hazardForm">

                        <div class="module-field">
                            <label for="hazardName">Observation Name</label>
                            <input type="text" id="hazardName"
                                   maxlength="60"
                                   placeholder="Example: Crater Observation A"
                                   required>
                        </div>

                        <div class="module-field">
                            <label for="hazardType">Hazard Category</label>
                            <select id="hazardType" required>
                                <option value="crater">Impact Crater</option>
                                <option value="rock">Rock Field</option>
                                <option value="boulder">Large Boulder</option>
                                <option value="slope">Steep Slope</option>
                                <option value="other">Other Obstruction</option>
                            </select>
                        </div>

                        <div class="module-field">
                            <label for="hazardSeverity">Severity Classification</label>
                            <select id="hazardSeverity" required>
                                <option value="low">Low</option>
                                <option value="moderate" selected>Moderate</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>

                        <div class="module-field-grid">

                            <div class="module-field">
                                <label for="hazardLat">Map Latitude</label>
                                <input type="number" id="hazardLat"
                                       min="-90" max="90" step="any" required>
                            </div>

                            <div class="module-field">
                                <label for="hazardLng">Map Longitude</label>
                                <input type="number" id="hazardLng"
                                       min="-180" max="180" step="any" required>
                            </div>

                        </div>

                        <div class="module-field">
                            <label for="hazardDiameter">
                                Estimated Diameter <span>Optional · meters</span>
                            </label>
                            <input type="number" id="hazardDiameter"
                                   min="0" max="100000" step="any"
                                   placeholder="Enter observed diameter">
                        </div>

                        <div class="module-field">
                            <label for="hazardDescription">Observation Notes</label>
                            <textarea id="hazardDescription" rows="3"
                                      maxlength="500"
                                      placeholder="Describe the observed terrain feature..."></textarea>
                        </div>

                        <button type="submit" class="btn-primary-custom hazard-submit">
                            <i class="bi bi-plus-lg"></i>
                            Register Hazard
                        </button>

                    </form>

                    <p id="hazardFeedback" class="hazard-feedback"
                       role="status" aria-live="polite"></p>

                </div>

            </section>

            <!-- SLOPE ANALYSIS -->
            <section class="hazard-lower-grid">

                <div class="panel slope-panel">

                    <div class="panel-heading">
                        <div>
                            <span class="panel-label">TERRAIN INCLINATION ANALYSIS</span>
                            <h3>Slope Risk Visualization</h3>
                        </div>
                        <i class="bi bi-graph-up-arrow cyan"></i>
                    </div>

                    <p class="section-description">
                        Enter a measured slope value to evaluate its
                        preliminary screening category.
                    </p>

                    <div class="slope-input-area">

                        <div class="module-field">
                            <label for="slopeInput">
                                Measured Surface Slope
                                <span>Degrees (°)</span>
                            </label>

                            <input type="number" id="slopeInput"
                                   min="0" max="90" step="0.1"
                                   value="4">
                        </div>

                        <button type="button" class="btn-primary-custom"
                                id="analyzeSlope">
                            <i class="bi bi-activity"></i>
                            Analyze Slope
                        </button>

                    </div>

                    <div class="slope-visual">

                        <div class="slope-value-display">
                            <strong id="slopeValueDisplay">4°</strong>
                            <span id="slopeRiskLabel" class="risk-label low">
                                LOW SCREENING RISK
                            </span>
                        </div>

                        <div class="slope-scale">
                            <div class="slope-segment safe-segment"></div>
                            <div class="slope-segment moderate-segment"></div>
                            <div class="slope-segment high-segment"></div>
                            <div class="slope-segment critical-segment"></div>
                        </div>

                        <div class="slope-scale-labels">
                            <span>0°</span>
                            <span>5°</span>
                            <span>10°</span>
                            <span>20°</span>
                            <span>90°</span>
                        </div>

                        <div class="slope-marker-track">
                            <div id="slopeMarker" class="slope-marker"></div>
                        </div>

                        <p id="slopeExplanation" class="slope-explanation">
                            The entered slope is within the model's
                            lowest screening category.
                        </p>

                    </div>

                    <p class="methodology-note">
                        Illustrative thresholds: 0–5° low, above 5–10°
                        moderate, above 10–20° high, above 20° critical.
                        These are project assumptions, not NASA landing standards.
                    </p>

                </div>

                <!-- SAFE ZONE -->
                <div class="panel safe-zone-panel">

                    <div class="panel-heading">
                        <div>
                            <span class="panel-label">LANDING CANDIDATE SCREENING</span>
                            <h3>Safe-Zone Identification</h3>
                        </div>
                        <i class="bi bi-shield-check cyan"></i>
                    </div>

                    <p class="section-description">
                        Evaluate a candidate position against registered
                        nearby hazards and the selected slope limit.
                    </p>

                    <form id="safeZoneForm">

                        <div class="module-field">
                            <label for="safeZoneName">Candidate Name</label>
                            <input type="text" id="safeZoneName"
                                   maxlength="60"
                                   placeholder="Example: Candidate Zone A"
                                   required>
                        </div>

                        <div class="module-field-grid">

                            <div class="module-field">
                                <label for="safeLat">Map Latitude</label>
                                <input type="number" id="safeLat"
                                       min="-90" max="90" step="any" required>
                            </div>

                            <div class="module-field">
                                <label for="safeLng">Map Longitude</label>
                                <input type="number" id="safeLng"
                                       min="-180" max="180" step="any" required>
                            </div>

                        </div>

                        <div class="module-field-grid">

                            <div class="module-field">
                                <label for="safeSlope">Measured Slope</label>
                                <input type="number" id="safeSlope"
                                       min="0" max="90" step="0.1"
                                       placeholder="Degrees" required>
                            </div>

                            <div class="module-field">
                                <label for="safeBuffer">Search Radius (km)</label>
                                <input type="number" id="safeBuffer"
                                       min="0.1" max="100"
                                       step="0.1" value="5" required>
                            </div>

                        </div>

                        <p class="methodology-note">
                            Radius is estimated on a spherical Mars model (mean radius 3,390 km).
                            Confirm the map coordinate reference system before scientific use.
                        </p>

                        <button type="submit" class="btn-primary-custom safe-zone-submit">
                            <i class="bi bi-shield-check"></i>
                            Evaluate Candidate
                        </button>

                    </form>

                    <div id="safeZoneResult" class="safe-zone-result" hidden>
                        <div class="safe-result-icon" id="safeResultIcon">
                            <i class="bi bi-shield-check"></i>
                        </div>

                        <div>
                            <strong id="safeResultTitle">Candidate Result</strong>
                            <p id="safeResultDescription"></p>
                        </div>
                    </div>

                </div>

            </section>

            <!-- HAZARD REGISTRY -->
            <section class="panel hazard-registry-panel">

                <div class="panel-heading registry-heading">
                    <div>
                        <span class="panel-label">OBSERVATION DATABASE</span>
                        <h3>Registered Hazard Registry</h3>
                    </div>

                    <div class="registry-actions">
                        <select id="hazardFilter" aria-label="Filter hazards">
                            <option value="all">All Categories</option>
                            <option value="crater">Crater</option>
                            <option value="rock">Rock Field</option>
                            <option value="boulder">Boulder</option>
                            <option value="slope">Steep Slope</option>
                            <option value="other">Other</option>
                        </select>

                        <button type="button" class="btn-secondary-custom"
                                id="exportHazards">
                            <i class="bi bi-download"></i>
                            Export
                        </button>
                    </div>
                </div>

                <div class="hazard-table-wrapper">
                    <table class="hazard-table">
                        <thead>
                            <tr>
                                <th>OBSERVATION</th>
                                <th>CATEGORY</th>
                                <th>SEVERITY</th>
                                <th>MAP COORDINATES</th>
                                <th>DIAMETER</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>

                        <tbody id="hazardTableBody">
                            <tr>
                                <td colspan="6" class="hazard-empty-row">
                                    No hazards registered. Add an observation to begin.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="registry-footer">
                    <span id="registryCount">0 observations</span>
                    <button type="button" id="clearHazards" class="danger-text-button">
                        <i class="bi bi-trash3"></i>
                        Clear Registry
                    </button>
                </div>

            </section>

            <!-- SCIENTIFIC LIMITATIONS -->
            <section class="panel source-panel hazard-source-panel">
                <div class="source-icon">
                    <i class="bi bi-database-check"></i>
                </div>

                <div>
                    <strong>Data source and scientific limitations</strong>
                    <p>
                        Terrain imagery: ArcGIS-hosted Mars Contour Imagery.
                        Hazard entries and candidate assessments are stored
                        locally in this browser. No automatic crater recognition,
                        boulder segmentation, elevation retrieval or NASA
                        observation ingestion is currently performed.
                    </p>
                </div>

                <a href="https://trek.nasa.gov/mars/"
                   target="_blank" rel="noopener noreferrer">
                    NASA Mars Trek <i class="bi bi-box-arrow-up-right"></i>
                </a>
            </section>

            <footer class="app-footer">
                <span>PROJECT ASTRO © 2026</span>
                <span>HAZARD DETECTION · PRELIMINARY TERRAIN SCREENING</span>
            </footer>

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/hazards.js"></script>

</body>
</html>
