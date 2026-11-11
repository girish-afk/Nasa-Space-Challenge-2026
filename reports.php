<?php

declare(strict_types=1);
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';


require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Mission Reports';
$currentPage = 'reports.php';
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

    <meta name="description" content="PROJECT ASTRO mission reports, planetary exploration analytics and mission documentation.">

    <title>Mission Reports | PROJECT ASTRO</title>

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

        <div class="resources-page reports-page">

            <!-- HEADER -->

            <section class="resources-hero">

                <div>
                    <span class="resource-eyebrow">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        MISSION CONTROL / DOCUMENTATION
                    </span>

                    <h1>Mission Reports & Analytics</h1>

                    <p>
                        Review exploration data, inspect mission readiness,
                        summarize planetary hazards and generate structured
                        mission reports.
                    </p>
                </div>

                <div class="resource-hero-status">
                    <span class="resource-status-dot"></span>
                    REPORTING SYSTEM READY
                </div>

            </section>

            <!-- DATA NOTICE -->

            <div class="resource-notice">
                <i class="bi bi-info-circle-fill"></i>

                <div>
                    <strong>Data transparency</strong>

                    <span>
                        Reports use the data currently available from PROJECT ASTRO
                        modules. Missing integrations are shown as unavailable.
                        Illustrative datasets are identified and are not presented
                        as verified NASA observations.
                    </span>
                </div>
            </div>

            <!-- REPORT ACTIONS -->

            <section class="reports-toolbar">

                <div class="reports-toolbar-copy">
                    <span class="resource-kicker">REPORT GENERATOR</span>
                    <strong id="reportGeneratedAt">Preparing report workspace...</strong>
                    <small id="reportDataStatus">Checking connected project modules</small>
                </div>

                <div class="resource-form-actions reports-actions">

                    <button type="button"
                            id="refreshReport"
                            class="btn-secondary-custom">
                        <i class="bi bi-arrow-clockwise"></i>
                        Refresh data
                    </button>

                    <button type="button"
                            id="printReport"
                            class="btn-secondary-custom">
                        <i class="bi bi-printer"></i>
                        Print / Save PDF
                    </button>

                    <button type="button"
                            id="exportReport"
                            class="btn-primary-custom">
                        <i class="bi bi-file-earmark-arrow-down"></i>
                        Export JSON
                    </button>

                </div>

            </section>

            <!-- SUMMARY STATS -->

            <section class="resource-stat-grid reports-stat-grid">

                <article class="resource-stat-card">
                    <div class="resource-stat-top">
                        <span>SAVED ROVER ROUTES</span>
                        <i class="bi bi-signpost-split"></i>
                    </div>

                    <strong id="reportRouteCount">--</strong>
                    <small>Routes available in route planner</small>
                </article>

                <article class="resource-stat-card">
                    <div class="resource-stat-top">
                        <span>KNOWN HAZARDS</span>
                        <i class="bi bi-shield-exclamation"></i>
                    </div>

                    <strong id="reportHazardCount">--</strong>
                    <small id="reportHazardNote">Hazard dataset status pending</small>
                </article>

                <article class="resource-stat-card">
                    <div class="resource-stat-top">
                        <span>RESOURCE CATEGORIES</span>
                        <i class="bi bi-droplet-half"></i>
                    </div>

                    <strong id="reportResourceCount">--</strong>
                    <small>Catalog entries available</small>
                </article>

                <article class="resource-stat-card">
                    <div class="resource-stat-top">
                        <span>SURVIVAL READINESS</span>
                        <i class="bi bi-heart-pulse"></i>
                    </div>

                    <strong id="reportReadiness">--</strong>
                    <small id="reportReadinessNote">Survival plan not found</small>
                </article>

            </section>

            <!-- MODULE CONNECTION STATUS -->

            <section class="resource-section-block">

                <div class="resource-section-heading">

                    <div>
                        <span class="resource-kicker">01 / DATA INTEGRATION</span>

                        <h2>Connected module overview</h2>

                        <p>
                            Current availability of the project's data sources.
                        </p>
                    </div>

                    <span class="resource-section-code">
                        MODULE STATUS
                    </span>

                </div>

                <div class="reports-module-grid" id="moduleStatusGrid">

                    <div class="resource-loading">
                        <i class="bi bi-arrow-repeat"></i>
                        Checking project modules...
                    </div>

                </div>

            </section>

            <!-- ANALYTICS -->

            <section class="resource-section-block">

                <div class="resource-section-heading">

                    <div>
                        <span class="resource-kicker">02 / MISSION ANALYTICS</span>

                        <h2>Exploration analytics</h2>

                        <p>
                            Visual summaries of currently available project data.
                        </p>
                    </div>

                    <span class="resource-section-code">
                        DATA VISUALIZATION
                    </span>

                </div>

                <div class="reports-chart-grid">

                    <article class="inventory-form-card reports-chart-card">

                        <div class="resource-panel-heading">
                            <div>
                                <h3>Hazard severity distribution</h3>
                                <p>Count of records grouped by reported severity.</p>
                            </div>

                            <i class="bi bi-pie-chart"></i>
                        </div>

                        <div class="reports-chart-wrap">
                            <canvas id="hazardChart"
                                    role="img"
                                    aria-label="Chart showing hazard severity distribution">
                            </canvas>
                        </div>

                        <p class="reports-chart-caption" id="hazardChartCaption">
                            Waiting for hazard data.
                        </p>

                    </article>

                    <article class="inventory-form-card reports-chart-card">

                        <div class="resource-panel-heading">
                            <div>
                                <h3>Survival supply requirements</h3>
                                <p>Estimated base requirements and available inventory.</p>
                            </div>

                            <i class="bi bi-bar-chart"></i>
                        </div>

                        <div class="reports-chart-wrap">
                            <canvas id="supplyChart"
                                    role="img"
                                    aria-label="Chart comparing required and available survival supplies">
                            </canvas>
                        </div>

                        <p class="reports-chart-caption" id="supplyChartCaption">
                            Waiting for survival plan.
                        </p>

                    </article>

                </div>

            </section>

            <!-- ROUTE REPORTS -->

            <section class="resource-section-block">

                <div class="resource-section-heading">

                    <div>
                        <span class="resource-kicker">03 / ROVER EXPLORATION</span>

                        <h2>Saved mission routes</h2>

                        <p>
                            Inspect saved route names, waypoint counts and
                            route information.
                        </p>
                    </div>

                    <span class="resource-section-code">
                        ROUTE SUMMARY
                    </span>

                </div>

                <article class="inventory-table-card">

                    <div class="inventory-table-wrap">

                        <table class="resource-table reports-table">

                            <thead>
                                <tr>
                                    <th>MISSION ROUTE</th>
                                    <th>WAYPOINTS</th>
                                    <th>CREATED</th>
                                    <th>ROUTE STATUS</th>
                                </tr>
                            </thead>

                            <tbody id="routeReportBody">

                                <tr>
                                    <td colspan="4" class="inventory-empty">
                                        Loading saved routes...
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </article>

            </section>

            <!-- HAZARD REPORT -->

            <section class="resource-section-block">

                <div class="resource-section-heading">

                    <div>
                        <span class="resource-kicker">04 / PLANETARY SAFETY</span>

                        <h2>Hazard observation report</h2>

                        <p>
                            Review hazard records returned by the Hazard Detection module.
                        </p>
                    </div>

                    <span class="resource-section-code">
                        SAFETY SUMMARY
                    </span>

                </div>

                <article class="inventory-table-card">

                    <div class="inventory-table-wrap">

                        <table class="resource-table reports-table">

                            <thead>
                                <tr>
                                    <th>HAZARD ID</th>
                                    <th>OBSERVATION</th>
                                    <th>LATITUDE</th>
                                    <th>LONGITUDE</th>
                                    <th>SEVERITY</th>
                                </tr>
                            </thead>

                            <tbody id="hazardReportBody">

                                <tr>
                                    <td colspan="5" class="inventory-empty">
                                        Loading hazard observations...
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                    <p class="reports-data-note" id="hazardDataNotice">
                        Hazard dataset information will appear here.
                    </p>

                </article>

            </section>

            <!-- SURVIVAL REPORT -->

            <section class="resource-section-block">

                <div class="resource-section-heading">

                    <div>
                        <span class="resource-kicker">05 / CREW SURVIVAL</span>

                        <h2>Survival assessment</h2>

                        <p>
                            Resource estimates from the saved Survival Planner configuration.
                        </p>
                    </div>

                    <span class="resource-section-code">
                        RESOURCE REPORT
                    </span>

                </div>

                <article class="inventory-table-card">

                    <div class="reports-survival-heading">

                        <div>
                            <strong id="survivalMissionName">
                                No saved survival mission
                            </strong>

                            <small id="survivalMissionDetails">
                                Configure and save a mission in Survival Planner.
                            </small>
                        </div>

                        <span id="survivalReportStatus"
                              class="resource-status-pill warning">
                            NOT AVAILABLE
                        </span>

                    </div>

                    <div class="inventory-table-wrap">

                        <table class="resource-table reports-table">

                            <thead>
                                <tr>
                                    <th>RESOURCE</th>
                                    <th>REQUIRED</th>
                                    <th>AVAILABLE</th>
                                    <th>BALANCE</th>
                                    <th>ASSESSMENT</th>
                                </tr>
                            </thead>

                            <tbody id="survivalReportBody">

                                <tr>
                                    <td colspan="5" class="inventory-empty">
                                        No saved survival assessment available.
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </article>

            </section>

            <!-- RESOURCE CATALOG -->

            <section class="resource-section-block">

                <div class="resource-section-heading">

                    <div>
                        <span class="resource-kicker">06 / PLANETARY RESOURCES</span>

                        <h2>Resource catalog summary</h2>

                        <p>
                            Reference categories from the Water & Resources module.
                        </p>
                    </div>

                    <span class="resource-section-code">
                        RESOURCE REFERENCES
                    </span>

                </div>

                <div class="reports-resource-grid" id="resourceReportGrid">

                    <div class="resource-loading">
                        <i class="bi bi-arrow-repeat"></i>
                        Loading resource catalog...
                    </div>

                </div>

            </section>

            <!-- REPORT ARCHIVE -->

            <section class="resource-section-block">

                <div class="resource-section-heading">

                    <div>
                        <span class="resource-kicker">07 / REPORT ARCHIVE</span>

                        <h2>Generated reports</h2>

                        <p>
                            Previously generated report snapshots saved in this browser.
                        </p>
                    </div>

                    <span class="resource-section-code">
                        LOCAL ARCHIVE
                    </span>

                </div>

                <article class="inventory-table-card">

                    <div class="reports-archive-toolbar">

                        <span class="inventory-count-pill"
                              id="archiveCount">
                            0 REPORTS
                        </span>

                        <button type="button"
                                id="clearReportArchive"
                                class="text-action-button">
                            Clear archive
                        </button>

                    </div>

                    <div class="inventory-table-wrap">

                        <table class="resource-table reports-table">

                            <thead>
                                <tr>
                                    <th>REPORT ID</th>
                                    <th>GENERATED</th>
                                    <th>MISSION ROUTES</th>
                                    <th>HAZARDS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>

                            <tbody id="reportArchiveBody">

                                <tr>
                                    <td colspan="5" class="inventory-empty">
                                        No reports generated yet.
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </article>

            </section>

            <!-- REPORT FOOTER -->

            <div class="resource-page-footer">
                <span>PROJECT ASTRO / MISSION REPORTING SYSTEM</span>
                <span>REPORT SNAPSHOTS ARE STORED LOCALLY IN THIS BROWSER</span>
            </div>

        </div>

        <?php include __DIR__ . '/includes/footer.php'; ?>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/reports.js"></script>

</body>
</html>
