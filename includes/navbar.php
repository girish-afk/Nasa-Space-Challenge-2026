
<header class="top-navbar">

    <div class="navbar-left">
        <button class="icon-button mobile-menu-btn" id="sidebarToggle" aria-label="Open navigation">
            <i class="bi bi-list"></i>
        </button>

        <div class="breadcrumb-area">
            <span>PROJECT ASTRO</span>
            <i class="bi bi-chevron-right"></i>
            <strong><?= htmlspecialchars($pageTitle ?? 'Mission Dashboard', ENT_QUOTES, 'UTF-8') ?></strong>
        </div>
    </div>

    <div class="navbar-right">

        <div class="system-status">
            <span class="status-dot"></span>
            <span>Local Interface Ready</span>
        </div>

    </div>

</header>
