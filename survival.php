<?php

declare(strict_types=1);
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';


require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Survival Planner';
$currentPage = 'survival.php';
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

    <meta name="description" content="Plan crew survival requirements, supplies and emergency reserves for Mars missions.">

    <title>Survival Planner | PROJECT ASTRO</title>

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

        <div class="resources-page survival-page">

            <!-- PAGE HEADER -->

            <section class="resources-hero">

                <div>
                    <span class="resource-eyebrow">
                        <i class="bi bi-heart-pulse"></i>
                        MISSION PLANNING / CREW SAFETY
                    </span>

                    <h1>Mars Survival Planner</h1>

                    <p>
                        Calculate crew requirements, manage essential supplies,
                        estimate emergency reserves and evaluate mission readiness
                        for a human expedition to Mars.
                    </p>
                </div>

                <div class="resource-hero-status">
                    <span class="resource-status-dot"></span>
                    SURVIVAL SYSTEM READY
                </div>

            </section>

            <!-- SCIENCE NOTICE -->

            <div class="resource-notice">
                <i class="bi bi-info-circle-fill"></i>

                <div>
                    <strong>Mission planning disclaimer</strong>

                    <span>
                        Consumption rates are configurable planning assumptions,
                        not official NASA life-support specifications.
                        Actual requirements depend on habitat design, recycling
                        systems, crew health and mission conditions.
                    </span>
                </div>
            </div>

            <!-- OVERVIEW STATISTICS -->

            <section class="resource-stat-grid">

                <article class="resource-stat-card">
                    <div class="resource-stat-top">
                        <span>MISSION DURATION</span>
                        <i class="bi bi-calendar3"></i>
                    </div>

                    <strong id="statDuration">180 Days</strong>
                    <small>Planned surface mission</small>
                </article>

                <article class="resource-stat-card">
                    <div class="resource-stat-top">
                        <span>CREW MEMBERS</span>
                        <i class="bi bi-people"></i>
                    </div>

                    <strong id="statCrew">04</strong>
                    <small>Mission personnel</small>
                </article>

                <article class="resource-stat-card">
                    <div class="resource-stat-top">
                        <span>SUPPLY SHORTAGES</span>
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>

                    <strong id="statShortages">0</strong>
                    <small>Resources below requirement</small>
                </article>

                <article class="resource-stat-card">
                    <div class="resource-stat-top">
                        <span>MISSION READINESS</span>
                        <i class="bi bi-shield-check"></i>
                    </div>

                    <strong id="statReadiness">0%</strong>
                    <small id="readinessCaption">Awaiting calculation</small>
                </article>

            </section>

            <!-- MISSION CONFIGURATION -->

            <section class="resource-section-block">

                <div class="resource-section-heading">

                    <div>
                        <span class="resource-kicker">
                            01 / MISSION CONFIGURATION
                        </span>

                        <h2>Configure your mission</h2>

                        <p>
                            Define the crew, mission duration and reserve margin.
                        </p>
                    </div>

                    <span class="resource-section-code">
                        MISSION PARAMETERS
                    </span>

                </div>

                <div class="survival-config-layout">

                    <article class="inventory-form-card">

                        <div class="resource-panel-heading">
                            <div>
                                <h3>Mission parameters</h3>
                                <p>Change these values to recalculate requirements.</p>
                            </div>

                            <i class="bi bi-sliders"></i>
                        </div>

                        <form id="survivalForm">

                            <div class="resource-form-field">
                                <label for="missionName">Mission name</label>

                                <input
                                    type="text"
                                    id="missionName"
                                    class="form-control"
                                    maxlength="80"
                                    value="Ares Surface Expedition"
                                    required
                                >
                            </div>

                            <div class="resource-form-row">

                                <div class="resource-form-field">
                                    <label for="crewSize">Crew members</label>

                                    <input
                                        type="number"
                                        id="crewSize"
                                        class="form-control"
                                        min="1"
                                        max="20"
                                        value="4"
                                        required
                                    >
                                </div>

                                <div class="resource-form-field">
                                    <label for="missionDays">Mission duration (days)</label>

                                    <input
                                        type="number"
                                        id="missionDays"
                                        class="form-control"
                                        min="1"
                                        max="2000"
                                        value="180"
                                        required
                                    >
                                </div>

                            </div>

                            <div class="resource-form-field">

                                <label for="reservePercent">
                                    Emergency reserve
                                    <strong id="reserveValue">20%</strong>
                                </label>

                                <input
                                    type="range"
                                    id="reservePercent"
                                    class="form-range"
                                    min="0"
                                    max="100"
                                    step="5"
                                    value="20"
                                >

                                <small>
                                    Additional supply margin for unexpected
                                    mission requirements.
                                </small>

                            </div>

                            <div class="resource-form-actions">

                                <button type="submit" class="btn-primary-custom">
                                    <i class="bi bi-calculator"></i>
                                    Calculate Requirements
                                </button>

                                <button
                                    type="button"
                                    id="resetMission"
                                    class="btn-secondary-custom"
                                >
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                    Reset
                                </button>

                            </div>

                            <p id="missionFeedback"
                               class="resource-form-feedback"
                               role="status"
                               aria-live="polite">
                            </p>

                        </form>

                    </article>

                    <!-- CREW CONSUMPTION SETTINGS -->

                    <article class="inventory-form-card">

                        <div class="resource-panel-heading">
                            <div>
                                <h3>Daily crew consumption</h3>
                                <p>Editable estimates per crew member per day.</p>
                            </div>

                            <i class="bi bi-droplet-half"></i>
                        </div>

                        <div class="resource-form-row">

                            <div class="resource-form-field">
                                <label for="waterRate">Water (litres)</label>

                                <input
                                    type="number"
                                    id="waterRate"
                                    class="form-control"
                                    min="0"
                                    max="1000"
                                    step="0.1"
                                    value="3.5"
                                >
                            </div>

                            <div class="resource-form-field">
                                <label for="foodRate">Food (kg)</label>

                                <input
                                    type="number"
                                    id="foodRate"
                                    class="form-control"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    value="1.8"
                                >
                            </div>

                        </div>

                        <div class="resource-form-row">

                            <div class="resource-form-field">
                                <label for="oxygenRate">Oxygen (kg)</label>

                                <input
                                    type="number"
                                    id="oxygenRate"
                                    class="form-control"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    value="0.84"
                                >
                            </div>

                            <div class="resource-form-field">
                                <label for="powerRate">Power (kWh)</label>

                                <input
                                    type="number"
                                    id="powerRate"
                                    class="form-control"
                                    min="0"
                                    max="10000"
                                    step="0.1"
                                    value="5"
                                >
                            </div>

                        </div>

                        <div class="resource-notice survival-small-notice">
                            <i class="bi bi-lightbulb"></i>

                            <div>
                                <strong>Important</strong>

                                <span>
                                    These values are simplified illustrative
                                    planning inputs. Water recycling, oxygen
                                    recovery and shared habitat power are not
                                    modeled automatically.
                                </span>
                            </div>
                        </div>

                    </article>

                </div>

            </section>

            <!-- INVENTORY -->

            <section class="resource-section-block">

                <div class="resource-section-heading">

                    <div>
                        <span class="resource-kicker">
                            02 / RESOURCE INVENTORY
                        </span>

                        <h2>Available mission supplies</h2>

                        <p>
                            Enter the supplies currently available for your mission.
                        </p>
                    </div>

                    <span class="resource-section-code">
                        SUPPLY MANAGEMENT
                    </span>

                </div>

                <article class="inventory-table-card">

                    <div class="survival-inventory-grid">

                        <div class="resource-form-field">
                            <label for="stockWater">Available water (L)</label>

                            <input
                                type="number"
                                id="stockWater"
                                class="form-control"
                                min="0"
                                step="0.1"
                                value="3000"
                            >
                        </div>

                        <div class="resource-form-field">
                            <label for="stockFood">Available food (kg)</label>

                            <input
                                type="number"
                                id="stockFood"
                                class="form-control"
                                min="0"
                                step="0.1"
                                value="1500"
                            >
                        </div>

                        <div class="resource-form-field">
                            <label for="stockOxygen">Available oxygen (kg)</label>

                            <input
                                type="number"
                                id="stockOxygen"
                                class="form-control"
                                min="0"
                                step="0.1"
                                value="700"
                            >
                        </div>

                        <div class="resource-form-field">
                            <label for="stockPower">Available energy (kWh)</label>

                            <input
                                type="number"
                                id="stockPower"
                                class="form-control"
                                min="0"
                                step="0.1"
                                value="4000"
                            >
                        </div>

                    </div>

                    <div class="resource-form-actions">

                        <button type="button"
                                id="calculateSupplies"
                                class="btn-primary-custom">
                            <i class="bi bi-arrow-repeat"></i>
                            Update Supply Assessment
                        </button>

                    </div>

                </article>

            </section>

            <!-- SUPPLY ASSESSMENT -->

            <section class="resource-section-block">

                <div class="resource-section-heading">

                    <div>
                        <span class="resource-kicker">
                            03 / SURVIVAL ANALYSIS
                        </span>

                        <h2>Resource requirement assessment</h2>

                        <p>
                            Calculated requirement includes the selected
                            emergency reserve.
                        </p>
                    </div>

                    <span class="resource-section-code">
                        LIVE CALCULATION
                    </span>

                </div>

                <article class="inventory-table-card">

                    <div class="inventory-table-wrap">

                        <table class="resource-table survival-table">

                            <thead>
                                <tr>
                                    <th>RESOURCE</th>
                                    <th>BASE REQUIREMENT</th>
                                    <th>WITH RESERVE</th>
                                    <th>AVAILABLE</th>
                                    <th>SUPPLY BALANCE</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>

                            <tbody id="supplyResults">

                                <tr>
                                    <td colspan="6" class="inventory-empty">
                                        Configure your mission and calculate
                                        resource requirements.
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                    <div class="survival-summary" id="survivalSummary">
                        <i class="bi bi-info-circle"></i>
                        <span>Run the calculation to see your mission assessment.</span>
                    </div>

                </article>

            </section>

            <!-- MISSION CHECKLIST -->

            <section class="resource-section-block">

                <div class="resource-section-heading">

                    <div>
                        <span class="resource-kicker">
                            04 / CREW PREPARATION
                        </span>

                        <h2>Mission survival checklist</h2>

                        <p>
                            Track essential preparations before the mission begins.
                        </p>
                    </div>

                    <span class="resource-section-code">
                        CREW SAFETY
                    </span>

                </div>

                <article class="checklist-card">

                    <div class="resource-panel-heading">

                        <div>
                            <h3>Mission preparation tasks</h3>
                            <p>Progress is saved automatically in this browser.</p>
                        </div>

                        <span class="inventory-count-pill"
                              id="checklistCounter">
                            0 / 8 COMPLETED
                        </span>

                    </div>

                    <div class="checklist-progress-track">
                        <span id="checklistProgressBar"></span>
                    </div>

                    <div class="resource-checklist survival-checklist"
                         id="survivalChecklist">

                        <label>
                            <input type="checkbox" value="habitat">
                            <span>
                                <strong>Habitat systems verified</strong>
                                <small>Confirm pressure, temperature and life-support systems.</small>
                            </span>
                        </label>

                        <label>
                            <input type="checkbox" value="water">
                            <span>
                                <strong>Water storage and recycling checked</strong>
                                <small>Verify water storage, treatment and recovery systems.</small>
                            </span>
                        </label>

                        <label>
                            <input type="checkbox" value="oxygen">
                            <span>
                                <strong>Oxygen supply verified</strong>
                                <small>Check oxygen storage, monitoring and backup systems.</small>
                            </span>
                        </label>

                        <label>
                            <input type="checkbox" value="food">
                            <span>
                                <strong>Food supply inspected</strong>
                                <small>Verify food quantity, nutrition and storage conditions.</small>
                            </span>
                        </label>

                        <label>
                            <input type="checkbox" value="medical">
                            <span>
                                <strong>Medical equipment prepared</strong>
                                <small>Prepare first aid, medicines and emergency procedures.</small>
                            </span>
                        </label>

                        <label>
                            <input type="checkbox" value="communication">
                            <span>
                                <strong>Communication backup tested</strong>
                                <small>Verify communication equipment and backup procedures.</small>
                            </span>
                        </label>

                        <label>
                            <input type="checkbox" value="radiation">
                            <span>
                                <strong>Radiation protection reviewed</strong>
                                <small>Review shielding and radiation monitoring procedures.</small>
                            </span>
                        </label>

                        <label>
                            <input type="checkbox" value="emergency">
                            <span>
                                <strong>Emergency evacuation plan ready</strong>
                                <small>Review emergency shelter and evacuation procedures.</small>
                            </span>
                        </label>

                    </div>

                    <div class="checklist-footer">
                        <span id="checklistCaption">
                            Complete all tasks before mission departure.
                        </span>

                        <button type="button"
                                id="resetChecklist"
                                class="text-action-button">
                            Reset checklist
                        </button>
                    </div>

                </article>

            </section>

            <!-- MISSION NOTES -->

            <section class="resource-section-block">

                <div class="resource-section-heading">

                    <div>
                        <span class="resource-kicker">
                            05 / MISSION DOCUMENTATION
                        </span>

                        <h2>Mission notes</h2>

                        <p>
                            Record important survival considerations and
                            operational instructions.
                        </p>
                    </div>

                    <span class="resource-section-code">
                        MISSION LOG
                    </span>

                </div>

                <article class="inventory-form-card">

                    <div class="resource-form-field">

                        <label for="missionNotes">
                            Mission observations and emergency instructions
                        </label>

                        <textarea
                            id="missionNotes"
                            class="form-control"
                            rows="5"
                            maxlength="3000"
                            placeholder="Enter mission observations, emergency procedures, supply instructions..."
                        ></textarea>

                    </div>

                    <div class="resource-form-actions">

                        <button type="button"
                                id="saveMission"
                                class="btn-primary-custom">
                            <i class="bi bi-floppy"></i>
                            Save Mission Plan
                        </button>

                        <button type="button"
                                id="exportMission"
                                class="btn-secondary-custom">
                            <i class="bi bi-download"></i>
                            Export Mission JSON
                        </button>

                    </div>

                    <p id="saveFeedback"
                       class="resource-form-feedback"
                       role="status"
                       aria-live="polite">
                    </p>

                </article>

            </section>

            <div class="resource-page-footer">
                <span>PROJECT ASTRO / SURVIVAL PLANNING SYSTEM</span>
                <span>ALL CONSUMPTION FIGURES ARE CONFIGURABLE ESTIMATES</span>
            </div>

        </div>

        <?php include __DIR__ . '/includes/footer.php'; ?>

    </main>

</div>

<script src="assets/js/app.js"></script>
<script src="assets/js/survival.js"></script>

</body>
</html>
