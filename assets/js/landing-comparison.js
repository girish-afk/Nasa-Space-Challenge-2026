
/* ==========================================
   PROJECT ASTRO
   PHASE 4.3 - LANDING SITE COMPARISON
========================================== */

document.addEventListener("DOMContentLoaded", () => {

    "use strict";

    const $ = id => document.getElementById(id);

    const STORAGE = {
        assessments: [
            "projectAstro.landingAssessments.v1",
            "projectAstro.landingZone.assessments.v1",
            "projectAstro.assessments.v1"
        ],

        hazards: "projectAstro.hazards.v1",
        safeCandidates: "projectAstro.safeCandidates.v1"
    };

    let candidates = [];
    let hazards = [];
    let chart = null;

    const WEIGHTS = {
        slope: 35,
        elevation: 25,
        rocks: 20,
        temperature: 20
    };

    // ----------------------------------
    // SAFE STORAGE READER
    // ----------------------------------

    function readStorage(key) {

        try {

            const value = JSON.parse(
                localStorage.getItem(key) || "[]"
            );

            return Array.isArray(value) ? value : [];

        } catch (error) {

            console.error("Storage error:", key, error);

            return [];

        }

    }

    function loadAssessments() {

        for (const key of STORAGE.assessments) {

            const data = readStorage(key);

            if (data.length) {
                return data;
            }

        }

        return [];

    }

    // ----------------------------------
    // NORMALIZE CANDIDATES
    // ----------------------------------

    function normalizeCandidate(item, index) {

        const data = item.data || item.parameters || item;

        const score = Number(
            item.score ??
            item.totalScore ??
            item.suitabilityScore
        );

        const latitude = Number(
            data.latitude ?? data.lat
        );

        const longitude = Number(
            data.longitude ?? data.lng ?? data.lon
        );

        const slope = Number(
            data.slope
        );

        const elevation = Number(
            data.elevation
        );

        const rocks = Number(
            data.rocks ?? data.rockCoverage
        );

        const temperature = Number(
            data.temperatureRange ??
            data.temperature
        );

        return {

            id: String(
                item.id ??
                item.assessmentId ??
                `candidate-${index}`
            ),

            name: String(
                data.name ??
                item.name ??
                `Candidate ${index + 1}`
            ),

            latitude,
            longitude,

            slope,
            elevation,
            rocks,
            temperature,

            score: Number.isFinite(score) &&
                   score >= 0 && score <= 100
                ? score
                : null,

            category: item.category ?? "Unclassified",

            raw: item

        };

    }

    // ----------------------------------
    // DATA VALIDATION
    // ----------------------------------

    function isValidCoordinate(value, min, max) {

        return Number.isFinite(value) &&
               value >= min &&
               value <= max;

    }

    function isValidCandidate(candidate) {

        return Number.isFinite(candidate.score) &&
               candidate.score >= 0 &&
               candidate.score <= 100 &&
               isValidCoordinate(candidate.latitude, -90, 90) &&
               isValidCoordinate(candidate.longitude, -180, 180);

    }

    // ----------------------------------
    // HAZARD DISTANCE
    // ----------------------------------

    function distanceKm(lat1, lon1, lat2, lon2) {

        const radius = 3390;

        const toRad = value => value * Math.PI / 180;

        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);

        const a =
            Math.sin(dLat / 2) ** 2 +
            Math.cos(toRad(lat1)) *
            Math.cos(toRad(lat2)) *
            Math.sin(dLon / 2) ** 2;

        return 2 * radius *
            Math.atan2(
                Math.sqrt(a),
                Math.sqrt(1 - a)
            );

    }

    function countNearbyHazards(candidate) {

        if (
            !Number.isFinite(candidate.latitude) ||
            !Number.isFinite(candidate.longitude)
        ) {
            return null;
        }

        return hazards.filter(hazard => {

            const lat = Number(
                hazard.lat ?? hazard.latitude
            );

            const lng = Number(
                hazard.lng ??
                hazard.longitude ??
                hazard.lon
            );

            if (
                !isValidCoordinate(lat, -90, 90) ||
                !isValidCoordinate(lng, -180, 180)
            ) {
                return false;
            }

            return distanceKm(
                candidate.latitude,
                candidate.longitude,
                lat,
                lng
            ) <= 5;

        }).length;

    }

    // ----------------------------------
    // UI HELPERS
    // ----------------------------------

    function setText(id, value) {

        const element = $(id);

        if (element) {
            element.textContent = value;
        }

    }

    function populateCandidates() {

        const selectA = $("candidateA");
        const selectB = $("candidateB");

        selectA.replaceChildren();
        selectB.replaceChildren();

        candidates.forEach(candidate => {

            [selectA, selectB].forEach(select => {

                const option = document.createElement("option");

                option.value = candidate.id;

                option.textContent =
                    `${candidate.name} (${candidate.score}/100)`;

                select.appendChild(option);

            });

        });

        if (candidates.length > 1) {
            selectB.selectedIndex = 1;
        }

    }

    // ----------------------------------
    // SUMMARY
    // ----------------------------------

    function updateSummary() {

        setText(
            "totalCandidates",
            candidates.length
        );

        setText(
            "candidateCount",
            `${candidates.length} Candidates`
        );

        setText(
            "totalHazards",
            hazards.length
        );

        const best = [...candidates].sort(
            (a, b) => b.score - a.score
        )[0];

        if (best) {

            setText(
                "bestScore",
                `${best.score}/100`
            );

            setText(
                "bestSiteName",
                best.name
            );

        } else {

            setText("bestScore", "--");

            setText(
                "bestSiteName",
                "No valid assessments"
            );

        }

    }

    // ----------------------------------
    // COMPARISON TABLE
    // ----------------------------------

    function renderComparison(a, b) {

        setText("headingA", a.name);
        setText("headingB", b.name);

        const rows = [

            ["Suitability Score", `${a.score}/100`, `${b.score}/100`],

            ["Surface Slope", format(a.slope, "°"), format(b.slope, "°")],

            ["Elevation Variation", format(a.elevation, "m"), format(b.elevation, "m")],

            ["Rock Coverage", format(a.rocks, "%"), format(b.rocks, "%")],

            ["Temperature Range", format(a.temperature, "°C"), format(b.temperature, "°C")],

            ["Nearby Hazards (5 km)", format(a.nearbyHazards), format(b.nearbyHazards)]

        ];

        const tbody = $("comparisonTable");

        tbody.replaceChildren();

        rows.forEach(row => {

            const tr = document.createElement("tr");

            row.forEach((value, index) => {

                const td = document.createElement("td");

                td.textContent = value;

                if (index === 0) {
                    td.className = "fw-semibold";
                }

                tr.appendChild(td);

            });

            tbody.appendChild(tr);

        });

    }

    function format(value, unit = "") {

        return Number.isFinite(value)
            ? `${value}${unit}`
            : "Unavailable";

    }

    // ----------------------------------
    // CHART
    // ----------------------------------

    function renderChart(a, b) {

        if (typeof Chart === "undefined") {
            setText("comparisonStatus", "Chart.js unavailable.");
            return;
        }

        const canvas = $("comparisonChart");

        if (chart) {
            chart.destroy();
        }

        chart = new Chart(canvas, {

            type: "bar",

            data: {

                labels: [a.name, b.name],

                datasets: [{

                    label: "Suitability Score",

                    data: [a.score, b.score],

                    backgroundColor: [
                        "#E66A45",
                        "#50C8E8"
                    ],

                    borderRadius: 4

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                scales: {

                    y: {

                        min: 0,
                        max: 100,

                        ticks: {
                            color: "#A3AFBE"
                        },

                        grid: {
                            color: "rgba(255,255,255,0.09)"
                        }

                    },

                    x: {

                        ticks: {
                            color: "#A3AFBE"
                        },

                        grid: {
                            display: false
                        }

                    }

                },

                plugins: {

                    legend: {
                        labels: {
                            color: "#F1F3F6"
                        }
                    }

                }

            }

        });

    }

    // ----------------------------------
    // RECOMMENDATION
    // ----------------------------------

    function renderRecommendation(a, b) {

        const output = $("recommendationContent");

        output.replaceChildren();

        const winner = a.score > b.score
            ? a
            : b.score > a.score
                ? b
                : null;

        const heading = document.createElement("h4");

        heading.className = "mb-2";

        heading.style.color = "var(--accent)";

        heading.textContent = winner
            ? `Higher-ranked candidate: ${winner.name}`
            : "Both candidates have equal suitability scores";

        const description = document.createElement("p");

        description.textContent = winner
            ? `${winner.name} has a screening score of ${winner.score}/100, compared with ${winner === a ? b.score : a.score}/100 for the other candidate.`
            : `Both candidates scored ${a.score}/100. Additional scientific evidence is needed to distinguish them.`;

        const note = document.createElement("small");

        note.className = "text-secondary";

        note.textContent =
            "This is a preliminary screening recommendation, not a certified landing decision. Verify data quality, hazard coverage and terrain resolution before mission use.";

        output.append(heading, description, note);

    }

    // ----------------------------------
    // RUN COMPARISON
    // ----------------------------------

    function compare() {

        const a = candidates.find(
            item => item.id === $("candidateA").value
        );

        const b = candidates.find(
            item => item.id === $("candidateB").value
        );

        if (!a || !b) {

            setText(
                "comparisonStatus",
                "Select two valid candidates."
            );

            return;

        }

        if (a.id === b.id) {

            setText(
                "comparisonStatus",
                "Select two different candidates."
            );

            return;

        }

        a.nearbyHazards = countNearbyHazards(a);
        b.nearbyHazards = countNearbyHazards(b);

        renderComparison(a, b);

        renderChart(a, b);

        renderRecommendation(a, b);

        setText(
            "comparisonStatus",
            "Comparison completed using available stored assessments."
        );

    }

    // ----------------------------------
    // CSV EXPORT
    // ----------------------------------

    function exportCSV() {

        const a = candidates.find(
            item => item.id === $("candidateA").value
        );

        const b = candidates.find(
            item => item.id === $("candidateB").value
        );

        if (!a || !b || a.id === b.id) {

            setText(
                "comparisonStatus",
                "Select two different candidates before exporting."
            );

            return;

        }

        a.nearbyHazards = countNearbyHazards(a);
        b.nearbyHazards = countNearbyHazards(b);

        const rows = [

            ["Parameter", a.name, b.name],

            ["Score", a.score, b.score],

            ["Latitude", a.latitude, b.latitude],

            ["Longitude", a.longitude, b.longitude],

            ["Slope", a.slope, b.slope],

            ["Elevation variation", a.elevation, b.elevation],

            ["Rock coverage", a.rocks, b.rocks],

            ["Temperature range", a.temperature, b.temperature],

            ["Hazards within 5 km",
                a.nearbyHazards,
                b.nearbyHazards]

        ];

        const csv = rows.map(row =>
            row.map(value => {

                const text = String(value ?? "");

                return `"${text.replace(/"/g, '""')}"`;

            }).join(",")

        ).join("\r\n");

        const blob = new Blob(
            ["\uFEFF" + csv],
            { type: "text/csv;charset=utf-8;" }
        );

        const url = URL.createObjectURL(blob);

        const link = document.createElement("a");

        link.href = url;

        link.download = "project-astro-comparison.csv";

        link.click();

        URL.revokeObjectURL(url);

    }

    // ----------------------------------
    // INITIALIZATION
    // ----------------------------------

    function initialize() {

        hazards = readStorage(STORAGE.hazards);

        const assessments = loadAssessments();

        candidates = assessments
            .map(normalizeCandidate)
            .filter(isValidCandidate);

        updateSummary();

        if (candidates.length === 0) {

            setText(
                "comparisonStatus",
                "No compatible landing assessments found. Create and save assessments in the Landing Zone Analyzer first."
            );

            return;

        }

        populateCandidates();

        setText(
            "comparisonStatus",
            "Landing assessments loaded successfully."
        );

        if (candidates.length > 1) {
            compare();
        }

    }

    // ----------------------------------
    // EVENTS
    // ----------------------------------

    $("compareSites").addEventListener(
        "click",
        compare
    );

    $("refreshComparison").addEventListener(
        "click",
        initialize
    );

    $("exportComparison").addEventListener(
        "click",
        exportCSV
    );

    initialize();

});
