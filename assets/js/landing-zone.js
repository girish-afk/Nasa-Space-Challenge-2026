
/* =========================================
   PROJECT ASTRO
   PHASE 4 — LANDING ZONE ANALYZER
   PHASE 4.3 — SHARED ASSESSMENT STORAGE
========================================= */

document.addEventListener("DOMContentLoaded", () => {

    "use strict";

    const form = document.getElementById("landingAssessmentForm");

    if (!form) return;

    const get = id => document.getElementById(id);

    const result = get("assessmentResult");
    const emptyResult = get("emptyResult");

    const STORAGE_KEY =
        "projectAstro.landingAssessments.v1";

    let latestAssessment = null;

    // Import a map position explicitly handed off from Martian Explorer.
    // The source map's planetary coordinate reference system is not validated here.
    const pendingLocationKey = "projectAstro.landingZone.pendingLocation.v1";
    const importNotice = get("explorerImportNotice");

    try {
        const pendingRaw = localStorage.getItem(pendingLocationKey);
        if (pendingRaw) {
            const pending = JSON.parse(pendingRaw);
            const latitude = Number(pending.latitude);
            const longitude = Number(pending.longitude);

            if (Number.isFinite(latitude) && latitude >= -90 && latitude <= 90 &&
                Number.isFinite(longitude) && longitude >= -180 && longitude <= 180) {
                get("siteLatitude").value = latitude.toFixed(6);
                get("siteLongitude").value = longitude.toFixed(6);
                if (!get("siteName").value.trim()) {
                    get("siteName").value = "Explorer Map Candidate";
                }
                if (importNotice) {
                    importNotice.hidden = false;
                    importNotice.textContent = "Coordinates imported from Martian Explorer. They are map-service coordinates only; verify the Mars coordinate reference system and enter measured terrain parameters before interpreting the screening score.";
                }
            } else if (importNotice) {
                importNotice.hidden = false;
                importNotice.textContent = "A saved Explorer position could not be imported because its coordinates were outside the accepted numeric range.";
            }
            localStorage.removeItem(pendingLocationKey);
        }
    } catch (error) {
        console.error("Explorer coordinate import failed:", error);
        if (importNotice) {
            importNotice.hidden = false;
            importNotice.textContent = "Explorer coordinates could not be imported from browser storage.";
        }
    }

    // =====================================
    // EDUCATIONAL SCREENING MODEL
    // NOT OFFICIAL NASA STANDARDS
    // =====================================

    const MODEL = {

        slope: [
            { max: 5, score: 100, label: "Favorable" },
            { max: 10, score: 60, label: "Moderate" },
            { max: Infinity, score: 20, label: "Unfavorable" }
        ],

        elevation: [
            { max: 10, score: 100, label: "Favorable" },
            { max: 25, score: 60, label: "Moderate" },
            { max: Infinity, score: 20, label: "Unfavorable" }
        ],

        rocks: [
            { max: 5, score: 100, label: "Favorable" },
            { max: 15, score: 60, label: "Moderate" },
            { max: Infinity, score: 20, label: "Unfavorable" }
        ],

        temperature: [
            { max: 40, score: 100, label: "Favorable" },
            { max: 60, score: 60, label: "Moderate" },
            { max: Infinity, score: 20, label: "Unfavorable" }
        ]

    };

    const WEIGHTS = {
        slope: 0.35,
        elevation: 0.25,
        rocks: 0.20,
        temperature: 0.20
    };

    // =====================================
    // SHARED ASSESSMENT STORAGE
    // =====================================

    function loadSavedAssessments() {

        try {

            const data = JSON.parse(
                localStorage.getItem(STORAGE_KEY) || "[]"
            );

            return Array.isArray(data) ? data : [];

        } catch (error) {

            console.error(
                "Assessment loading error:",
                error
            );

            return [];

        }

    }

    function saveAssessment(assessment) {

        try {

            const assessments = loadSavedAssessments();

            const index = assessments.findIndex(
                item => item.id === assessment.id
            );

            if (index >= 0) {

                assessments[index] = assessment;

            } else {

                assessments.push(assessment);

            }

            localStorage.setItem(
                STORAGE_KEY,
                JSON.stringify(assessments)
            );

            return true;

        } catch (error) {

            console.error(
                "Assessment saving error:",
                error
            );

            alert(
                "Unable to save assessment. Browser storage may be unavailable or full."
            );

            return false;

        }

    }

    // =====================================
    // UTILITIES
    // =====================================

    function evaluate(value, criteria) {

        return criteria.find(
            rule => value <= rule.max
        );

    }

    function setText(id, value) {

        const element = get(id);

        if (element) {
            element.textContent = value;
        }

    }

    function generateId() {

        if (window.crypto?.randomUUID) {
            return window.crypto.randomUUID();
        }

        return `assessment-${Date.now()}-${Math.random()
            .toString(36).slice(2)}`;

    }

    // =====================================
    // ASSESSMENT CALCULATION
    // =====================================

    function calculateAssessment(data) {

        const slope = evaluate(
            data.slope,
            MODEL.slope
        );

        const elevation = evaluate(
            data.elevation,
            MODEL.elevation
        );

        const rocks = evaluate(
            data.rocks,
            MODEL.rocks
        );

        const temperature = evaluate(
            data.temperatureRange,
            MODEL.temperature
        );

        const factors = {

            slope: {
                ...slope,
                weight: WEIGHTS.slope
            },

            elevation: {
                ...elevation,
                weight: WEIGHTS.elevation
            },

            rocks: {
                ...rocks,
                weight: WEIGHTS.rocks
            },

            temperature: {
                ...temperature,
                weight: WEIGHTS.temperature
            }

        };

        const score = Math.round(

            factors.slope.score * factors.slope.weight +

            factors.elevation.score * factors.elevation.weight +

            factors.rocks.score * factors.rocks.weight +

            factors.temperature.score * factors.temperature.weight

        );

        let category;
        let summary;

        if (score >= 80) {

            category = "LOW SCREENING CONCERN";

            summary =
                "The entered parameters score favorably under this preliminary screening model. Further engineering validation is required.";

        } else if (score >= 55) {

            category = "MODERATE SCREENING CONCERN";

            summary =
                "Some entered parameters require further investigation before this location can be considered.";

        } else {

            category = "HIGH SCREENING CONCERN";

            summary =
                "Multiple entered parameters score unfavorably. Detailed terrain and engineering investigation is recommended.";

        }

        return {

            id: generateId(),

            data,

            factors,

            score,

            category,

            summary,

            assessedAt: new Date().toLocaleString(),

            savedAt: new Date().toISOString()

        };

    }

    // =====================================
    // FACTOR VISUALIZATION
    // =====================================

    function renderFactor(id, factor) {

        setText(
            id + "Score",
            `${factor.score}/100 · ${factor.label}`
        );

        const bar = get(id + "Bar");

        if (!bar) return;

        bar.style.width = factor.score + "%";

        if (factor.score === 100) {

            bar.style.background = "var(--success)";

        } else if (factor.score === 60) {

            bar.style.background = "var(--warning)";

        } else {

            bar.style.background = "var(--danger)";

        }

    }

    // =====================================
    // ASSESSMENT FINDINGS
    // =====================================

    function generateFindings(assessment) {

        const { data, factors } = assessment;

        const findings = [

            {
                label: `Surface slope: ${data.slope}°. ${factors.slope.label} under screening assumptions.`
            },

            {
                label: `Elevation variation: ${data.elevation} m. ${factors.elevation.label} under screening assumptions.`
            },

            {
                label: `Rock coverage: ${data.rocks}%. ${factors.rocks.label} under screening assumptions.`
            },

            {
                label: `Temperature spread: ${data.temperatureRange}°C. ${factors.temperature.label} under screening assumptions.`
            }

        ];

        const list = get("assessmentFindings");

        if (!list) return;

        list.replaceChildren();

        findings.forEach(item => {

            const li = document.createElement("li");

            li.textContent = item.label;

            list.appendChild(li);

        });

    }

    // =====================================
    // DISPLAY ASSESSMENT
    // =====================================

    function displayAssessment(assessment) {

        latestAssessment = assessment;

        if (emptyResult) emptyResult.hidden = true;

        if (result) result.hidden = false;

        setText("totalScore", assessment.score);

        setText("scoreCategory", assessment.category);

        setText("scoreSummary", assessment.summary);

        setText("resultSiteName", assessment.data.name);

        setText(
            "resultCoordinates",
            `Latitude ${assessment.data.latitude}° · Longitude ${assessment.data.longitude}°`
        );

        const ring = get("scoreRing");

        if (ring) {

            ring.style.background = `
                conic-gradient(
                    var(--accent) 0deg,
                    var(--accent) ${assessment.score * 3.6}deg,
                    var(--bg-elevated) ${assessment.score * 3.6}deg,
                    var(--bg-elevated) 360deg
                )
            `;

        }

        const categoryElement = get("scoreCategory");

        if (categoryElement) {

            categoryElement.className = "score-category";

            if (assessment.score >= 80) {

                categoryElement.classList.add("low");

            } else if (assessment.score < 55) {

                categoryElement.classList.add("high");

            }

        }

        renderFactor("slope", assessment.factors.slope);

        renderFactor("elevation", assessment.factors.elevation);

        renderFactor("rock", assessment.factors.rocks);

        renderFactor("temperature", assessment.factors.temperature);

        generateFindings(assessment);

        result?.scrollIntoView({

            behavior: window.matchMedia(
                "(prefers-reduced-motion: reduce)"
            ).matches ? "auto" : "smooth",

            block: "nearest"

        });

    }

    // =====================================
    // FORM SUBMISSION
    // =====================================

    form.addEventListener("submit", event => {

        event.preventDefault();

        if (!form.reportValidity()) return;

        const data = {

            name: get("siteName").value.trim(),

            latitude: Number(get("siteLatitude").value),

            longitude: Number(get("siteLongitude").value),

            slope: Number(get("slope").value),

            elevation: Number(get("elevationVariation").value),

            rocks: Number(get("rockCoverage").value),

            minTemperature: Number(get("minTemperature").value),

            maxTemperature: Number(get("maxTemperature").value)

        };

        const values = [

            data.latitude,
            data.longitude,
            data.slope,
            data.elevation,
            data.rocks,
            data.minTemperature,
            data.maxTemperature

        ];

        if (!data.name || !values.every(Number.isFinite)) {

            alert("Please enter valid values in all assessment fields.");

            return;

        }

        if (
            data.latitude < -90 ||
            data.latitude > 90 ||
            data.longitude < -180 ||
            data.longitude > 180
        ) {

            alert("Coordinates must be within valid latitude and longitude ranges.");

            return;

        }

        if (
            data.slope < 0 ||
            data.elevation < 0 ||
            data.rocks < 0 ||
            data.rocks > 100
        ) {

            alert("Please enter valid non-negative terrain values and rock coverage between 0 and 100.");

            return;

        }

        if (data.minTemperature > data.maxTemperature) {

            get("maxTemperature").setCustomValidity(
                "Maximum temperature must be greater than or equal to minimum temperature."
            );

            get("maxTemperature").reportValidity();

            get("maxTemperature").setCustomValidity("");

            return;

        }

        data.temperatureRange =
            data.maxTemperature - data.minTemperature;

        const assessment = calculateAssessment(data);

        // Save assessment for Phase 4.3.
        const saved = saveAssessment(assessment);

        // Preserve existing result display.
        displayAssessment(assessment);

        if (saved) {

            console.log(
                "PROJECT ASTRO: Assessment saved.",
                assessment.id
            );

        }

    });

    // =====================================
    // RESET
    // =====================================

    form.addEventListener("reset", () => {

        latestAssessment = null;

        window.setTimeout(() => {

            if (result) result.hidden = true;

            if (emptyResult) emptyResult.hidden = false;

            setText("totalScore", "--");

            setText("scoreCategory", "NOT ASSESSED");

            const ring = get("scoreRing");

            if (ring) {

                ring.style.background =
                    "conic-gradient(var(--bg-elevated) 360deg, var(--bg-elevated) 360deg)";

            }

            ["slope", "elevation", "rock", "temperature"].forEach(id => {

                setText(id + "Score", "—");

                const bar = get(id + "Bar");

                if (bar) bar.style.width = "0%";

            });

        }, 0);

    });

    // =====================================
    // DOWNLOAD ASSESSMENT REPORT
    // =====================================

    get("downloadAssessment")?.addEventListener("click", () => {

        if (!latestAssessment) return;

        const a = latestAssessment;

        const f = a.factors;

        const report = `
PROJECT ASTRO
MARS LANDING ZONE ASSESSMENT REPORT

==================================================

ASSESSMENT INFORMATION

Assessment ID: ${a.id}
Site Name: ${a.data.name}
Latitude: ${a.data.latitude}°
Longitude: ${a.data.longitude}°
Assessment Date: ${a.assessedAt}

==================================================

ENTERED TERRAIN PARAMETERS

Surface Slope: ${a.data.slope}°
Elevation Variation: ${a.data.elevation} m
Rock Coverage: ${a.data.rocks}%

Minimum Temperature: ${a.data.minTemperature} °C
Maximum Temperature: ${a.data.maxTemperature} °C
Temperature Spread: ${a.data.temperatureRange} °C

==================================================

SCREENING RESULTS

Overall Score: ${a.score}/100
Classification: ${a.category}

Surface Slope: ${f.slope.score}/100
Elevation Variation: ${f.elevation.score}/100
Rock Coverage: ${f.rocks.score}/100
Temperature Range: ${f.temperature.score}/100

==================================================

WEIGHT DISTRIBUTION

Surface Slope: 35%
Elevation Variation: 25%
Rock Coverage: 20%
Temperature Range: 20%

==================================================

ASSESSMENT SUMMARY

${a.summary}

==================================================

IMPORTANT SCIENTIFIC LIMITATIONS

1. This is an educational preliminary screening model.
2. Inputs are supplied by the user.
3. No live NASA measurements were retrieved.
4. Thresholds are illustrative assumptions, not NASA standards.
5. No actual landing safety certification is provided.
6. Coordinate reference system has not been validated.
7. Additional terrain, dust, rock distribution, thermal,
   atmospheric and engineering analysis is necessary.

PROJECT ASTRO
Interplanetary Survival Guide: Martian Map
NASA Space Apps Challenge 2026
`;

        const blob = new Blob([report], {

            type: "text/plain;charset=utf-8"

        });

        const url = URL.createObjectURL(blob);

        const link = document.createElement("a");

        const safeName = a.data.name

            .toLowerCase()

            .replace(/[^a-z0-9]+/g, "-")

            .replace(/^-|-$/g, "") || "landing-site";

        link.href = url;

        link.download = `astro-${safeName}-assessment.txt`;

        document.body.appendChild(link);

        link.click();

        link.remove();

        URL.revokeObjectURL(url);

    });

});
