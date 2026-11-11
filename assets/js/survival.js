
document.addEventListener("DOMContentLoaded", () => {
    "use strict";

    const STORAGE_KEY = "projectAstroSurvivalPlanV1";

    const $ = (id) => document.getElementById(id);

    const fields = [
        "missionName",
        "crewSize",
        "missionDays",
        "reservePercent",
        "waterRate",
        "foodRate",
        "oxygenRate",
        "powerRate",
        "stockWater",
        "stockFood",
        "stockOxygen",
        "stockPower",
        "missionNotes"
    ];

    const defaults = {
        missionName: "Ares Surface Expedition",
        crewSize: 4,
        missionDays: 180,
        reservePercent: 20,
        waterRate: 3.5,
        foodRate: 1.8,
        oxygenRate: 0.84,
        powerRate: 5,
        stockWater: 3000,
        stockFood: 1500,
        stockOxygen: 700,
        stockPower: 4000,
        missionNotes: ""
    };

    const resources = [
        {
            id: "water",
            name: "Water",
            rate: "waterRate",
            stock: "stockWater",
            unit: "L"
        },
        {
            id: "food",
            name: "Food",
            rate: "foodRate",
            stock: "stockFood",
            unit: "kg"
        },
        {
            id: "oxygen",
            name: "Oxygen",
            rate: "oxygenRate",
            stock: "stockOxygen",
            unit: "kg"
        },
        {
            id: "power",
            name: "Power",
            rate: "powerRate",
            stock: "stockPower",
            unit: "kWh"
        }
    ];

    let lastAssessment = null;

    function numberValue(id) {
        const value = Number($(id).value);

        return Number.isFinite(value) ? value : 0;
    }

    function escapeHTML(value) {
        return String(value).replace(/[&<>"']/g, (char) => ({
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        })[char]);
    }

    function formatNumber(value) {
        return Number(value).toLocaleString("en-IN", {
            maximumFractionDigits: 2
        });
    }

    function readForm() {
        const data = {};

        fields.forEach((id) => {
            const element = $(id);

            if (!element) return;

            data[id] = element.type === "number" ||
                       element.type === "range"
                ? numberValue(id)
                : element.value.trim();
        });

        return data;
    }

    function validateMission() {
        const checks = [
            ["crewSize", 1, 20, "Crew members must be between 1 and 20."],
            ["missionDays", 1, 2000, "Mission duration must be between 1 and 2000 days."],
            ["reservePercent", 0, 100, "Reserve must be between 0 and 100."]
        ];

        resources.forEach((resource) => {
            checks.push([
                resource.rate,
                0,
                10000,
                "Daily consumption cannot be negative."
            ]);

            checks.push([
                resource.stock,
                0,
                100000000,
                "Available supply cannot be negative."
            ]);
        });

        for (const [id, min, max, message] of checks) {
            const element = $(id);
            const value = numberValue(id);

            if (
                !element ||
                element.value.trim() === "" ||
                !Number.isFinite(value) ||
                value < min ||
                value > max
            ) {
                element?.focus();
                return message;
            }
        }

        if (!$("missionName").value.trim()) {
            $("missionName").focus();
            return "Please enter a mission name.";
        }

        return null;
    }

    function saveLocal() {
        try {
            const data = readForm();

            data.checklist = Array.from(
                document.querySelectorAll("#survivalChecklist input")
            ).map((checkbox) => ({
                id: checkbox.value,
                checked: checkbox.checked
            }));

            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));

            return true;
        } catch (error) {
            console.error("Could not save survival plan:", error);
            return false;
        }
    }

    function loadLocal() {
        try {
            const saved = localStorage.getItem(STORAGE_KEY);

            if (!saved) return;

            const data = JSON.parse(saved);

            fields.forEach((id) => {
                if (data[id] === undefined || !$(id)) return;
                $(id).value = data[id];
            });

            if (Array.isArray(data.checklist)) {
                data.checklist.forEach((item) => {
                    const checkbox = document.querySelector(
                        `#survivalChecklist input[value="${CSS.escape(item.id)}"]`
                    );

                    if (checkbox) {
                        checkbox.checked = Boolean(item.checked);
                    }
                });
            }

        } catch (error) {
            console.error("Could not load survival plan:", error);
        }
    }

    function updateChecklist() {
        const checkboxes = Array.from(
            document.querySelectorAll("#survivalChecklist input")
        );

        const completed = checkboxes.filter(
            (checkbox) => checkbox.checked
        ).length;

        const total = checkboxes.length;

        const percentage = total
            ? Math.round((completed / total) * 100)
            : 0;

        $("checklistCounter").textContent =
            `${completed} / ${total} COMPLETED`;

        $("checklistProgressBar").style.width =
            `${percentage}%`;

        $("checklistCaption").textContent =
            percentage === 100
                ? "All preparation tasks completed."
                : `${completed} of ${total} preparation tasks completed.`;

        saveLocal();
    }

    function calculate() {
        const validationError = validateMission();

        if (validationError) {
            $("missionFeedback").textContent = validationError;
            $("missionFeedback").style.color = "var(--danger)";
            return;
        }

        const crew = numberValue("crewSize");
        const days = numberValue("missionDays");
        const reserve = numberValue("reservePercent");

        const results = resources.map((resource) => {
            const dailyRate = numberValue(resource.rate);
            const available = numberValue(resource.stock);

            const base = dailyRate * crew * days;

            const required = base * (1 + reserve / 100);

            const balance = available - required;

            let status;
            let statusClass;

            if (available >= required) {
                status = "SUFFICIENT";
                statusClass = "good";
            } else if (available >= base) {
                status = "RESERVE GAP";
                statusClass = "low";
            } else {
                status = "SHORTAGE";
                statusClass = "bad";
            }

            const coverage = required > 0
                ? Math.min(available / required, 1)
                : 1;

            return {
                ...resource,
                dailyRate,
                base,
                required,
                available,
                balance,
                status,
                statusClass,
                coverage
            };
        });

        const shortages = results.filter(
            (item) => item.status !== "SUFFICIENT"
        ).length;

        const readiness = Math.round(
            results.reduce(
                (total, item) => total + item.coverage,
                0
            ) / results.length * 100
        );

        lastAssessment = {
            missionName: $("missionName").value.trim(),
            crew,
            days,
            reserve,
            results,
            shortages,
            readiness,
            calculatedAt: new Date().toISOString()
        };

        renderResults(lastAssessment);

        $("statDuration").textContent = `${days} Days`;

        $("statCrew").textContent =
            String(crew).padStart(2, "0");

        $("statShortages").textContent = shortages;

        $("statReadiness").textContent = `${readiness}%`;

        $("readinessCaption").textContent =
            shortages === 0
                ? "All planned supply reserves covered"
                : "Some supplies need attention";

        const feedback = $("missionFeedback");

        feedback.textContent =
            "Mission requirements calculated successfully.";

        feedback.style.color = "var(--success)";

        saveLocal();
    }

    function renderResults(assessment) {
        const tbody = $("supplyResults");

        tbody.innerHTML = assessment.results.map((item) => {
            const balanceClass = item.balance >= 0
                ? "balance-positive"
                : "balance-negative";

            const balanceText = item.balance >= 0
                ? `+${formatNumber(item.balance)}`
                : formatNumber(item.balance);

            return `
                <tr>
                    <td>
                        <strong>${escapeHTML(item.name)}</strong>
                    </td>

                    <td>${formatNumber(item.base)} ${item.unit}</td>

                    <td>
                        <strong>${formatNumber(item.required)} ${item.unit}</strong>
                    </td>

                    <td>${formatNumber(item.available)} ${item.unit}</td>

                    <td>
                        <strong class="inventory-number ${balanceClass}">
                            ${balanceText} ${item.unit}
                        </strong>
                    </td>

                    <td>
                        <span class="stock-status ${item.statusClass}">
                            ${item.status}
                        </span>
                    </td>
                </tr>
            `;
        }).join("");

        const summary = $("survivalSummary");

        if (assessment.shortages === 0) {
            summary.className = "survival-summary safe";

            summary.innerHTML = `
                <i class="bi bi-check-circle-fill"></i>
                <span>
                    All four resource categories meet the configured
                    requirement including the ${assessment.reserve}% reserve.
                    This is a supply calculation, not a complete safety certification.
                </span>
            `;
        } else {
            summary.className = "survival-summary warning";

            summary.innerHTML = `
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>
                    ${assessment.shortages} resource categories do not
                    meet the complete requirement including reserve.
                    Review the supply balances before mission approval.
                </span>
            `;
        }
    }

    function updateReserveLabel() {
        $("reserveValue").textContent =
            `${numberValue("reservePercent")}%`;
    }

    function exportMission() {
        if (!lastAssessment) {
            calculate();
        }

        if (!lastAssessment) return;

        const plan = {
            application: "PROJECT ASTRO",
            module: "Mars Survival Planner",
            mission: readForm(),
            assessment: lastAssessment,
            checklist: Array.from(
                document.querySelectorAll("#survivalChecklist input")
            ).map((checkbox) => ({
                task: checkbox.value,
                completed: checkbox.checked
            })),
            notes: $("missionNotes").value.trim(),
            disclaimer:
                "Consumption rates are user-configurable planning assumptions, not NASA-certified life-support requirements."
        };

        const blob = new Blob(
            [JSON.stringify(plan, null, 2)],
            { type: "application/json" }
        );

        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");

        link.href = url;
        link.download = "project-astro-survival-plan.json";

        document.body.appendChild(link);
        link.click();
        link.remove();

        URL.revokeObjectURL(url);

        $("saveFeedback").textContent =
            "Mission plan exported successfully.";

        $("saveFeedback").style.color = "var(--success)";
    }

    function resetMission() {
        if (!confirm("Reset all mission settings to their default values?")) {
            return;
        }

        Object.entries(defaults).forEach(([id, value]) => {
            $(id).value = value;
        });

        document.querySelectorAll(
            "#survivalChecklist input"
        ).forEach((checkbox) => {
            checkbox.checked = false;
        });

        lastAssessment = null;

        $("supplyResults").innerHTML = `
            <tr>
                <td colspan="6" class="inventory-empty">
                    Calculate requirements to view the assessment.
                </td>
            </tr>
        `;

        $("survivalSummary").textContent =
            "Run the calculation to see your mission assessment.";

        $("statDuration").textContent = "180 Days";
        $("statCrew").textContent = "04";
        $("statShortages").textContent = "0";
        $("statReadiness").textContent = "0%";
        $("readinessCaption").textContent = "Awaiting calculation";

        $("missionFeedback").textContent = "";
        $("saveFeedback").textContent = "";

        updateReserveLabel();
        updateChecklist();
        saveLocal();
    }

    $("survivalForm").addEventListener("submit", (event) => {
        event.preventDefault();
        calculate();
    });

    $("calculateSupplies").addEventListener("click", calculate);

    $("reservePercent").addEventListener(
        "input",
        updateReserveLabel
    );

    fields.forEach((id) => {
        $(id).addEventListener("change", () => {
            saveLocal();

            if (lastAssessment) {
                calculate();
            }
        });
    });

    $("survivalChecklist").addEventListener("change", updateChecklist);

    $("resetChecklist").addEventListener("click", () => {
        if (!confirm("Clear all completed checklist tasks?")) return;

        document.querySelectorAll(
            "#survivalChecklist input"
        ).forEach((checkbox) => {
            checkbox.checked = false;
        });

        updateChecklist();
    });

    $("resetMission").addEventListener("click", resetMission);

    $("saveMission").addEventListener("click", () => {
        const error = validateMission();

        if (error) {
            $("saveFeedback").textContent = error;
            $("saveFeedback").style.color = "var(--danger)";
            return;
        }

        calculate();

        if (!lastAssessment) return;

        const saved = saveLocal();

        $("saveFeedback").textContent = saved
            ? "Mission plan saved in this browser."
            : "Could not save. Check browser storage settings.";

        $("saveFeedback").style.color = saved
            ? "var(--success)"
            : "var(--danger)";
    });

    $("exportMission").addEventListener("click", exportMission);

    // Restore previous mission settings.
    loadLocal();

    updateReserveLabel();
    updateChecklist();

    // Calculate the initial mission assessment.
    calculate();
});
