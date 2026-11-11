<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';


require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Water & Resources';
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'resources.php');
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
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Explore Martian water-ice evidence, local resources and mission supply requirements in PROJECT ASTRO.">
<title>PROJECT ASTRO | Water &amp; Resources</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css"><link rel="stylesheet" href="assets/css/modules.css">
    <link rel="stylesheet" href="assets/css/theme.css">
</head>
<body>
<div class="app-layout">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<main class="main-content">
<?php include __DIR__ . '/includes/navbar.php'; ?>
<div class="resources-page">
    <section class="resources-hero">
        <div>
            <span class="resource-eyebrow"><i class="bi bi-database-check"></i> RESOURCE INTELLIGENCE / MARS</span>
            <h1>Water &amp; Resources</h1>
            <p>Investigate potential in-situ resources and prepare a supply plan for a future surface mission.</p>
        </div>
        <div class="resource-hero-status"><span class="resource-status-dot"></span><span>PLANNING SYSTEM READY</span></div>
    </section>

    <div class="resource-notice" role="note">
        <i class="bi bi-info-circle-fill"></i>
        <div><strong>Science note</strong><span>Resource indicators are remote-sensing evidence, not proof of a confirmed, accessible or safe-to-use deposit. All inventory quantities and consumption rates entered below are mission-planning estimates.</span></div>
    </div>

    <section class="resource-stat-grid" aria-label="Resource overview">
        <article class="resource-stat-card"><div class="resource-stat-top"><span>RESOURCE CLASSES</span><i class="bi bi-boxes"></i></div><strong>04</strong><small>Tracked in this module</small></article>
        <article class="resource-stat-card"><div class="resource-stat-top"><span>WATER EVIDENCE</span><i class="bi bi-droplet-half"></i></div><strong>Orbital</strong><small>Evidence-based research</small></article>
        <article class="resource-stat-card"><div class="resource-stat-top"><span>SUPPLY INVENTORY</span><i class="bi bi-clipboard-data"></i></div><strong id="inventoryCount">0</strong><small>Items in your local plan</small></article>
        <article class="resource-stat-card"><div class="resource-stat-top"><span>MISSION CHECKLIST</span><i class="bi bi-list-check"></i></div><strong id="checklistProgress">0%</strong><small id="checklistCaption">0 of 5 tasks complete</small></article>
    </section>

    <section class="resource-section-block">
        <div class="resource-section-heading"><div><span class="resource-kicker">01 / IN-SITU RESOURCE CATALOG</span><h2>What can Mars provide?</h2><p>Potential resources and the technologies required to evaluate or use them.</p></div><span class="resource-section-code">ISRU · OVERVIEW</span></div>
        <div class="resource-catalog-grid" id="resourceCatalog" aria-live="polite">
            <div class="resource-loading"><span class="spinner-border spinner-border-sm" role="status"></span> Loading resource catalog…</div>
        </div>
        <p class="resource-source-note" id="catalogStatus">Catalog records are loaded from the local PROJECT ASTRO resource API.</p>
    </section>

    <section class="resource-section-block">
        <div class="resource-section-heading"><div><span class="resource-kicker">02 / WATER-ICE RESEARCH</span><h2>Where could water ice exist?</h2><p>Use orbital observations to shortlist regions for further investigation.</p></div><span class="resource-section-code">SWIM · MRO · ODYSSEY</span></div>
        <div class="water-research-grid">
            <article class="astro-card water-feature-card">
                <div class="water-feature-top"><span class="water-feature-icon"><i class="bi bi-water"></i></span><span class="evidence-label">MULTI-DATASET STUDY</span></div>
                <h3>Subsurface Water Ice Mapping (SWIM)</h3>
                <p>SWIM combines orbital observations and geological indicators to map consistency with near-surface ice. It is a useful research starting point for identifying candidate areas.</p>
                <div class="water-facts"><div><strong>60°S–60°N</strong><small>Study latitude range in published SWIM products</small></div><div><strong>Multiple</strong><small>Remote-sensing evidence types combined</small></div></div>
                <a class="resource-link" href="https://swim.psi.edu/" target="_blank" rel="noopener noreferrer">Explore SWIM project <i class="bi bi-box-arrow-up-right"></i></a>
            </article>
            <article class="astro-card evidence-card">
                <div class="evidence-card-heading"><i class="bi bi-radar"></i><div><h3>How scientists look for ice</h3><span>Evidence is complementary, not interchangeable</span></div></div>
                <div class="evidence-row"><span class="evidence-symbol"><i class="bi bi-broadcast-pin"></i></span><div><strong>SHARAD radar</strong><p>Uses radar echoes to investigate subsurface layers and possible buried ice.</p></div><a href="https://pds-geosciences.wustl.edu/missions/mro/sharad.htm" target="_blank" rel="noopener noreferrer" aria-label="SHARAD data archive"><i class="bi bi-box-arrow-up-right"></i></a></div>
                <div class="evidence-row"><span class="evidence-symbol"><i class="bi bi-thermometer-half"></i></span><div><strong>Thermal observations</strong><p>Surface temperature behavior can help identify terrain consistent with shallow ice.</p></div><a href="https://science.nasa.gov/mission/2001-mars-odyssey/" target="_blank" rel="noopener noreferrer" aria-label="Mars Odyssey mission"><i class="bi bi-box-arrow-up-right"></i></a></div>
                <div class="evidence-row"><span class="evidence-symbol"><i class="bi bi-moisture"></i></span><div><strong>Neutron / hydrogen data</strong><p>Neutron measurements can indicate hydrogen in near-surface materials; hydrogen is not automatically pure water ice.</p></div><a href="https://science.nasa.gov/resource/ice-salt-and-warm-season-flows-on-mars/" target="_blank" rel="noopener noreferrer" aria-label="NASA hydrogen and ice observations"><i class="bi bi-box-arrow-up-right"></i></a></div>
                <div class="evidence-row"><span class="evidence-symbol"><i class="bi bi-camera"></i></span><div><strong>Images and geomorphology</strong><p>Landforms and fresh impact exposures can provide supporting context for ice investigations.</p></div><a href="https://science.nasa.gov/mission/mars-reconnaissance-orbiter/" target="_blank" rel="noopener noreferrer" aria-label="Mars Reconnaissance Orbiter"><i class="bi bi-box-arrow-up-right"></i></a></div>
            </article>
        </div>
        <div class="candidate-region-grid">
            <article class="candidate-region"><span class="candidate-tag">RESEARCH REGION</span><h3>Arcadia Planitia</h3><p>A northern mid-latitude region highlighted in NASA water-ice research as a candidate area for investigating relatively accessible buried ice.</p><a href="https://www.nasa.gov/solar-system/nasas-treasure-map-for-water-ice-on-mars/" target="_blank" rel="noopener noreferrer">Read NASA research <i class="bi bi-arrow-up-right"></i></a></article>
            <article class="candidate-region"><span class="candidate-tag">RESEARCH CONTEXT</span><h3>Mid-latitude terrain</h3><p>SWIM products assess ice-consistency indicators across mid-latitudes. Local conditions, depth, uncertainty and landing safety still need separate evaluation.</p><a href="https://science.nasa.gov/resource/swim-map-shows-subsurface-water-ice-on-mars/" target="_blank" rel="noopener noreferrer">View NASA SWIM overview <i class="bi bi-arrow-up-right"></i></a></article>
        </div>
    </section>

    <section class="resource-section-block">
        <div class="resource-section-heading"><div><span class="resource-kicker">03 / MISSION SUPPLY MANAGEMENT</span><h2>Mission inventory</h2><p>Track planned supplies and update quantities as your mission plan changes.</p></div><span class="resource-section-code">LOCAL PLAN DATA</span></div>
        <div class="inventory-layout">
            <article class="astro-card inventory-form-card">
                <div class="resource-panel-heading"><div><h3 id="inventoryFormTitle">Add inventory item</h3><p>Values are stored in this browser on this device.</p></div><i class="bi bi-box-seam"></i></div>
                <form id="inventoryForm">
                    <input type="hidden" id="inventoryEditId">
                    <div class="resource-form-field"><label for="inventoryName">Supply name</label><input id="inventoryName" class="form-control" maxlength="60" placeholder="e.g. Drinking water" required></div>
                    <div class="resource-form-row"><div class="resource-form-field"><label for="inventoryCategory">Category</label><select id="inventoryCategory" class="form-select"><option value="Water">Water</option><option value="Food">Food</option><option value="Oxygen">Oxygen</option><option value="Power">Power</option><option value="Medical">Medical</option><option value="Equipment">Equipment</option><option value="Other">Other</option></select></div><div class="resource-form-field"><label for="inventoryUnit">Unit</label><select id="inventoryUnit" class="form-select"><option value="L">L</option><option value="kg">kg</option><option value="g">g</option><option value="kWh">kWh</option><option value="units">units</option></select></div></div>
                    <div class="resource-form-row"><div class="resource-form-field"><label for="inventoryQuantity">Available quantity</label><input id="inventoryQuantity" type="number" min="0" step="0.01" class="form-control" placeholder="0" required></div><div class="resource-form-field"><label for="inventoryMinimum">Minimum reserve</label><input id="inventoryMinimum" type="number" min="0" step="0.01" class="form-control" placeholder="0" required></div></div>
                    <div class="resource-form-actions"><button class="btn-primary-custom" type="submit" id="inventorySubmit"><i class="bi bi-plus-lg"></i> Add to inventory</button><button class="btn-secondary-custom" type="button" id="inventoryCancel" hidden>Cancel edit</button></div>
                    <p class="resource-form-feedback" id="inventoryFeedback" aria-live="polite"></p>
                </form>
            </article>
            <article class="astro-card inventory-table-card">
                <div class="resource-panel-heading"><div><h3>Current supplies</h3><p>Low-stock status compares quantity against your minimum reserve.</p></div><span class="inventory-count-pill" id="inventoryTableCount">0 ITEMS</span></div>
                <div class="inventory-table-wrap"><table class="resource-table"><thead><tr><th>SUPPLY</th><th>AVAILABLE</th><th>RESERVE</th><th>STATUS</th><th>ACTIONS</th></tr></thead><tbody id="inventoryTableBody"><tr><td colspan="5" class="inventory-empty">No supplies added yet. Add your first item.</td></tr></tbody></table></div>
                <div class="inventory-footer"><span id="inventorySummary">No inventory records</span><button type="button" class="text-action-button" id="exportInventory"><i class="bi bi-download"></i> Export CSV</button></div>
            </article>
        </div>
    </section>

    <section class="resource-section-block">
        <div class="resource-section-heading"><div><span class="resource-kicker">04 / CREW CONSUMPTION MODEL</span><h2>Mission consumption planner</h2><p>Estimate baseline water and oxygen demand from your mission assumptions.</p></div><span class="resource-section-code">ESTIMATE · NOT TELEMETRY</span></div>
        <div class="consumption-layout">
            <article class="astro-card consumption-input-card">
                <div class="resource-panel-heading"><div><h3>Mission parameters</h3><p>Change the inputs to recalculate the requirement.</p></div><i class="bi bi-sliders"></i></div>
                <div class="resource-form-field"><label for="crewSize">Crew members <strong id="crewSizeValue">4</strong></label><input type="range" id="crewSize" min="1" max="12" value="4" class="form-range"></div>
                <div class="resource-form-field"><label for="missionDays">Mission duration (sols)</label><input type="number" id="missionDays" class="form-control" min="1" max="2000" value="30"></div>
                <div class="resource-form-field"><label for="waterRate">Water allowance (L / person / sol)</label><input type="number" id="waterRate" class="form-control" min="0" max="1000" step="0.1" value="3.5"><small>Editable planning assumption. Actual needs depend on recycling, hygiene, food and mission design.</small></div>
                <div class="resource-form-field"><label for="oxygenRate">Oxygen allowance (kg / person / sol)</label><input type="number" id="oxygenRate" class="form-control" min="0" max="100" step="0.01" value="0.84"><small>Editable planning assumption for a simplified baseline estimate.</small></div>
                <button type="button" class="btn-secondary-custom consumption-reset" id="resetConsumption"><i class="bi bi-arrow-counterclockwise"></i> Reset assumptions</button>
            </article>
            <article class="astro-card consumption-results-card" aria-live="polite">
                <div class="resource-panel-heading"><div><h3>Estimated total demand</h3><p>Calculated from the assumptions on the left.</p></div><span class="estimate-badge">SIMULATED ESTIMATE</span></div>
                <div class="consumption-result-grid"><div class="consumption-result"><span><i class="bi bi-droplet"></i> WATER</span><strong id="waterDemand">420</strong><small>litres for full mission</small></div><div class="consumption-result"><span><i class="bi bi-wind"></i> OXYGEN</span><strong id="oxygenDemand">100.8</strong><small>kilograms for full mission</small></div></div>
                <div class="consumption-equation" id="consumptionEquation">4 crew × 30 sols × 3.5 L = 420 L water</div>
                <div class="consumption-caveat"><i class="bi bi-exclamation-triangle"></i><span>This is a simplified arithmetic estimate, not a NASA life-support standard or a complete closed-loop life-support model. Add contingency margins and account for recycling and losses in a real mission design.</span></div>
            </article>
        </div>
    </section>

    <section class="resource-section-block resource-checklist-section">
        <div class="resource-section-heading"><div><span class="resource-kicker">05 / FIELD OPERATIONS</span><h2>Resource survey checklist</h2><p>Keep track of the steps required before treating a resource as mission-ready.</p></div><span class="resource-section-code">FIELD CHECKLIST</span></div>
        <article class="astro-card checklist-card">
            <div class="checklist-progress-track"><span id="checklistProgressBar"></span></div>
            <div id="resourceChecklist" class="resource-checklist">
                <label><input type="checkbox" value="site"><span><strong>Shortlist a candidate site</strong><small>Use published orbital evidence and landing constraints.</small></span></label>
                <label><input type="checkbox" value="data"><span><strong>Review source datasets</strong><small>Check instrument, resolution, coverage and observation date.</small></span></label>
                <label><input type="checkbox" value="depth"><span><strong>Assess estimated depth and uncertainty</strong><small>Do not assume an orbital indicator means shallow, mineable ice.</small></span></label>
                <label><input type="checkbox" value="access"><span><strong>Evaluate extraction and access risks</strong><small>Consider terrain, power, thermal conditions and equipment.</small></span></label>
                <label><input type="checkbox" value="verify"><span><strong>Plan in-situ verification</strong><small>Require ground measurements before confirming a reserve.</small></span></label>
            </div>
            <div class="checklist-footer"><span id="checklistFooterText">0 of 5 tasks complete</span><button type="button" class="text-action-button" id="resetChecklist">Reset checklist</button></div>
        </article>
    </section>
    <footer class="resource-page-footer"><span>PROJECT ASTRO · RESOURCE INTELLIGENCE</span><span>Scientific references are linked to their source organizations. Planning values are user-defined.</span></footer>
</div>
</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/resources.js"></script>
</body>
</html>
