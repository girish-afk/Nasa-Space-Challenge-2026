
/* =========================================
   PROJECT ASTRO
   PHASE 4.2 — HAZARD DETECTION ENGINE
   Updated & Improved Version
========================================= */

document.addEventListener("DOMContentLoaded", () => {

    "use strict";

    const $ = id => document.getElementById(id);

    // -------------------------------------
    // CONFIGURATION
    // -------------------------------------

    const STORAGE_KEY = "projectAstro.hazards.v1";
    const SAFE_KEY = "projectAstro.safeCandidates.v1";

    const TYPES = {
        crater: "Impact Crater",
        rock: "Rock Field",
        boulder: "Large Boulder",
        slope: "Steep Slope",
        other: "Other Obstruction"
    };

    const SEVERITIES = {
        low: "Low",
        moderate: "Moderate",
        high: "High",
        critical: "Critical"
    };

    const MAX_HAZARDS = 500;
    const MAX_CANDIDATES = 100;

    // -------------------------------------
    // STATE
    // -------------------------------------

    let hazards = loadData(STORAGE_KEY);
    let safeCandidates = loadData(SAFE_KEY);

    let selectedCoordinates = {
        lat: 0,
        lng: 0
    };

    let map = null;
    let hazardLayer = null;
    let safeLayer = null;

    let lastSlopeAnalysis = null;

    // -------------------------------------
    // STORAGE
    // -------------------------------------

    function loadData(key) {

        try {

            const parsed = JSON.parse(
                localStorage.getItem(key) || "[]"
            );

            return Array.isArray(parsed) ? parsed : [];

        } catch (error) {

            console.error("Storage loading error:", error);

            return [];

        }

    }

    function saveData(key, data) {

        try {

            localStorage.setItem(
                key,
                JSON.stringify(data)
            );

            return true;

        } catch (error) {

            console.error("Storage saving error:", error);

            feedback(
                "Browser storage is unavailable or full.",
                true
            );

            return false;

        }

    }

    // -------------------------------------
    // UTILITIES
    // -------------------------------------

    function validCoordinates(lat, lng) {

        return Number.isFinite(lat) &&
            Number.isFinite(lng) &&
            lat >= -90 &&
            lat <= 90 &&
            lng >= -180 &&
            lng <= 180;

    }

    // Great-circle distance using the approximate mean radius of Mars.
    // Valid only when the supplied map coordinates are planetary lat/lon.
    function marsSurfaceDistanceKm(lat1, lng1, lat2, lng2) {

        const radiusKm = 3390;
        const radians = value => value * Math.PI / 180;
        const dLat = radians(lat2 - lat1);
        const dLng = radians(lng2 - lng1);
        const a = Math.sin(dLat / 2) ** 2 +
            Math.cos(radians(lat1)) * Math.cos(radians(lat2)) *
            Math.sin(dLng / 2) ** 2;

        return 2 * radiusKm * Math.atan2(
            Math.sqrt(a),
            Math.sqrt(Math.max(0, 1 - a))
        );

    }

    function makeId() {

        if (window.crypto?.randomUUID) {
            return window.crypto.randomUUID();
        }

        return `${Date.now()}-${Math.random()
            .toString(36).slice(2)}`;

    }

    function escapeHTML(value) {

        return String(value ?? "").replace(
            /[&<>"']/g,
            character => ({
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#039;"
            })[character]
        );

    }

    // -------------------------------------
    // FEEDBACK
    // -------------------------------------

    function feedback(message, isError = false) {

        const output = $("hazardFeedback");

        if (!output) return;

        output.textContent = message;

        output.classList.toggle(
            "error",
            isError
        );

    }

    // -------------------------------------
    // MAP INITIALIZATION
    // -------------------------------------

    function initializeMap() {

        const mapElement = $("hazardMap");
        const mapStatus = $("hazardMapStatus");

        if (!mapElement) {
            console.error("Hazard map element not found.");
            return false;
        }

        if (typeof L === "undefined") {

            if (mapStatus) {
                mapStatus.textContent =
                    "LEAFLET LIBRARY UNAVAILABLE";
            }

            feedback(
                "Map library failed to load. Check your internet connection.",
                true
            );

            return false;

        }

        map = L.map("hazardMap", {

            center: [0, 0],
            zoom: 2,
            minZoom: 1,
            maxZoom: 18,
            zoomControl: true,
            attributionControl: true,
            preferCanvas: true

        });

        // Mars imagery layer
        const marsLayer = L.tileLayer(

            "https://tiles.arcgis.com/tiles/Ezk9fcjSUkeadg6u/arcgis/rest/services/Mars_Contour_Imagery/MapServer/tile/{z}/{y}/{x}",

            {
                minZoom: 1,
                maxZoom: 18,

                attribution:
                    'Mars Contour Imagery | <a href="https://www.arcgis.com/" target="_blank" rel="noopener noreferrer">ArcGIS</a>'
            }

        );

        marsLayer.addTo(map);

        // Marker layers
        hazardLayer = L.layerGroup().addTo(map);
        safeLayer = L.layerGroup().addTo(map);

        marsLayer.on("load", () => {

            if (mapStatus) {
                mapStatus.textContent =
                    "MARS IMAGERY LOADED";
            }

        });

        marsLayer.on("tileerror", () => {

            if (mapStatus) {
                mapStatus.textContent =
                    "SOME MAP TILES UNAVAILABLE";
            }

        });

        marsLayer.on("tileload", () => {

            if (mapStatus) {
                mapStatus.textContent =
                    "MAP SERVICE RESPONDING";
            }

        });

        // Map click event
        map.on("click", event => {

            const lat = event.latlng.lat;
            const lng = event.latlng.lng;

            selectedCoordinates = { lat, lng };

            updateCoordinateDisplay();

            if ($("hazardLat")) {
                $("hazardLat").value = lat.toFixed(6);
            }

            if ($("hazardLng")) {
                $("hazardLng").value = lng.toFixed(6);
            }

            if ($("safeLat")) {
                $("safeLat").value = lat.toFixed(6);
            }

            if ($("safeLng")) {
                $("safeLng").value = lng.toFixed(6);
            }

            feedback(
                "Map position selected. Coordinates filled into both assessment forms."
            );

        });

        // Reset map
        $("hazardMapReset")?.addEventListener(
            "click",
            () => {

                map.setView([0, 0], 2);

                selectedCoordinates = {
                    lat: 0,
                    lng: 0
                };

                updateCoordinateDisplay();

                feedback("Map view reset.");

            }
        );

        renderMapMarkers();

        window.setTimeout(() => {

            map.invalidateSize();

        }, 250);

        return true;

    }

    function updateCoordinateDisplay() {

        const display = $("hazardMapCoordinates");

        if (!display) return;

        display.textContent =
            `LAT: ${selectedCoordinates.lat.toFixed(4)} · LNG: ${selectedCoordinates.lng.toFixed(4)}`;

    }

    // -------------------------------------
    // MAP POPUP
    // -------------------------------------

    function createPopup(name, details) {

        const container = document.createElement("div");

        container.className = "hazard-popup";

        const heading = document.createElement("strong");

        heading.textContent = name;

        const detail = document.createElement("div");

        detail.textContent = details;

        container.append(heading, detail);

        return container;

    }

    // -------------------------------------
    // MAP MARKERS
    // -------------------------------------

    function renderMapMarkers() {

        if (!map || !hazardLayer || !safeLayer) {
            return;
        }

        hazardLayer.clearLayers();
        safeLayer.clearLayers();

        // Hazard markers
        hazards.forEach(hazard => {

            const lat = Number(hazard.lat);
            const lng = Number(hazard.lng);

            if (!validCoordinates(lat, lng)) return;

            if (!SEVERITIES[hazard.severity]) return;

            if (!TYPES[hazard.type]) return;

            const icon = L.divIcon({

                className: "astro-hazard-marker",

                html:
                    `<span class="marker-${hazard.severity}">
                        <i class="bi bi-exclamation"></i>
                    </span>`,

                iconSize: [27, 27],
                iconAnchor: [13, 13]

            });

            const marker = L.marker(
                [lat, lng],
                { icon }
            );

            marker.bindPopup(
                createPopup(
                    hazard.name,
                    `${TYPES[hazard.type]} · ${SEVERITIES[hazard.severity]} · ${lat.toFixed(4)}, ${lng.toFixed(4)}`
                )
            );

            marker.addTo(hazardLayer);

        });

        // Safe-zone candidate markers
        safeCandidates.forEach(candidate => {

            const lat = Number(candidate.lat);
            const lng = Number(candidate.lng);

            if (!validCoordinates(lat, lng)) return;

            const passed = candidate.status === "PASS";

            const icon = L.divIcon({

                className: passed
                    ? "astro-safe-marker"
                    : "astro-review-marker",

                html: passed
                    ? '<span><i class="bi bi-shield-check"></i></span>'
                    : '<span><i class="bi bi-exclamation"></i></span>',

                iconSize: [30, 30],
                iconAnchor: [15, 15]

            });

            const marker = L.marker(
                [lat, lng],
                { icon }
            );

            marker.bindPopup(
                createPopup(
                    candidate.name,
                    `Screening result: ${candidate.status} · Slope: ${candidate.slope}°`
                )
            );

            marker.addTo(safeLayer);

        });

    }

    // -------------------------------------
    // STATISTICS
    // -------------------------------------

    function updateStatistics() {

        const total = hazards.length;

        const high = hazards.filter(hazard =>
            hazard.severity === "high" ||
            hazard.severity === "critical"
        ).length;

        const craters = hazards.filter(hazard =>
            hazard.type === "crater"
        ).length;

        const safe = safeCandidates.filter(candidate =>
            candidate.status === "PASS"
        ).length;

        if ($("totalHazards")) {
            $("totalHazards").textContent = total;
        }

        if ($("highHazards")) {
            $("highHazards").textContent = high;
        }

        if ($("craterHazards")) {
            $("craterHazards").textContent = craters;
        }

        if ($("safeCandidates")) {
            $("safeCandidates").textContent = safe;
        }

    }

    // -------------------------------------
    // HAZARD TABLE
    // -------------------------------------

    function renderHazardTable() {

        const body = $("hazardTableBody");

        if (!body) return;

        const filter = $("hazardFilter")?.value || "all";

        body.replaceChildren();

        const filtered = hazards.filter(hazard =>
            filter === "all" ||
            hazard.type === filter
        );

        if (!filtered.length) {

            const row = document.createElement("tr");
            const cell = document.createElement("td");

            cell.colSpan = 6;
            cell.className = "hazard-empty-row";

            cell.textContent =
                "No matching hazard observations found.";

            row.appendChild(cell);
            body.appendChild(row);

        } else {

            filtered.forEach(hazard => {

                const row = document.createElement("tr");

                const name = document.createElement("td");
                name.textContent = hazard.name || "Unnamed hazard";

                const type = document.createElement("td");

                type.innerHTML =
                    `<span class="hazard-type-tag">${escapeHTML(
                        TYPES[hazard.type] || "Other"
                    )}</span>`;

                const severity = document.createElement("td");

                severity.innerHTML =
                    `<span class="hazard-severity-tag ${escapeHTML(
                        hazard.severity
                    )}">${escapeHTML(
                        SEVERITIES[hazard.severity] || "Unknown"
                    )}</span>`;

                const coordinates = document.createElement("td");

                coordinates.textContent =
                    `${Number(hazard.lat).toFixed(4)}, ${Number(hazard.lng).toFixed(4)}`;

                const diameter = document.createElement("td");

                diameter.textContent =
                    hazard.diameter === null ||
                    hazard.diameter === undefined
                        ? "Not recorded"
                        : `${hazard.diameter} m`;

                const action = document.createElement("td");

                const button = document.createElement("button");

                button.type = "button";
                button.className = "hazard-action-button";
                button.title = "View hazard on map";

                button.setAttribute(
                    "aria-label",
                    `View ${hazard.name} on map`
                );

                button.innerHTML =
                    '<i class="bi bi-eye"></i>';

                button.addEventListener("click", () => {

                    if (!map) {
                        feedback("Map is not available.", true);
                        return;
                    }

                    const lat = Number(hazard.lat);
                    const lng = Number(hazard.lng);

                    map.setView(
                        [lat, lng],
                        Math.max(map.getZoom(), 6)
                    );

                    const marker = hazardLayer
                        .getLayers()
                        .find(layer => {

                            const point = layer.getLatLng();

                            return point.lat === lat &&
                                point.lng === lng;

                        });

                    marker?.openPopup();

                });

                action.appendChild(button);

                row.append(
                    name,
                    type,
                    severity,
                    coordinates,
                    diameter,
                    action
                );

                body.appendChild(row);

            });

        }

        if ($("registryCount")) {

            $("registryCount").textContent =
                `${filtered.length} of ${hazards.length} observations`;

        }

        updateStatistics();

    }

    // -------------------------------------
    // ADD HAZARD
    // -------------------------------------

    $("hazardForm")?.addEventListener(
        "submit",
        event => {

            event.preventDefault();

            const form = event.currentTarget;

            if (!form.reportValidity()) return;

            const lat = Number($("hazardLat").value);
            const lng = Number($("hazardLng").value);

            const diameterInput =
                $("hazardDiameter").value.trim();

            const diameter = diameterInput === ""
                ? null
                : Number(diameterInput);

            if (!validCoordinates(lat, lng)) {

                feedback(
                    "Enter valid map coordinates.",
                    true
                );

                return;

            }

            if (
                diameter !== null &&
                (
                    !Number.isFinite(diameter) ||
                    diameter < 0 ||
                    diameter > 100000
                )
            ) {

                feedback(
                    "Enter a valid non-negative diameter.",
                    true
                );

                return;

            }

            if (hazards.length >= MAX_HAZARDS) {

                feedback(
                    "Maximum of 500 observations reached. Export or clear records first.",
                    true
                );

                return;

            }

            const hazard = {

                id: makeId(),

                name: $("hazardName").value.trim(),

                type: $("hazardType").value,

                severity: $("hazardSeverity").value,

                lat,
                lng,

                diameter,

                description:
                    $("hazardDescription").value.trim(),

                createdAt: new Date().toISOString()

            };

            if (
                !hazard.name ||
                !TYPES[hazard.type] ||
                !SEVERITIES[hazard.severity]
            ) {

                feedback(
                    "Please check the observation details.",
                    true
                );

                return;

            }

            hazards.push(hazard);

            if (!saveData(STORAGE_KEY, hazards)) {

                hazards.pop();
                return;

            }

            renderHazardTable();
            renderMapMarkers();

            if (map) {

                map.setView(
                    [lat, lng],
                    Math.max(map.getZoom(), 6)
                );

            }

            form.reset();

            $("hazardLat").value = lat.toFixed(6);
            $("hazardLng").value = lng.toFixed(6);

            feedback(
                "Hazard observation registered and saved in this browser."
            );

        }
    );

    // -------------------------------------
    // FILTER
    // -------------------------------------

    $("hazardFilter")?.addEventListener(
        "change",
        renderHazardTable
    );

    // -------------------------------------
    // CLEAR HAZARD REGISTRY
    // -------------------------------------

    $("clearHazards")?.addEventListener(
        "click",
        () => {

            if (!hazards.length) {

                feedback(
                    "The hazard registry is already empty."
                );

                return;

            }

            if (!confirm(
                "Delete every registered hazard from this browser?"
            )) {
                return;
            }

            const previous = hazards;

            hazards = [];

            if (!saveData(STORAGE_KEY, hazards)) {

                hazards = previous;
                return;

            }

            renderHazardTable();
            renderMapMarkers();

            feedback(
                "All hazard observations have been cleared."
            );

        }
    );

    // -------------------------------------
    // SLOPE RISK CLASSIFICATION
    // -------------------------------------

    function classifySlope(value) {

        if (value <= 5) {

            return {

                label: "LOW SCREENING RISK",

                category: "low",

                explanation:
                    "This slope falls within the model's lowest screening band."

            };

        }

        if (value <= 10) {

            return {

                label: "MODERATE SCREENING RISK",

                category: "moderate",

                explanation:
                    "Additional surface and vehicle stability investigation is recommended."

            };

        }

        if (value <= 20) {

            return {

                label: "HIGH SCREENING RISK",

                category: "high",

                explanation:
                    "This slope falls within the model's high-risk band and requires further engineering assessment."

            };

        }

        return {

            label: "CRITICAL SCREENING RISK",

            category: "critical",

            explanation:
                "This slope falls within the model's critical band."

        };

    }

    // -------------------------------------
    // SLOPE ANALYSIS
    // -------------------------------------

    function analyzeSlope() {

        const input = $("slopeInput");

        if (!input) return null;

        const value = Number(input.value);

        if (
            !Number.isFinite(value) ||
            value < 0 ||
            value > 90
        ) {

            input.setCustomValidity(
                "Enter a slope between 0 and 90 degrees."
            );

            input.reportValidity();

            input.setCustomValidity("");

            return null;

        }

        const risk = classifySlope(value);

        $("slopeValueDisplay").textContent =
            `${value}°`;

        const label = $("slopeRiskLabel");

        label.textContent = risk.label;

        label.className =
            `risk-label ${risk.category}`;

        $("slopeMarker").style.left =
            `${(value / 90) * 100}%`;

        $("slopeExplanation").textContent =
            risk.explanation;

        lastSlopeAnalysis = {

            value,
            ...risk

        };

        return lastSlopeAnalysis;

    }

    $("analyzeSlope")?.addEventListener(
        "click",
        analyzeSlope
    );

    $("slopeInput")?.addEventListener(
        "keydown",
        event => {

            if (event.key === "Enter") {

                event.preventDefault();

                analyzeSlope();

            }

        }
    );

    // -------------------------------------
    // SAFE ZONE EVALUATION
    // -------------------------------------

    $("safeZoneForm")?.addEventListener(
        "submit",
        event => {

            event.preventDefault();

            const form = event.currentTarget;

            if (!form.reportValidity()) return;

            const lat = Number($("safeLat").value);
            const lng = Number($("safeLng").value);

            const slope = Number($("safeSlope").value);
            const buffer = Number($("safeBuffer").value);

            const name = $("safeZoneName").value.trim();

            if (!validCoordinates(lat, lng)) {

                feedback(
                    "Enter valid candidate map coordinates.",
                    true
                );

                return;

            }

            if (
                !Number.isFinite(slope) ||
                slope < 0 ||
                slope > 90
            ) {

                feedback(
                    "Enter a slope between 0 and 90 degrees.",
                    true
                );

                return;

            }

            if (
                !Number.isFinite(buffer) ||
                buffer < 0.1 ||
                buffer > 100
            ) {

                feedback(
                    "Enter a search radius between 0.1 and 100 km.",
                    true
                );

                return;

            }

            if (!name) {

                feedback(
                    "Enter a candidate name.",
                    true
                );

                return;

            }

            if (safeCandidates.length >= MAX_CANDIDATES) {

                feedback(
                    "Maximum of 100 candidates reached.",
                    true
                );

                return;

            }

            // Find hazards within a great-circle radius on a spherical Mars model.
            // This assumes the stored map coordinates are true planetary lat/lon.
            const nearby = hazards.filter(hazard => {

                const hazardLat = Number(hazard.lat ?? hazard.latitude);
                const hazardLng = Number(hazard.lng ?? hazard.longitude ?? hazard.lon);

                return validCoordinates(hazardLat, hazardLng) &&
                    marsSurfaceDistanceKm(lat, lng, hazardLat, hazardLng) <= buffer;

            });

            const slopeRisk = classifySlope(slope);

            const slopePass = slope <= 5;

            const hazardPass = nearby.length === 0;

            const passed = slopePass && hazardPass;

            const candidate = {

                id: makeId(),

                name,

                lat,
                lng,

                slope,
                bufferKm: buffer,

                nearbyHazards: nearby.length,

                status: passed ? "PASS" : "REVIEW",

                createdAt: new Date().toISOString()

            };

            safeCandidates.push(candidate);

            if (!saveData(SAFE_KEY, safeCandidates)) {

                safeCandidates.pop();
                return;

            }

            // Display screening result
            const result = $("safeZoneResult");

            result.hidden = false;

            result.className = "safe-zone-result";

            if (passed) {

                result.classList.add("safe");

                $("safeResultIcon").innerHTML =
                    '<i class="bi bi-shield-check"></i>';

                $("safeResultTitle").textContent =
                    "Candidate Passed Preliminary Screening";

            } else {

                const severeHazard = nearby.some(hazard =>
                    hazard.severity === "critical" ||
                    hazard.severity === "high"
                );

                result.classList.add(
                    slopeRisk.category === "critical" ||
                    severeHazard
                        ? "danger"
                        : "warning"
                );

                $("safeResultIcon").innerHTML =
                    '<i class="bi bi-exclamation-triangle"></i>';

                $("safeResultTitle").textContent =
                    "Candidate Requires Further Review";

            }

            // Explanation
            const reasons = [];

            if (!slopePass) {

                reasons.push(
                    `Slope is ${slope}°, exceeding the configured 5° limit.`
                );

            }

            if (!hazardPass) {

                reasons.push(
                    `${nearby.length} registered hazard(s) fall within approximately ${buffer} km.`
                );

            }

            if (passed) {

                reasons.push(
                    `No registered hazards were found within approximately ${buffer} km.`
                );

                reasons.push(
                    "Entered slope is within the configured 5° screening limit."
                );

            }

            $("safeResultDescription").textContent =
                reasons.join(" ") +
                " This is not a certified safe landing zone.";

            // Re-render markers only once.
            renderMapMarkers();

            // Move map to evaluated candidate.
            if (map) {

                map.setView(
                    [lat, lng],
                    Math.max(map.getZoom(), 6)
                );

                const marker = safeLayer
                    .getLayers()
                    .find(layer => {

                        const point = layer.getLatLng();

                        return point.lat === lat &&
                            point.lng === lng;

                    });

                marker?.openPopup();

            }

            updateStatistics();

            feedback(
                `Candidate "${name}" evaluated: ${candidate.status}.`
            );

        }
    );

    // -------------------------------------
    // CSV EXPORT
    // -------------------------------------

    function csvCell(value) {

        let safeValue = String(value ?? "");

        // Reduce spreadsheet formula-injection risk
        // for text fields.
        if (/^[\s]*[=+\-@]/.test(safeValue)) {
            safeValue = "'" + safeValue;
        }

        return `"${safeValue.replace(/"/g, '""')}"`;

    }

    $("exportHazards")?.addEventListener(
        "click",
        () => {

            if (!hazards.length) {

                feedback(
                    "There are no hazard observations to export.",
                    true
                );

                return;

            }

            const rows = [

                [
                    "Name",
                    "Category",
                    "Severity",
                    "Map Latitude",
                    "Map Longitude",
                    "Diameter (m)",
                    "Description",
                    "Created At"
                ]

            ];

            hazards.forEach(hazard => {

                rows.push([

                    hazard.name,

                    TYPES[hazard.type] || hazard.type,

                    SEVERITIES[hazard.severity] ||
                        hazard.severity,

                    hazard.lat,
                    hazard.lng,

                    hazard.diameter ?? "",

                    hazard.description,

                    hazard.createdAt

                ]);

            });

            const csv = "\uFEFF" + rows
                .map(row =>
                    row.map(csvCell).join(",")
                )
                .join("\r\n");

            downloadFile(
                csv,
                "project-astro-hazard-registry.csv",
                "text/csv;charset=utf-8"
            );

            feedback(
                "Hazard registry exported successfully."
            );

        }
    );

    // -------------------------------------
    // DOWNLOAD UTILITY
    // -------------------------------------

    function downloadFile(content, filename, type) {

        const blob = new Blob(
            [content],
            { type }
        );

        const url = URL.createObjectURL(blob);

        const link = document.createElement("a");

        link.href = url;
        link.download = filename;

        document.body.appendChild(link);

        link.click();

        link.remove();

        window.setTimeout(() => {

            URL.revokeObjectURL(url);

        }, 1000);

    }

    // -------------------------------------
    // INITIALIZE MODULE
    // -------------------------------------

    initializeMap();

    renderHazardTable();

    updateStatistics();

    analyzeSlope();

    updateCoordinateDisplay();

});
