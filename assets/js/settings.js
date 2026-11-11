
document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const STORAGE_KEY = 'projectAstro.settings.v1';

    const defaults = {
        theme: 'dark',
        accent: 'orange',
        compactMode: false,
        reduceMotion: false,

        measurementUnit: 'metric',
        distanceUnit: 'km',
        temperatureUnit: 'celsius',
        mapDefault: 'standard',

        hazardAlerts: true,
        resourceAlerts: true,
        reportAlerts: true
    };

    const $ = id => document.getElementById(id);

    const controls = {
        compactMode: $('compactMode'),
        reduceMotion: $('reduceMotion'),
        measurementUnit: $('measurementUnit'),
        distanceUnit: $('distanceUnit'),
        temperatureUnit: $('temperatureUnit'),
        mapDefault: $('mapDefault'),
        hazardAlerts: $('hazardAlerts'),
        resourceAlerts: $('resourceAlerts'),
        reportAlerts: $('reportAlerts')
    };

    let settings = { ...defaults };
    let savedSettings = { ...defaults };
    let storageAvailable = true;

    function readSettings() {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);

            if (stored) {
                const parsed = JSON.parse(stored);

                if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
                    settings = { ...defaults, ...parsed };
                }
            }
        } catch (error) {
            storageAvailable = false;
            console.warn('Unable to read saved settings:', error);
        }

        savedSettings = { ...settings };
    }

    function applyAppearance() {
        const root = document.documentElement;

        root.dataset.astroTheme = settings.theme === 'light' ? 'light' : 'dark';
        root.dataset.astroAccent = settings.accent === 'cyan' ? 'cyan' : 'orange';
        root.style.colorScheme = settings.theme === 'light' ? 'light' : 'dark';

        if (settings.accent === 'cyan') {
            root.style.setProperty('--accent', '#50C8E8');
            root.style.setProperty('--accent-hover', '#79D9F0');
        } else {
            root.style.setProperty('--accent', '#E66A45');
            root.style.setProperty('--accent-hover', '#F18460');
        }

        document.body.classList.toggle(
            'settings-compact-mode',
            Boolean(settings.compactMode)
        );

        document.body.classList.toggle(
            'settings-reduced-motion',
            Boolean(settings.reduceMotion)
        );
    }

    function render() {
        Object.entries(controls).forEach(([key, element]) => {
            if (!element) return;

            if (element.type === 'checkbox') {
                element.checked = Boolean(settings[key]);
            } else {
                element.value = settings[key];
            }
        });

        document.querySelectorAll('[data-theme]').forEach(button => {
            const selected = button.dataset.theme === settings.theme;
            button.classList.toggle('active', selected);
            button.setAttribute('aria-pressed', String(selected));
        });

        document.querySelectorAll('[data-accent]').forEach(button => {
            button.classList.toggle(
                'active',
                button.dataset.accent === settings.accent
            );
        });

        $('settingsThemeStatus').textContent = settings.theme === 'light' ? 'Mars Light' : 'Mars Dark';
        $('settingsUnitStatus').textContent =
            settings.measurementUnit === 'imperial' ? 'Imperial' : 'Metric';

        $('settingsStorageStatus').textContent =
            storageAvailable ? 'Local Storage' : 'Storage Unavailable';

        applyAppearance();
    }

    function collectSettings() {
        Object.entries(controls).forEach(([key, element]) => {
            if (!element) return;

            settings[key] = element.type === 'checkbox'
                ? element.checked
                : element.value;
        });
    }

    function showMessage(message, type = 'normal') {
        const messageElement = $('settingsSaveMessage');

        if (!messageElement) return;

        const icons = {
            normal: 'bi-info-circle',
            success: 'bi-check-circle',
            error: 'bi-exclamation-circle'
        };

        messageElement.className = `settings-save-message ${type}`;
        messageElement.innerHTML =
            `<i class="bi ${icons[type] || icons.normal}"></i>`;

        messageElement.append(
            document.createTextNode(' ' + message)
        );
    }

    function markChanged() {
        collectSettings();
        applyAppearance();
        showMessage('You have unsaved changes.');
    }

    function saveSettings() {
        collectSettings();

        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
            savedSettings = { ...settings };
            storageAvailable = true;

            render();
            showMessage('Preferences saved successfully.', 'success');
        } catch (error) {
            storageAvailable = false;
            showMessage(
                'Could not save preferences. Check browser storage permissions.',
                'error'
            );
        }
    }

    function discardChanges() {
        settings = { ...savedSettings };
        render();
        showMessage('Unsaved changes have been discarded.');
    }

    function resetSettings() {
        const confirmed = window.confirm(
            'Reset all settings on this page to their default values? Other mission data will not be deleted.'
        );

        if (!confirmed) return;

        settings = { ...defaults };

        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
            savedSettings = { ...settings };
            storageAvailable = true;

            render();
            showMessage('Settings restored to defaults.', 'success');
        } catch (error) {
            showMessage('Unable to reset browser preferences.', 'error');
        }
    }

    function exportSettings() {
        collectSettings();

        const exportData = {
            application: 'PROJECT ASTRO',
            exportedAt: new Date().toISOString(),
            preferences: settings
        };

        const blob = new Blob(
            [JSON.stringify(exportData, null, 4)],
            { type: 'application/json' }
        );

        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');

        link.href = url;
        link.download = 'project-astro-settings.json';

        document.body.appendChild(link);
        link.click();
        link.remove();

        URL.revokeObjectURL(url);
        showMessage('Settings export downloaded.', 'success');
    }

    // Load preferences
    readSettings();
    render();

    // Detect browser
    const userAgent = navigator.userAgent;

    let browser = 'Unknown Browser';

    if (userAgent.includes('Edg/')) browser = 'Microsoft Edge';
    else if (userAgent.includes('Firefox/')) browser = 'Mozilla Firefox';
    else if (userAgent.includes('Chrome/')) browser = 'Google Chrome';
    else if (userAgent.includes('Safari/')) browser = 'Safari';

    $('browserInfo').textContent = browser;

    // General controls
    Object.values(controls).forEach(element => {
        if (!element) return;

        element.addEventListener('change', markChanged);
    });

    // Accent selection
    document.querySelectorAll('[data-accent]').forEach(button => {
        button.addEventListener('click', () => {
            settings.accent = button.dataset.accent;
            render();
            showMessage('Accent colour changed. Save to keep this preference.');
        });
    });

    // Theme selection
    document.querySelectorAll('[data-theme]').forEach(button => {
        button.addEventListener('click', () => {
            if (button.disabled) return;

            settings.theme = button.dataset.theme;
            collectSettings();

            // Theme choice is persisted immediately so the same appearance is
            // applied when navigating to any other PROJECT ASTRO page.
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
                savedSettings = { ...settings };
                storageAvailable = true;
                render();
                showMessage('Theme saved and applied across PROJECT ASTRO.', 'success');
            } catch (error) {
                render();
                storageAvailable = false;
                showMessage('Theme preview applied, but browser storage could not save it.', 'error');
            }
        });
    });

    // Save / discard / reset / export
    $('saveSettings')?.addEventListener('click', saveSettings);
    $('discardSettings')?.addEventListener('click', discardChanges);
    $('resetSettings')?.addEventListener('click', resetSettings);
    $('exportSettings')?.addEventListener('click', exportSettings);

    // Highlight the current settings section
    const navLinks = document.querySelectorAll('.settings-nav-link');

    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            navLinks.forEach(item => item.classList.remove('active'));
            link.classList.add('active');
        });
    });
});
    