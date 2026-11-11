<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';


$pageTitle = "Martian Explorer";
$currentPage = "explorer.php";
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
    <meta name="description" content="Explore Mars terrain imagery and inspect map positions with PROJECT ASTRO.">
    <title>Martian Explorer | PROJECT ASTRO</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
</head>
<body>
<div class="app-layout">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include __DIR__ . '/includes/navbar.php'; ?>

        <div class="dashboard-content explorer-content">
            <section class="welcome-section explorer-welcome">
                <div>
                    <div class="eyebrow"><span class="status-dot"></span> PLANETARY GEOGRAPHIC INFORMATION SYSTEM</div>
                    <h1>Martian Explorer</h1>
                    <p>Navigate available Mars terrain imagery, inspect map positions and save locations for mission analysis.</p>
                </div>
                <a href="https://trek.nasa.gov/mars/" target="_blank" rel="noopener noreferrer" class="btn-secondary-custom">
                    <i class="bi bi-box-arrow-up-right"></i> NASA Mars Trek
                </a>
            </section>

            <section class="explorer-toolbar panel">
                <form class="map-search" id="coordinateSearchForm" novalidate>
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input type="text" id="locationSearch" placeholder="Enter map coordinates: latitude, longitude" aria-label="Search map coordinates" autocomplete="off">
                    <button type="submit" class="btn-primary-custom">Go</button>
                </form>
                <div class="map-toolbar-right">
                    <span class="map-status"><span class="status-dot"></span><span id="mapStatusText">CONNECTING TO MAP SERVICE</span></span>
                    <button type="button" class="icon-button" id="resetMap" title="Reset map view" aria-label="Reset map view"><i class="bi bi-crosshair"></i></button>
                    <button type="button" class="icon-button" id="fullscreenMap" title="Toggle map fullscreen" aria-label="Toggle map fullscreen"><i class="bi bi-fullscreen"></i></button>
                </div>
            </section>
            <p class="explorer-feedback" id="explorerFeedback" role="status" aria-live="polite"></p>

            <section class="explorer-layout">
                <div class="panel map-panel" id="mapPanel">
                    <div class="panel-heading map-heading">
                        <div><span class="panel-label">MARS SURFACE VISUALIZATION</span><h3>Global Terrain Map</h3></div>
                        <span class="map-layer-tag">MARS · ARCGIS IMAGERY</span>
                    </div>
                    <div class="map-control-strip">
                        <label for="layerOpacity">Imagery opacity <strong id="opacityValue">100%</strong></label>
                        <input type="range" id="layerOpacity" min="20" max="100" value="100" step="5" aria-label="Mars imagery opacity">
                        <span class="map-control-hint"><i class="bi bi-mouse"></i> Drag to pan · Scroll to zoom · Click to inspect</span>
                    </div>
                    <div id="marsMap" aria-label="Interactive Mars terrain imagery map"></div>
                    <div class="map-footer">
                        <span><i class="bi bi-info-circle"></i> Selected coordinates represent the current map service position.</span>
                        <span id="mapZoom">ZOOM: —</span>
                    </div>
                </div>

                <aside class="explorer-side">
                    <div class="panel coordinate-panel">
                        <div class="panel-heading"><div><span class="panel-label">POSITION INSPECTOR</span><h3>Selected Location</h3></div><i class="bi bi-crosshair cyan"></i></div>
                        <div class="coordinate-value"><span>MAP LATITUDE</span><strong id="selectedLat">0.000000°</strong></div>
                        <div class="coordinate-value"><span>MAP LONGITUDE</span><strong id="selectedLng">0.000000°</strong></div>
                        <div class="coordinate-actions">
                            <button type="button" id="copyCoordinates" class="btn-secondary-custom"><i class="bi bi-copy"></i> Copy</button>
                            <button type="button" id="saveLocation" class="btn-primary-custom"><i class="bi bi-bookmark-plus"></i> Save location</button>
                        </div>
                        <button type="button" id="useForAssessment" class="btn-secondary-custom w-100 mt-2" disabled>
                            <i class="bi bi-arrow-right-circle"></i> Use coordinates in Landing Zone Analyzer
                        </button>
                        <p class="coordinate-note">These are map-service coordinates, not validated Mars planetographic coordinates. Confirm coordinate reference systems before using a position for scientific or landing-site decisions.</p>
                    </div>

                    <div class="panel layer-panel">
                        <div class="panel-heading"><div><span class="panel-label">MAP CONFIGURATION</span><h3>Active Data Source</h3></div><i class="bi bi-layers cyan"></i></div>
                        <div class="layer-option"><div><strong>Mars Contour Imagery</strong><small>ArcGIS-hosted tiled map service</small></div><span class="layer-active">ACTIVE</span></div>
                        <div class="layer-option"><div><strong>NASA Mars Trek</strong><small>Additional planetary datasets</small></div><a href="https://trek.nasa.gov/mars/" target="_blank" rel="noopener noreferrer">OPEN</a></div>
                        <div class="layer-attribution-note">Only the imagery layer above is loaded in this map. Mars Trek opens as an external resource.</div>
                    </div>

                    <div class="panel saved-panel">
                        <div class="panel-heading"><div><span class="panel-label">LOCAL MISSION BOOKMARKS</span><h3>Saved Locations</h3></div><i class="bi bi-bookmarks cyan"></i></div>
                        <div id="savedLocations" class="saved-locations"><p class="empty-saved">No saved locations yet. Select a position and save it.</p></div>
                        <button type="button" class="saved-clear" id="clearSavedLocations">Clear all saved locations</button>
                    </div>
                </aside>
            </section>

            <section class="panel source-panel">
                <div class="source-icon"><i class="bi bi-database"></i></div>
                <div><strong>Map data attribution</strong><p>Mars Contour Imagery is loaded from the ArcGIS-hosted Mars Contour Imagery MapServer. NASA Mars Trek is linked as a separate planetary data portal. Availability and coverage depend on the external services.</p></div>
                <a href="https://tiles.arcgis.com/tiles/Ezk9fcjSUkeadg6u/arcgis/rest/services/Mars_Contour_Imagery/MapServer" target="_blank" rel="noopener noreferrer">Service details <i class="bi bi-arrow-up-right"></i></a>
            </section>

            <footer class="app-footer"><span>PROJECT ASTRO © 2026</span><span>INTERPLANETARY SURVIVAL GUIDE · MARTIAN MAP</span></footer>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/explorer.js"></script>
</body>
</html>
