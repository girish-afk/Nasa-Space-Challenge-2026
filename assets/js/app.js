/*
 * PROJECT ASTRO - Global interface behaviour
 * Applies saved appearance preferences on every application page.
 */
(function () {
    'use strict';

    const SETTINGS_KEY = 'projectAstro.settings.v1';
    const PROFILE_KEY = 'projectAstro.commanderProfile.v1';

    function applySavedPreferences() {
        let preferences = {};

        try {
            const saved = localStorage.getItem(SETTINGS_KEY);
            if (saved) {
                const parsed = JSON.parse(saved);
                if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
                    preferences = parsed;
                }
            }
        } catch (error) {
            console.warn('PROJECT ASTRO preferences could not be loaded.', error);
        }

        const root = document.documentElement;
        const body = document.body;

        const theme = preferences.theme === 'light' ? 'light' : 'dark';
        const accent = preferences.accent === 'cyan' ? 'cyan' : 'orange';

        root.dataset.astroTheme = theme;
        root.dataset.astroAccent = accent;
        root.style.colorScheme = theme;

        if (accent === 'cyan') {
            root.style.setProperty('--accent', '#50C8E8');
            root.style.setProperty('--accent-hover', '#79D9F0');
        } else {
            root.style.setProperty('--accent', '#E66A45');
            root.style.setProperty('--accent-hover', '#F18460');
        }

        if (body) {
            body.classList.toggle('settings-compact-mode', Boolean(preferences.compactMode));
            body.classList.toggle('settings-reduced-motion', Boolean(preferences.reduceMotion));
        }
    }

    function applyCommanderIdentity(profile) {
        const name = (profile && typeof profile.name === 'string' && profile.name.trim())
            ? profile.name.trim() : 'Mission Commander';
        const role = (profile && typeof profile.role === 'string' && profile.role.trim())
            ? profile.role.trim() : 'Mission Commander';
        const parts = name.split(/\s+/).filter(Boolean);
        const initials = parts.length > 1
            ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
            : (parts[0] || 'MC').slice(0, 2).toUpperCase();

        const nameNode = document.getElementById('navbarProfileName');
        const roleNode = document.getElementById('navbarProfileRole');
        const avatarNode = document.getElementById('navbarProfileAvatar');
        if (nameNode) nameNode.textContent = name;
        if (roleNode) roleNode.textContent = role.toUpperCase();
        if (avatarNode) avatarNode.textContent = initials;
    }

    function loadCommanderIdentity() {
        try {
            const profile = JSON.parse(localStorage.getItem(PROFILE_KEY) || '{}');
            applyCommanderIdentity(profile);
        } catch (error) {
            applyCommanderIdentity(null);
        }
    }

    // Scripts are loaded at the end of the page, so body is available here.
    applySavedPreferences();
    loadCommanderIdentity();
    document.addEventListener('astro:profile-updated', event => {
        applyCommanderIdentity(event.detail || null);
    });

    // Keep already-open PROJECT ASTRO tabs in sync with saved preferences and profile.
    window.addEventListener('storage', event => {
        if (event.key === SETTINGS_KEY) applySavedPreferences();
        if (event.key === PROFILE_KEY) loadCommanderIdentity();
    });

    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        const close = document.getElementById('sidebarClose');
        const backdrop = document.getElementById('sidebarBackdrop');

        function openSidebar() {
            sidebar?.classList.add('open');
            backdrop?.classList.add('show');
        }

        function closeSidebar() {
            sidebar?.classList.remove('open');
            backdrop?.classList.remove('show');
        }

        toggle?.addEventListener('click', openSidebar);
        close?.addEventListener('click', closeSidebar);
        backdrop?.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeSidebar();
        });
    });
})();
