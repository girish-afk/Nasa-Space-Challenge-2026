
document.addEventListener("DOMContentLoaded", () => {
    "use strict";

    const $ = (id) => document.getElementById(id);

    const ARCHIVE_KEY = "projectAstroReportsArchiveV1";
    const SURVIVAL_KEY = "projectAstroSurvivalPlanV1";

    const API = {
        routes: "api/routes.php",
        hazards: "api/hazards.php",
        resources: "api/resources.php",
        weather: "api/weather.php",
        locations: "api/locations.php"
    };

    const state = {
        routes: [],
        hazards: [],
        resources: [],
        weather: null,
        locations: [],
        survival: null,
        moduleStatus: {},
        charts: {},
        generatedAt: null
    };

    const COLORS = {
        orange: "#E66A45",
        cyan: "#50C8E8",
        green: "#39C58A",
        yellow: "#E9B44C",
        red: "#F05252",
        muted: "#788697",
        grid: "rgba(255,255,255,0.09)",
        text: "#A3AFBE"
    };

    function escapeHTML(value) {
        return String(value ?? "").replace(/[&<>"']/g, (char) => ({
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        })[char]);
    }

    function formatNumber(value, digits = 2) {
        return Number(value).toLocaleString("en-IN", {
            maximumFractionDigits: digits
        });
    }

    function formatDate(value) {
        if (!value) return "Not available";

        const date = new Date(value);

        if (Number.isNaN(date.getTime())) {
            return "Not available";
        }

        return date.toLocaleString("en-IN", {
            dateStyle: "medium",
            timeStyle: "short"
        });
    }

    async function fetchJSON(url) {
        const response = await fetch(url, {
            method: "GET",
            headers: {
                "Accept": "application/json"
            },
            cache: "no-store"
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        if (!data || typeof data !== "object") {
            throw new Error("Invalid API response");
        }

        if (data.success === false) {
            throw new Error(data.message || "Module unavailable");
        }

        return data;
    }

    function loadSurvival() {
        try {
            const saved = localStorage.getItem(SURVIVAL_KEY);

            if (!saved) return null;

            const data = JSON.parse(saved);

            if (!data || typeof data !== "object") return null;

            const crew = Number(data.crewSize);
            const days = Number(data.missionDays);
            const reserve = Number(data.reservePercent);

            if (
                !Number.isFinite(crew) ||
                !Number.isFinite(days) ||
                crew < 1 ||
                days < 1
            ) {
                return null;
            }

            const definitions = [
                {
                    name: "Water",
                    rate: "waterRate",
                    stock: "stockWater",
                    unit: "L"
                },
                {
                    name: "Food",
                    rate: "foodRate",
                    stock: "stockFood",
                    unit: "kg"
                },
                {
                    name: "Oxygen",
                    rate: "oxygenRate",
                    stock: "stockOxygen",
                    unit: "kg"
                },
                {
                    name: "Power",
                    rate: "powerRate",
                    stock: "stockPower",
                    unit: "kWh"
                }
            ];

            const results = definitions.map((item) => {
                const rate = Number(data[item.rate]);
                const available = Number(data[item.stock]);

                if (
                    !Number.isFinite(rate) ||
                    !Number.isFinite(available) ||
                    rate < 0 ||
                    available < 0
                ) {
                    throw new Error("Invalid saved survival values");
                }

                const base = rate * crew * days;
                const required = base * (1 + reserve / 100);
                const balance = available - required;

                return {
                    name: item.name,
                    unit: item.unit,
                    base,
                    required,
                    available,
                    balance,
                    sufficient: balance >= 0
                };
            });

            const readiness = Math.round(
                results.reduce((sum, item) => {
                    const coverage = item.required > 0
                        ? Math.min(item.available / item.required, 1)
                        : 1;

                    return sum + coverage;
                }, 0) / results.length * 100
            );

            return {
                missionName: data.missionName || "Unnamed mission",
                crew,
                days,
                reserve,
                results,
                readiness,
                shortages: results.filter(
                    (item) => !item.sufficient
                ).length,
                notes: data.missionNotes || "",
                checklist: Array.isArray(data.checklist)
                    ? data.checklist
                    : []
            };

        } catch (error) {
            console.warn("Survival report unavailable:", error);
            return null;
        }
    }

    function getArchive() {
        try {
            const archive = JSON.parse(
                localStorage.getItem(ARCHIVE_KEY) || "[]"
            );

            return Array.isArray(archive) ? archive : [];

        } catch {
            return [];
        }
    }

    function saveArchive(archive) {
        try {
            localStorage.setItem(
                ARCHIVE_KEY,
                JSON.stringify(archive.slice(0, 30))
            );

            return true;

        } catch (error) {
            console.error("Report archive save failed:", error);
            return false;
        }
    }

    function setStatus(id, text, tone = "info") {
        const element = $(id);

        if (!element) return;

        element.textContent = text;
        element.dataset.status = tone;
    }

    function updateModuleStatus() {
        const modules = [
            {
                name: "Rover Route Planner",
                key: "routes",
                icon: "bi-signpost-split",
                detail: `${state.routes.length} saved routes`
            },
            {
                name: "Hazard Detection",
                key: "hazards",
                icon: "bi-shield-exclamation",
                detail: `${state.hazards.length} hazard records`
            },
            {
                name: "Water & Resources",
                key: "resources",
                icon: "bi-droplet-half",
                detail: `${state.resources.length} catalog entries`
            },
            {
                name: "Weather & Environment",
                key: "weather",
                icon: "bi-cloud-haze",
                detail: state.weather
                    ? "Historical weather response received"
                    : "Weather data unavailable"
            },
            {
                name: "Planetary Locations",
                key: "locations",
                icon: "bi-geo-alt",
                detail: `${state.locations.length} location references`
            },
            {
                name: "Survival Planner",
                key: "survival",
                icon: "bi-heart-pulse",
                detail: state.survival
                    ? "Saved survival configuration found"
                    : "No valid saved survival configuration"
            }
        ];

        $("moduleStatusGrid").innerHTML = modules.map((module) => {
            const available = Boolean(state.moduleStatus[module.key]);

            return `
                <article class="reports-module-card">
                    <div class="reports-module-icon">
                        <i class="bi ${module.icon}"></i>
                    </div>

                    <div class="reports-module-content">
                        <strong>${escapeHTML(module.name)}</strong>
                        <small>${escapeHTML(module.detail)}</small>
                    </div>

                    <span class="reports-module-state ${available ? "available" : "unavailable"}">
                        <span></span>
                        ${available ? "AVAILABLE" : "UNAVAILABLE"}
                    </span>
                </article>
            `;
        }).join("");
    }

    function updateSummary() {
        $("reportRouteCount").textContent = state.moduleStatus.routes
            ? state.routes.length
            : "--";

        $("reportHazardCount").textContent = state.moduleStatus.hazards
            ? state.hazards.length
            : "--";

        $("reportHazardNote").textContent =
            state.moduleStatus.hazards &&
            state.hazards.length
                ? "Returned by Hazard Detection API"
                : "No hazard records available";

        $("reportResourceCount").textContent =
            state.moduleStatus.resources
                ? state.resources.length
                : "--";

        $("reportReadiness").textContent =
            state.survival
                ? `${state.survival.readiness}%`
                : "--";

        $("reportReadinessNote").textContent =
            state.survival
                ? `${state.survival.shortages} supply categories need attention`
                : "No valid saved survival plan";

        $("reportGeneratedAt").textContent =
            `Last refreshed: ${formatDate(state.generatedAt)}`;

        const available = Object.values(state.moduleStatus)
            .filter(Boolean).length;

        $("reportDataStatus").textContent =
            `${available} of 6 data sources available`;
    }

    function renderRoutes() {
        const body = $("routeReportBody");

        if (!state.moduleStatus.routes) {
            body.innerHTML = `
                <tr>
                    <td colspan="4" class="inventory-empty">
                        Route API unavailable. Check api/routes.php.
                    </td>
                </tr>
            `;
            return;
        }

        if (!state.routes.length) {
            body.innerHTML = `
                <tr>
                    <td colspan="4" class="inventory-empty">
                        No saved routes. Create and save a route in Rover Route Planner.
                    </td>
                </tr>
            `;
            return;
        }

        body.innerHTML = state.routes.map((route) => {
            const waypoints = Array.isArray(route.waypoints)
                ? route.waypoints.length
                : 0;

            return `
                <tr>
                    <td>
                        <strong>${escapeHTML(route.name || "Unnamed route")}</strong>
                        <small>ID: ${escapeHTML(route.id || "N/A")}</small>
                    </td>

                    <td>${waypoints}</td>

                    <td>${escapeHTML(formatDate(route.created_at))}</td>

                    <td>
                        <span class="stock-status ${waypoints >= 2 ? "good" : "low"}">
                            ${waypoints >= 2 ? "SAVED" : "INCOMPLETE"}
                        </span>
                    </td>
                </tr>
            `;
        }).join("");
    }

    function renderHazards() {
        const body = $("hazardReportBody");

        if (!state.moduleStatus.hazards) {
            body.innerHTML = `
                <tr>
                    <td colspan="5" class="inventory-empty">
                        Hazard API unavailable.
                    </td>
                </tr>
            `;
            return;
        }

        if (!state.hazards.length) {
            body.innerHTML = `
                <tr>
                    <td colspan="5" class="inventory-empty">
                        No hazard records returned.
                    </td>
                </tr>
            `;
            return;
        }

        body.innerHTML = state.hazards.map((hazard) => {
            const severity = String(
                hazard.severity || "unknown"
            ).toLowerCase();

            const allowed = ["low", "moderate", "high"];

            const tone = allowed.includes(severity)
                ? severity
                : "low";

            return `
                <tr>
                    <td>
                        <strong>${escapeHTML(hazard.id || "N/A")}</strong>
                    </td>

                    <td>
                        <strong>${escapeHTML(hazard.name || "Unknown hazard")}</strong>
                        <small>${escapeHTML(hazard.description || "")}</small>
                    </td>

                    <td>${escapeHTML(hazard.latitude ?? "N/A")}</td>

                    <td>${escapeHTML(hazard.longitude ?? "N/A")}</td>

                    <td>
                        <span class="stock-status ${tone}">
                            ${escapeHTML(severity.toUpperCase())}
                        </span>
                    </td>
                </tr>
            `;
        }).join("");

        $("hazardDataNotice").textContent =
            "Dataset classification: " +
            (state.hazardDatasetType || "Not specified") +
            ". " +
            (state.hazardMessage || "");
    }

    function renderSurvival() {
        const body = $("survivalReportBody");
        const mission = state.survival;

        if (!mission) {
            $("survivalMissionName").textContent =
                "No saved survival mission";

            $("survivalMissionDetails").textContent =
                "Open Survival Planner, configure your mission and save it.";

            $("survivalReportStatus").textContent = "NOT AVAILABLE";

            body.innerHTML = `
                <tr>
                    <td colspan="5" class="inventory-empty">
                        No valid saved survival configuration found in this browser.
                    </td>
                </tr>
            `;
            return;
        }

        $("survivalMissionName").textContent =
            mission.missionName;

        $("survivalMissionDetails").textContent =
            `${mission.crew} crew members · ${mission.days} days · ${mission.reserve}% reserve`;

        const safe = mission.shortages === 0;

        $("survivalReportStatus").textContent =
            safe ? "SUPPLIES COVERED" : "REVIEW REQUIRED";

        $("survivalReportStatus").className =
            `resource-status-pill ${safe ? "success" : "warning"}`;

        body.innerHTML = mission.results.map((item) => `
            <tr>
                <td>
                    <strong>${escapeHTML(item.name)}</strong>
                </td>

                <td>${formatNumber(item.required)} ${item.unit}</td>

                <td>${formatNumber(item.available)} ${item.unit}</td>

                <td>
                    <strong class="inventory-number ${item.balance >= 0 ? "balance-positive" : "balance-negative"}">
                        ${item.balance >= 0 ? "+" : ""}${formatNumber(item.balance)} ${item.unit}
                    </strong>
                </td>

                <td>
                    <span class="stock-status ${item.sufficient ? "good" : "bad"}">
                        ${item.sufficient ? "SUFFICIENT" : "SHORTAGE"}
                    </span>
                </td>
            </tr>
        `).join("");
    }

    function renderResources() {
        const container = $("resourceReportGrid");

        if (!state.moduleStatus.resources) {
            container.innerHTML = `
                <div class="resource-api-error">
                    Resource catalog unavailable.
                </div>
            `;
            return;
        }

        if (!state.resources.length) {
            container.innerHTML = `
                <div class="resource-loading">
                    No resource catalog entries returned.
                </div>
            `;
            return;
        }

        container.innerHTML = state.resources.map((resource) => `
            <article class="reports-resource-card">
                <div class="reports-resource-card-icon">
                    <i class="bi ${escapeHTML(resource.icon || "bi-box")}"></i>
                </div>

                <div>
                    <span>${escapeHTML(resource.category || "RESOURCE")}</span>
                    <strong>${escapeHTML(resource.name || "Unnamed resource")}</strong>
                    <small>${escapeHTML(resource.status || "Reference entry")}</small>
                </div>
            </article>
        `).join("");
    }

    function destroyChart(key) {
        if (state.charts[key]) {
            state.charts[key].destroy();
            state.charts[key] = null;
        }
    }

    function chartDefaults() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: COLORS.text,
                        padding: 16,
                        usePointStyle: true,
                        font: {
                            family: "Inter",
                            size: 10
                        }
                    }
                },
                tooltip: {
                    backgroundColor: "#141E2B",
                    titleColor: "#F1F3F6",
                    bodyColor: "#A3AFBE",
                    borderColor: COLORS.grid,
                    borderWidth: 1
                }
            }
        };
    }

    function renderHazardChart() {
        destroyChart("hazards");

        if (!state.moduleStatus.hazards) {
            $("hazardChartCaption").textContent =
                "Chart unavailable because the hazard API could not be reached.";
            return;
        }

        const counts = {
            Low: 0,
            Moderate: 0,
            High: 0,
            Other: 0
        };

        state.hazards.forEach((hazard) => {
            const severity = String(
                hazard.severity || ""
            ).toLowerCase();

            if (severity === "low") counts.Low++;
            else if (severity === "moderate") counts.Moderate++;
            else if (severity === "high") counts.High++;
            else counts.Other++;
        });

        if (!state.hazards.length) {
            $("hazardChartCaption").textContent =
                "No hazard records are available to visualize.";
            return;
        }

        state.charts.hazards = new Chart(
            $("hazardChart"),
            {
                type: "doughnut",
                data: {
                    labels: Object.keys(counts),
                    datasets: [{
                        data: Object.values(counts),
                        backgroundColor: [
                            COLORS.green,
                            COLORS.yellow,
                            COLORS.red,
                            COLORS.muted
                        ],
                        borderColor: "#141E2B",
                        borderWidth: 3
                    }]
                },
                options: {
                    ...chartDefaults(),
                    cutout: "68%"
                }
            }
        );

        $("hazardChartCaption").textContent =
            `${state.hazards.length} records returned by the hazard API.`;
    }

    function renderSupplyChart() {
        destroyChart("supplies");

        const mission = state.survival;

        if (!mission) {
            $("supplyChartCaption").textContent =
                "Save a Survival Planner configuration to generate this chart.";
            return;
        }

        state.charts.supplies = new Chart(
            $("supplyChart"),
            {
                type: "bar",
                data: {
                    labels: mission.results.map((item) => item.name),
                    datasets: [
                        {
                            label: "Required including reserve",
                            data: mission.results.map((item) => item.required),
                            backgroundColor: COLORS.orange,
                            borderRadius: 4
                        },
                        {
                            label: "Available inventory",
                            data: mission.results.map((item) => item.available),
                            backgroundColor: COLORS.cyan,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    ...chartDefaults(),
                    scales: {
                        x: {
                            ticks: {
                                color: COLORS.text,
                                font: { family: "Inter", size: 10 }
                            },
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: COLORS.text,
                                font: { family: "Inter", size: 9 }
                            },
                            grid: {
                                color: COLORS.grid
                            }
                        }
                    }
                }
            }
        );

        $("supplyChartCaption").textContent =
            "Values use the units defined for each resource; compare each resource within its own unit.";
    }

    function buildReport() {
        return {
            application: "PROJECT ASTRO",
            reportType: "Interplanetary Mission Summary",
            reportId: "ASTRO-" + Date.now().toString(36).toUpperCase(),
            generatedAt: new Date().toISOString(),

            dataClassification: {
                hazards: state.hazardDatasetType || "Not specified",
                weather: state.weather
                    ? "NASA InSight historical archive response"
                    : "Unavailable",
                resources: "Curated reference catalog",
                routes: "Locally saved rover route records",
                survival: "Browser-saved planning assumptions"
            },

            moduleAvailability: { ...state.moduleStatus },

            summary: {
                savedRoutes: state.routes.length,
                hazards: state.hazards.length,
                resourceCategories: state.resources.length,
                locations: state.locations.length,
                survivalReadiness: state.survival
                    ? state.survival.readiness
                    : null
            },

            routes: state.routes,
            hazards: state.hazards,
            resources: state.resources,
            locations: state.locations,
            weather: state.weather,
            survival: state.survival
        };
    }

    function exportJSON() {
        const report = buildReport();

        const blob = new Blob(
            [JSON.stringify(report, null, 2)],
            { type: "application/json" }
        );

        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");

        link.href = url;
        link.download = `${report.reportId}.json`;

        document.body.appendChild(link);
        link.click();
        link.remove();

        URL.revokeObjectURL(url);
    }

    function generateSnapshot() {
        const report = buildReport();

        const archive = getArchive();

        archive.unshift({
            id: report.reportId,
            generatedAt: report.generatedAt,
            routeCount: report.summary.savedRoutes,
            hazardCount: report.summary.hazards,
            data: report
        });

        if (!saveArchive(archive)) {
            alert("Could not save the report archive. Check browser storage.");
            return;
        }

        renderArchive();

        return report;
    }

    function exportSnapshot(id) {
        const snapshot = getArchive().find(
            (item) => item.id === id
        );

        if (!snapshot) {
            alert("The selected report could not be found.");
            return;
        }

        const blob = new Blob(
            [JSON.stringify(snapshot.data, null, 2)],
            { type: "application/json" }
        );

        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");

        link.href = url;
        link.download = `${snapshot.id}.json`;

        document.body.appendChild(link);
        link.click();
        link.remove();

        URL.revokeObjectURL(url);
    }

    function renderArchive() {
        const archive = getArchive();

        $("archiveCount").textContent =
            `${archive.length} REPORTS`;

        const body = $("reportArchiveBody");

        if (!archive.length) {
            body.innerHTML = `
                <tr>
                    <td colspan="5" class="inventory-empty">
                        No reports generated yet. Click Export JSON to download a report,
                        or use Print / Save PDF to create a report snapshot.
                    </td>
                </tr>
            `;
            return;
        }

        body.innerHTML = archive.map((item) => `
            <tr>
                <td>
                    <strong>${escapeHTML(item.id)}</strong>
                </td>

                <td>${escapeHTML(formatDate(item.generatedAt))}</td>

                <td>${Number(item.routeCount) || 0}</td>

                <td>${Number(item.hazardCount) || 0}</td>

                <td>
                    <button type="button"
                            class="reports-download-button"
                            data-export-report="${escapeHTML(item.id)}"
                            aria-label="Download report ${escapeHTML(item.id)}">
                        <i class="bi bi-download"></i>
                        JSON
                    </button>
                </td>
            </tr>
        `).join("");
    }

    async function loadAllData() {
        $("refreshReport").disabled = true;
        $("refreshReport").innerHTML =
            '<i class="bi bi-arrow-repeat"></i> Refreshing...';

        state.survival = loadSurvival();

        const requests = [
            ["hazards", API.hazards, (data) => {
                state.hazards = Array.isArray(data.hazards)
                    ? data.hazards
                    : [];

                state.hazardDatasetType = data.dataset_type || "unspecified";
                state.hazardMessage = data.message || "";
            }],

            ["resources", API.resources, (data) => {
                state.resources = Array.isArray(data.resources)
                    ? data.resources
                    : [];
            }],

            ["weather", API.weather, (data) => {
                state.weather = data;
            }],

            ["locations", API.locations, (data) => {
                state.locations = Array.isArray(data.locations)
                    ? data.locations
                    : [];
            }]
        ];

        // Saved rover routes live in this browser because Vercel containers are stateless.
        try {
            const savedRoutes = JSON.parse(localStorage.getItem("projectAstroSavedRoutesV1") || "[]");
            state.routes = Array.isArray(savedRoutes) ? savedRoutes : [];
            state.moduleStatus.routes = true;
        } catch (error) {
            state.routes = [];
            state.moduleStatus.routes = false;
            console.warn("Saved routes unavailable in browser storage:", error);
        }

        await Promise.all(requests.map(async ([key, url, apply]) => {
            try {
                const data = await fetchJSON(url);
                apply(data);
                state.moduleStatus[key] = true;

            } catch (error) {
                console.warn(`${key} module unavailable:`, error);

                state.moduleStatus[key] = false;

                if (key === "routes") state.routes = [];
                if (key === "hazards") state.hazards = [];
                if (key === "resources") state.resources = [];
                if (key === "weather") state.weather = null;
                if (key === "locations") state.locations = [];
            }
        }));

        state.moduleStatus.survival = Boolean(state.survival);

        state.generatedAt = new Date().toISOString();

        updateModuleStatus();
        updateSummary();

        renderRoutes();
        renderHazards();
        renderSurvival();
        renderResources();

        renderHazardChart();
        renderSupplyChart();

        $("refreshReport").disabled = false;
        $("refreshReport").innerHTML =
            '<i class="bi bi-arrow-clockwise"></i> Refresh data';
    }

    $("refreshReport").addEventListener("click", loadAllData);

    $("exportReport").addEventListener("click", () => {
        const report = generateSnapshot();

        if (report) {
            exportJSON();
        }
    });

    $("printReport").addEventListener("click", () => {
        generateSnapshot();
        window.print();
    });

    $("clearReportArchive").addEventListener("click", () => {
        if (!confirm("Delete all locally saved report snapshots?")) {
            return;
        }

        try {
            localStorage.removeItem(ARCHIVE_KEY);
            renderArchive();
        } catch (error) {
            alert("Could not clear the report archive.");
        }
    });

    $("reportArchiveBody").addEventListener("click", (event) => {
        const button = event.target.closest("[data-export-report]");

        if (!button) return;

        exportSnapshot(button.dataset.exportReport);
    });

    renderArchive();
    loadAllData();
});
