
<aside class="sidebar" id="sidebar">

    <div class="brand">
        <div class="brand-icon"><i class="bi bi-globe-americas"></i></div>
        <div>
            <strong>PROJECT <span>ASTRO</span></strong>
            <small>PLANETARY EXPLORATION</small>
        </div>
        <button class="sidebar-close" id="sidebarClose" aria-label="Close sidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="sidebar-nav">

        <span class="nav-section-title">OVERVIEW</span>

        <a href="index.php" class="nav-link-item">
            <i class="bi bi-house"></i>
            <span>Home</span>
        </a>

        <a href="dashboard.php" class="nav-link-item <?= (($currentPage ?? '') === 'dashboard.php') ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2"></i>
            <span>Mission Dashboard</span>
        </a>

        <span class="nav-section-title">PLANETARY EXPLORATION</span>

        <a href="explorer.php" class="nav-link-item <?= (($currentPage ?? '') === 'explorer.php') ? 'active' : '' ?>">
            <i class="bi bi-globe"></i>
            <span>Martian Explorer</span>
        </a>

        <a href="landing-zone.php" class="nav-link-item <?= (($currentPage ?? '') === 'landing-zone.php') ? 'active' : '' ?>">
            <i class="bi bi-bullseye"></i>
            <span>Landing Zone Analyzer</span>
        </a>

        <a href="landing-comparison.php" class="nav-link-item <?= (($currentPage ?? '') === 'landing-comparison.php') ? 'active' : '' ?>">
            <i class="bi bi-bar-chart-line"></i>
            <span>Landing Site Comparison</span>
        </a>

        <a href="hazards.php" class="nav-link-item <?= (($currentPage ?? '') === 'hazards.php') ? 'active' : '' ?>">
            <i class="bi bi-shield-exclamation"></i>
            <span>Hazard Detection</span>
        </a>

        <a href="weather.php" class="nav-link-item <?= (($currentPage ?? '') === 'weather.php') ? 'active' : '' ?>">
            <i class="bi bi-cloud-haze"></i>
            <span>Weather & Environment</span>
        </a>

        <a href="resources.php" class="nav-link-item <?= (($currentPage ?? '') === 'resources.php') ? 'active' : '' ?>">
            <i class="bi bi-droplet"></i>
            <span>Water & Resources</span>
        </a>

        <span class="nav-section-title">MISSION PLANNING</span>

        <a href="route-planner.php" class="nav-link-item <?= (($currentPage ?? '') === 'route-planner.php') ? 'active' : '' ?>">
            <i class="bi bi-signpost-split"></i>
            <span>Rover Route Planner</span>
        </a>

        <a href="survival.php" class="nav-link-item <?= (($currentPage ?? '') === 'survival.php') ? 'active' : '' ?>">
            <i class="bi bi-heart-pulse"></i>
            <span>Survival Planner</span>
        </a>

        <a href="reports.php" class="nav-link-item <?= (($currentPage ?? '') === 'reports.php') ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-text"></i>
            <span>Mission Reports</span>
        </a>

        <span class="nav-section-title">SYSTEM</span>

        <a href="settings.php" class="nav-link-item <?= (($currentPage ?? '') === 'settings.php') ? 'active' : '' ?>">
            <i class="bi bi-gear"></i>
            <span>Settings</span>
        </a>

    </nav>

    <div class="sidebar-bottom">
        <div class="mission-card">
            <span class="mission-dot"></span>
            <div>
                <strong>MISSION SYSTEM</strong>
                <small>LOCAL INTERFACE ACTIVE</small>
            </div>
        </div>
        <div class="sidebar-version">PROJECT ASTRO · V1.0</div>
    </div>

</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
