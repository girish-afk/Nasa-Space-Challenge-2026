
<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';


$currentPage = 'route-planner.php';
$pageTitle = 'Rover Route Planner';

require_once __DIR__ . '/includes/functions.php';
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
    <title>Rover Route Planner | PROJECT ASTRO</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/modules.css">
    <link rel="stylesheet" href="assets/css/theme.css">
</head>

<body>
<div class="app-layout">

    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <main class="main-content">

        <?php include __DIR__ . '/includes/navbar.php'; ?>

        <div class="dashboard-content route-page">

            <section class="welcome-section">
                <div>
                    <div class="eyebrow">
                        <i class="bi bi-signpost-split orange"></i>
                        MISSION PLANNING / NAVIGATION
                    </div>

                    <h1>Rover Route Planner</h1>

                    <p>
                        Plan rover movement, manage waypoints and evaluate
                        potential terrain hazards across Mars.
                    </p>
                </div>

                <div class="route-status">
                    <span class="status-dot"></span>
                    PLANNER READY
                </div>
            </section>

            <!-- ROUTE STATISTICS -->

            <section class="route-stats">

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Route Distance</span>
                        <i class="bi bi-rulers orange"></i>
                    </div>
                    <h2 id="routeDistance">0.00 <small>km</small></h2>
                    <p>Calculated waypoint distance</p>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Estimated Travel</span>
                        <i class="bi bi-clock cyan"></i>
                    </div>
                    <h2 id="routeTime">0 <small>hrs</small></h2>
                    <p>Based on selected rover speed</p>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Waypoints</span>
                        <i class="bi bi-geo-alt orange"></i>
                    </div>
                    <h2 id="waypointCount">0</h2>
                    <p>Route locations</p>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Hazard Intersections</span>
                        <i class="bi bi-shield-exclamation" style="color:var(--warning)"></i>
                    </div>
                    <h2 id="hazardCount">0</h2>
                    <p>Known hazards near route</p>
                </div>

            </section>

            <!-- MAIN PLANNER -->

            <section class="route-layout">

                <div class="panel route-map-panel">

                    <div class="panel-heading">
                        <div>
                            <span class="panel-label">PLANETARY NAVIGATION</span>
                            <h3>Mars Surface Map</h3>
                        </div>

                        <span class="live-tag">COORDINATE MODE</span>
                    </div>

                    <div class="route-map-toolbar">
                        <span>
                            <i class="bi bi-crosshair2 cyan"></i>
                            Click the map to add a waypoint
                        </span>

                        <button class="btn-secondary-custom" id="clearRoute">
                            <i class="bi bi-trash"></i>
                            Clear Route
                        </button>
                    </div>

                    <div id="marsRouteMap"></div>

                    <div class="map-footer">
                        <span id="mapCoordinates">Lat: 0.0000° | Lon: 0.0000°</span>
                        <span>Coordinate reference: Planetocentric</span>
                    </div>

                </div>

                <!-- CONTROL PANEL -->

                <div class="panel route-control-panel">

                    <div class="panel-heading">
                        <div>
                            <span class="panel-label">MISSION CONFIGURATION</span>
                            <h3>Route Controls</h3>
                        </div>
                        <i class="bi bi-sliders orange"></i>
                    </div>

                    <div class="route-form-group">
                        <label for="routeName">Mission Name</label>
                        <input type="text" id="routeName"
                               value="Mars Exploration Route"
                               maxlength="80">
                    </div>

                    <div class="route-form-group">
                        <label for="roverSpeed">Rover Speed (km/hr)</label>
                        <input type="number" id="roverSpeed"
                               min="0.1" max="20" step="0.1" value="1">
                        <small>Planning assumption, not measured rover speed.</small>
                    </div>

                    <div class="route-form-group">
                        <label for="knownLocation">Add a reference site</label>
                        <div class="route-site-picker">
                            <select id="knownLocation" aria-label="Choose a known Mars site">
                                <option value="">Select a known Mars site</option>
                            </select>
                            <button type="button" class="btn-secondary-custom" id="addKnownLocation">
                                <i class="bi bi-plus-lg"></i> Add
                            </button>
                        </div>
                    </div>

                    <div class="route-form-group">
                        <label for="startLocation">Starting Location</label>
                        <input type="text" id="startLocation"
                               placeholder="Select first waypoint">
                    </div>

                    <div class="route-form-group">
                        <label for="destinationLocation">Destination</label>
                        <input type="text" id="destinationLocation"
                               placeholder="Select final waypoint">
                    </div>

                    <div class="route-section-title">
                        WAYPOINTS
                        <span id="waypointLabel">0 locations</span>
                    </div>

                    <div id="waypointList" class="waypoint-list">
                        <div class="empty-route">
                            <i class="bi bi-geo-alt"></i>
                            <p>No waypoints selected.</p>
                            <small>Click the Mars map to add locations.</small>
                        </div>
                    </div>

                    <button class="btn-primary-custom route-save-btn"
                            id="saveRoute">
                        <i class="bi bi-floppy"></i>
                        Save Mission Route
                    </button>

                    <button class="btn-secondary-custom route-export-btn"
                            id="exportRoute">
                        <i class="bi bi-download"></i>
                        Export Route CSV
                    </button>

                    <p id="routeMessage" class="route-message" role="status"></p>

                </div>

            </section>

            <!-- HAZARD ANALYSIS -->

            <section class="panel route-hazard-panel">

                <div class="panel-heading">
                    <div>
                        <span class="panel-label">SAFETY MONITORING</span>
                        <h3>Route Hazard Assessment</h3>
                    </div>

                    <span class="live-tag">DATA-BASED CHECK</span>
                </div>

                <div id="hazardResults">
                    <div class="empty-route">
                        Add at least two waypoints to evaluate the route.
                    </div>
                </div>

                <p class="route-disclaimer">
                    Hazard results depend on the coverage and accuracy of
                    available datasets. An absence of reported hazards
                    does not establish that a route is safe.
                </p>

            </section>

            <!-- SAVED ROUTES -->

            <section class="panel saved-route-panel">

                <div class="panel-heading">
                    <div>
                        <span class="panel-label">MISSION ARCHIVE</span>
                        <h3>Saved Routes</h3>
                    </div>
                </div>

                <div id="savedRoutes">
                    <p class="text-secondary">Loading saved routes...</p>
                </div>

            </section>

        </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/route-planner.js"></script>

        <?php include __DIR__ . '/includes/footer.php'; ?>
