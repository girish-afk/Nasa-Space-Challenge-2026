<?php

declare(strict_types=1);
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';


require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'System Settings';
$currentPage = 'settings.php';
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
    <meta name="description" content="Configure PROJECT ASTRO mission console preferences.">

    <title>PROJECT ASTRO | System Settings</title>

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

        <div class="module-page-content settings-page">

            <!-- PAGE HEADER -->
            <section class="module-hero settings-hero">
                <div class="module-hero-icon">
                    <i class="bi bi-sliders"></i>
                </div>

                <div>
                    <span class="page-eyebrow">MISSION CONTROL / CONFIGURATION</span>
                    <h1>System Settings</h1>
                    <p>Configure your mission console, exploration preferences and interface behaviour.</p>
                </div>

                <div class="settings-header-status">
                    <span class="status-dot"></span>
                    LOCAL CONFIGURATION
                </div>
            </section>

            <!-- SETTINGS STATUS -->
            <section class="settings-overview">

                <article class="astro-card settings-overview-card">
                    <div class="settings-overview-icon orange">
                        <i class="bi bi-display"></i>
                    </div>
                    <div>
                        <span>INTERFACE</span>
                        <strong id="settingsThemeStatus">Mars Dark</strong>
                        <small>Mission console appearance</small>
                    </div>
                </article>

                <article class="astro-card settings-overview-card">
                    <div class="settings-overview-icon cyan">
                        <i class="bi bi-globe2"></i>
                    </div>
                    <div>
                        <span>MEASUREMENT</span>
                        <strong id="settingsUnitStatus">Metric</strong>
                        <small>Exploration measurement units</small>
                    </div>
                </article>

                <article class="astro-card settings-overview-card">
                    <div class="settings-overview-icon green">
                        <i class="bi bi-hdd"></i>
                    </div>
                    <div>
                        <span>PREFERENCES</span>
                        <strong id="settingsStorageStatus">Local Storage</strong>
                        <small>Saved in this browser</small>
                    </div>
                </article>

            </section>

            <div class="settings-layout">

                <!-- SETTINGS NAVIGATION -->
                <aside class="astro-card settings-navigation">
                    <span class="settings-nav-heading">CONFIGURATION</span>

                    <a href="#appearanceSettings" class="settings-nav-link active">
                        <i class="bi bi-palette"></i>
                        <span>Appearance</span>
                    </a>

                    <a href="#missionSettings" class="settings-nav-link">
                        <i class="bi bi-rocket-takeoff"></i>
                        <span>Mission Preferences</span>
                    </a>

                    <a href="#alertSettings" class="settings-nav-link">
                        <i class="bi bi-bell"></i>
                        <span>Notifications</span>
                    </a>

                    <a href="#dataSettings" class="settings-nav-link">
                        <i class="bi bi-database"></i>
                        <span>Data & Privacy</span>
                    </a>

                    <a href="#systemSettings" class="settings-nav-link">
                        <i class="bi bi-cpu"></i>
                        <span>System Information</span>
                    </a>

                    <div class="settings-nav-note">
                        <i class="bi bi-shield-check"></i>
                        <span>Your preferences are stored locally in this browser.</span>
                    </div>
                </aside>

                <!-- SETTINGS CONTENT -->
                <div class="settings-content">

                    <!-- APPEARANCE -->
                    <section class="astro-card settings-panel" id="appearanceSettings">

                        <div class="settings-panel-heading">
                            <div>
                                <span class="settings-section-label">INTERFACE</span>
                                <h2>Appearance</h2>
                                <p>Customize the look and behaviour of your mission console.</p>
                            </div>

                            <div class="settings-panel-icon">
                                <i class="bi bi-palette2"></i>
                            </div>
                        </div>

                        <div class="settings-option">
                            <div class="settings-option-info">
                                <strong>Interface theme</strong>
                                <small>Choose the visual theme for the mission console.</small>
                            </div>

                            <div class="settings-choice-group" id="themeChoices">
                                <button type="button" class="settings-choice active" data-theme="dark">
                                    <i class="bi bi-moon-stars"></i>
                                    Dark
                                </button>

                                <button type="button" class="settings-choice" data-theme="light" aria-pressed="false">
                                    <i class="bi bi-sun"></i>
                                    Light
                                </button>
                            </div>
                        </div>

                        <div class="settings-divider"></div>

                        <div class="settings-option">
                            <div class="settings-option-info">
                                <strong>Accent colour</strong>
                                <small>Choose the highlight colour used across the interface.</small>
                            </div>

                            <div class="settings-accent-options">
                                <button type="button" class="accent-swatch active" data-accent="orange" aria-label="Mars orange">
                                    <span class="accent-orange"></span>
                                    <small>Mars</small>
                                </button>

                                <button type="button" class="accent-swatch" data-accent="cyan" aria-label="Mission cyan">
                                    <span class="accent-cyan"></span>
                                    <small>Cyan</small>
                                </button>
                            </div>
                        </div>

                        <div class="settings-divider"></div>

                        <div class="settings-option">
                            <div class="settings-option-info">
                                <strong>Compact interface</strong>
                                <small>Reduce spacing to fit more information on the screen.</small>
                            </div>

                            <label class="settings-switch">
                                <input type="checkbox" id="compactMode">
                                <span class="settings-switch-slider"></span>
                            </label>
                        </div>

                        <div class="settings-divider"></div>

                        <div class="settings-option">
                            <div class="settings-option-info">
                                <strong>Reduce animations</strong>
                                <small>Minimize interface animations and transitions.</small>
                            </div>

                            <label class="settings-switch">
                                <input type="checkbox" id="reduceMotion">
                                <span class="settings-switch-slider"></span>
                            </label>
                        </div>

                    </section>

                    <!-- MISSION PREFERENCES -->
                    <section class="astro-card settings-panel" id="missionSettings">

                        <div class="settings-panel-heading">
                            <div>
                                <span class="settings-section-label">EXPLORATION CONFIGURATION</span>
                                <h2>Mission Preferences</h2>
                                <p>Set your preferred units and exploration defaults.</p>
                            </div>

                            <div class="settings-panel-icon">
                                <i class="bi bi-rocket-takeoff"></i>
                            </div>
                        </div>

                        <div class="settings-form-grid">

                            <div class="settings-form-field">
                                <label for="measurementUnit">Measurement system</label>
                                <select id="measurementUnit" class="form-select">
                                    <option value="metric">Metric (km, kg, °C)</option>
                                    <option value="imperial">Imperial (mi, lb, °F)</option>
                                </select>
                                <small>Used as your preferred display unit.</small>
                            </div>

                            <div class="settings-form-field">
                                <label for="distanceUnit">Distance display</label>
                                <select id="distanceUnit" class="form-select">
                                    <option value="km">Kilometres (km)</option>
                                    <option value="mi">Miles (mi)</option>
                                </select>
                                <small>Preferred distance unit for exploration.</small>
                            </div>

                            <div class="settings-form-field">
                                <label for="temperatureUnit">Temperature display</label>
                                <select id="temperatureUnit" class="form-select">
                                    <option value="celsius">Celsius (°C)</option>
                                    <option value="fahrenheit">Fahrenheit (°F)</option>
                                </select>
                                <small>Preferred temperature display.</small>
                            </div>

                            <div class="settings-form-field">
                                <label for="mapDefault">Default map view</label>
                                <select id="mapDefault" class="form-select">
                                    <option value="standard">Standard exploration map</option>
                                    <option value="satellite">Satellite / imagery view</option>
                                </select>
                                <small>Preferred starting map layer where supported.</small>
                            </div>

                        </div>

                        <div class="settings-info-message">
                            <i class="bi bi-info-circle"></i>
                            <span>These preferences are saved here. Individual modules must read them before their displayed units or map layers change.</span>
                        </div>

                    </section>

                    <!-- NOTIFICATIONS -->
                    <section class="astro-card settings-panel" id="alertSettings">

                        <div class="settings-panel-heading">
                            <div>
                                <span class="settings-section-label">MISSION ALERTS</span>
                                <h2>Notifications</h2>
                                <p>Choose which interface reminders you want to enable.</p>
                            </div>

                            <div class="settings-panel-icon">
                                <i class="bi bi-bell"></i>
                            </div>
                        </div>

                        <div class="settings-option">
                            <div class="settings-option-info">
                                <strong>Hazard reminders</strong>
                                <small>Enable in-page reminders about hazard information.</small>
                            </div>

                            <label class="settings-switch">
                                <input type="checkbox" id="hazardAlerts">
                                <span class="settings-switch-slider"></span>
                            </label>
                        </div>

                        <div class="settings-divider"></div>

                        <div class="settings-option">
                            <div class="settings-option-info">
                                <strong>Resource reminders</strong>
                                <small>Enable reminders when reviewing resource planning.</small>
                            </div>

                            <label class="settings-switch">
                                <input type="checkbox" id="resourceAlerts">
                                <span class="settings-switch-slider"></span>
                            </label>
                        </div>

                        <div class="settings-divider"></div>

                        <div class="settings-option">
                            <div class="settings-option-info">
                                <strong>Mission report reminders</strong>
                                <small>Enable reminders to review your saved mission reports.</small>
                            </div>

                            <label class="settings-switch">
                                <input type="checkbox" id="reportAlerts">
                                <span class="settings-switch-slider"></span>
                            </label>
                        </div>

                        <p class="settings-footnote">
                            <i class="bi bi-info-circle"></i>
                            These are preference switches only. This version does not send background or push notifications.
                        </p>

                    </section>

                    <!-- DATA & PRIVACY -->
                    <section class="astro-card settings-panel" id="dataSettings">

                        <div class="settings-panel-heading">
                            <div>
                                <span class="settings-section-label">LOCAL DATA MANAGEMENT</span>
                                <h2>Data & Privacy</h2>
                                <p>Manage the preferences stored by this browser.</p>
                            </div>

                            <div class="settings-panel-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>

                        <div class="settings-data-row">
                            <div class="settings-data-icon">
                                <i class="bi bi-file-earmark-arrow-down"></i>
                            </div>

                            <div class="settings-option-info">
                                <strong>Export preferences</strong>
                                <small>Download your current settings as a JSON file.</small>
                            </div>

                            <button type="button" class="settings-secondary-button" id="exportSettings">
                                <i class="bi bi-download"></i>
                                Export
                            </button>
                        </div>

                        <div class="settings-divider"></div>

                        <div class="settings-data-row">
                            <div class="settings-data-icon danger">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </div>

                            <div class="settings-option-info">
                                <strong>Reset preferences</strong>
                                <small>Restore this page's settings to their default values.</small>
                            </div>

                            <button type="button" class="settings-danger-button" id="resetSettings">
                                <i class="bi bi-arrow-repeat"></i>
                                Reset
                            </button>
                        </div>

                        <div class="settings-privacy-note">
                            <i class="bi bi-shield-check"></i>
                            <div>
                                <strong>Local storage notice</strong>
                                <p>Settings are stored in your browser using local storage. They are not uploaded to a server by this page. Resetting preferences does not delete your mission routes, reports or survival plans.</p>
                            </div>
                        </div>

                    </section>

                    <!-- SYSTEM INFORMATION -->
                    <section class="astro-card settings-panel" id="systemSettings">

                        <div class="settings-panel-heading">
                            <div>
                                <span class="settings-section-label">CONSOLE DIAGNOSTICS</span>
                                <h2>System Information</h2>
                                <p>Basic information about this PROJECT ASTRO interface.</p>
                            </div>

                            <div class="settings-panel-icon">
                                <i class="bi bi-cpu"></i>
                            </div>
                        </div>

                        <div class="settings-system-grid">

                            <div class="settings-system-item">
                                <span>APPLICATION</span>
                                <strong>PROJECT ASTRO</strong>
                            </div>

                            <div class="settings-system-item">
                                <span>VERSION</span>
                                <strong><?= e(APP_VERSION) ?></strong>
                            </div>

                            <div class="settings-system-item">
                                <span>INTERFACE</span>
                                <strong>PHP / Bootstrap 5</strong>
                            </div>

                            <div class="settings-system-item">
                                <span>STORAGE</span>
                                <strong>Browser Local Storage</strong>
                            </div>

                            <div class="settings-system-item">
                                <span>SERVER TIME</span>
                                <strong><?= e(date('d M Y, H:i')) ?> UTC</strong>
                            </div>

                            <div class="settings-system-item">
                                <span>BROWSER</span>
                                <strong id="browserInfo">Detecting...</strong>
                            </div>

                        </div>

                    </section>

                    <!-- SAVE BAR -->
                    <div class="settings-save-bar">
                        <div class="settings-save-message" id="settingsSaveMessage" aria-live="polite">
                            <i class="bi bi-check-circle"></i>
                            Your changes are not saved yet.
                        </div>

                        <div class="settings-save-actions">
                            <button type="button" class="settings-secondary-button" id="discardSettings">
                                Discard
                            </button>

                            <button type="button" class="settings-save-button" id="saveSettings">
                                <i class="bi bi-check2"></i>
                                Save Preferences
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/settings.js"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
