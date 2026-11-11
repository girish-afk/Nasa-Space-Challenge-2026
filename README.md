# PROJECT ASTRO
### Interplanetary Survival Guide: Martian Map
**NASA Space Apps Challenge 2026 — Mars Exploration & Mission Planning Platform**

PROJECT ASTRO is an educational, browser-based Mars exploration workspace. It brings together an interactive Martian map, landing-site assessment, hazard review, environmental information, resource planning, rover navigation and mission-support tools.

> **Important:** This is an educational mission-planning prototype. Scores, hazards and planning outputs are not certified engineering advice or real-time mission-safety data.

---

## Table of Contents
1. [Project Overview](#project-overview)
2. [Core Modules](#core-modules)
3. [Technology Stack](#technology-stack)
4. [Project Structure](#project-structure)
5. [Interactive User Guide](#interactive-user-guide)
6. [Data, Saving and Browser Storage](#data-saving-and-browser-storage)
7. [NASA API Configuration](#nasa-api-configuration)
8. [Run Locally with Docker](#run-locally-with-docker)
9. [Deploy to Vercel](#deploy-to-vercel)
10. [Post-Deployment Verification](#post-deployment-verification)
11. [Troubleshooting](#troubleshooting)
12. [Known Limitations and Scientific Context](#known-limitations-and-scientific-context)
13. [Security and Contribution Guidelines](#security-and-contribution-guidelines)

---

## Project Overview

Future human and robotic missions to Mars must evaluate terrain, landing locations, environmental conditions, hazards, resources and travel routes before operating on the surface.

PROJECT ASTRO provides a single interface for exploring these mission-planning topics. Users can move between specialized modules, inspect map locations, record planning information, compare candidate landing areas and estimate mission resource requirements.

### Project goals
- Make Mars exploration information easier to explore through one workspace.
- Provide interactive map-based exploration and coordinate inspection.
- Demonstrate a structured landing-site and hazard-assessment workflow.
- Help users understand resource requirements for a planned surface mission.
- Support rover route planning and mission documentation.
- Present scientific context while clearly identifying illustrative or historical data.

---

## Core Modules

| Module | Page | Purpose |
|---|---|---|
| Mission Gateway | `index.php` | Project introduction and navigation to the platform |
| Mission Dashboard | `dashboard.php` | Mission overview, indicators, charts and shortcuts |
| Martian Explorer | `explorer.php` | Explore the map, inspect coordinates and save locations |
| Landing Zone Analyzer | `landing-zone.php` | Enter site parameters and generate an educational assessment |
| Landing Site Comparison | `landing-comparison.php` | Compare available landing assessments and candidates |
| Hazard Detection | `hazards.php` | Review, manage and assess illustrative surface hazards |
| Weather & Environment | `weather.php` | View available Mars weather observations and charts |
| Water & Resources | `resources.php` | Explore resource information and manage planning inventory/checklists |
| Rover Route Planner | `route-planner.php` | Create, inspect and save rover route plans |
| Survival Planner | `survival.php` | Estimate crew resource requirements and remaining supplies |
| Mission Reports | `reports.php` | Review and generate a consolidated mission report interface |
| System Settings | `settings.php` | Configure supported appearance, units and interface preferences |

---

## Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript
- **UI framework:** Bootstrap 5
- **Icons:** Bootstrap Icons
- **Typography:** Inter (Google Fonts)
- **Maps:** Leaflet.js
- **Charts:** Chart.js
- **Backend:** PHP 8+
- **External data:** NASA APIs / NASA InSight archived weather observations
- **Deployment:** Docker container using FrankenPHP and Caddy; Vercel Container runtime
- **Client-side persistence:** Browser `localStorage`

External CDN libraries and Google Fonts require an internet connection. The project does not require a MySQL database in its current browser-storage-based configuration.

---

## Project Structure

```text
Nasa-Space-Challenge-2026/
│
├── index.php                     # Public project gateway / homepage
├── dashboard.php                 # Mission dashboard
├── explorer.php                  # Interactive Mars explorer
├── landing-zone.php              # Landing zone assessment
├── landing-comparison.php        # Landing candidate comparison
├── hazards.php                   # Hazard detection interface
├── weather.php                   # Mars weather and environment
├── resources.php                 # Water and resource planning
├── route-planner.php             # Rover route planning
├── survival.php                  # Crew survival calculations
├── reports.php                   # Mission reports
├── settings.php                  # User interface settings
│
├── api/
│   ├── weather.php               # Weather data endpoint
│   ├── resources.php             # Resource catalogue endpoint
│   ├── locations.php             # Location data endpoint
│   ├── hazards.php               # Hazard data endpoint
│   └── routes.php                # Route endpoint (not durable on stateless hosting)
│
├── config/
│   ├── app.php                   # Application constants and PHP settings
│   └── nasa.php                  # NASA API key environment configuration
│
├── includes/
│   ├── navbar.php                # Shared application navigation
│   ├── sidebar.php               # Shared sidebar
│   ├── footer.php                # Shared footer
│   └── functions.php             # Shared PHP helper functions
│
├── assets/
│   ├── css/
│   │   ├── style.css             # Main application styles
│   │   ├── theme.css             # Theme and appearance styles
│   │   ├── modules.css           # Shared module styles
│   │   └── gateway.css           # Homepage / gateway styles
│   │
│   ├── js/
│   │   ├── app.js                # Global preferences and interface behavior
│   │   ├── dashboard.js          # Dashboard clock and charts
│   │   ├── explorer.js           # Explorer map interactions
│   │   ├── landing-zone.js       # Landing assessment interactions
│   │   ├── landing-comparison.js # Candidate comparison logic
│   │   ├── hazards.js            # Hazard management and assessment
│   │   ├── weather.js            # Weather data and charts
│   │   ├── resources.js          # Resource inventory and checklist
│   │   ├── route-planner.js      # Rover route map and route operations
│   │   ├── survival.js           # Crew resource calculations
│   │   ├── reports.js            # Mission report assembly
│   │   ├── settings.js           # Settings controls and persistence
│   │   └── modules.js            # Module-specific mission notes
│   │
│   └── images/
│       └── mars-landscape.jpg    # Local Mars landscape asset
│
├── storage/
│   ├── index.html                # Prevents directory listing
│   ├── .htaccess                 # Storage access rules for compatible servers
│   ├── mars-weather-cache.json   # Bundled historical fallback sample
│   └── routes.json               # Legacy file-backed route data
│
├── .env.example                  # Example environment variable (no real key)
├── .gitignore
├── .dockerignore
├── .htaccess
├── Caddyfile                     # FrankenPHP / Caddy web-server configuration
├── Dockerfile.vercel             # Container build definition for Vercel
└── README.md
```

---

## Interactive User Guide

The following guide describes the intended workflow and the interactions implemented by the project's PHP and JavaScript modules. Exact controls may vary slightly with the current page layout.

### 1. Mission Gateway
1. Open the project homepage.
2. Use the top navigation to jump to the Mission, Modules, Mars Explorer or Weather sections.
3. Select **Enter Mission Control** or **Mission Dashboard** to open the dashboard.
4. Select a module card to open its dedicated page.

### 2. Mission Dashboard
1. Open **Mission Dashboard** from the gateway or sidebar.
2. Review the mission overview, key indicators and available charts.
3. Use the module shortcuts to move to the relevant planning tool.
4. The mission clock and date are generated from the visitor's device clock.
5. If the chart library does not load, chart areas may show a fallback message instead of a chart.

### 3. Martian Explorer
1. Open **Martian Explorer**.
2. Wait for the Leaflet map to load.
3. Pan by dragging the map; zoom with the map controls or mouse/touch gestures.
4. Select a map location to inspect its displayed latitude and longitude.
5. Use the page's available save controls to keep a location in the browser.
6. Review saved locations and use the available actions to revisit or remove entries.
7. Where the hand-off control is available, send the selected map location to the Landing Zone Analyzer.
8. If the map is blank or unavailable, check the internet connection and whether Leaflet's CDN resources are being blocked.

### 4. Landing Zone Analyzer
1. Open **Landing Zone Analyzer**.
2. Enter the available candidate-site information in the assessment form.
3. Submit the assessment using the page's assessment action.
4. Review the generated result and its displayed criteria.
5. If a location was sent from the Explorer, review the imported-location notice and verify the coordinates before assessing.
6. Save or reuse the assessment through the controls provided by the page.

**Interpretation:** The result is a prototype screening aid based on the project's configured criteria. It is not a real landing certification or a substitute for engineering analysis.

### 5. Landing Site Comparison
1. Open **Landing Site Comparison**.
2. Review the landing assessments/candidates available in this browser.
3. Compare the displayed criteria, scores and chart visualization.
4. Use the available candidate controls to inspect or select candidates.
5. If no candidates appear, first create and save assessments in the Landing Zone Analyzer in the same browser.

### 6. Hazard Detection
1. Open **Hazard Detection**.
2. Review the hazard records and severity categories.
3. Use the available form controls to add or update hazard entries.
4. Inspect hazard type, severity and location information.
5. Review the safe-candidate or assessment outputs where available.
6. Use the page's delete/reset controls carefully; removed browser-stored records may not be recoverable.

Hazard categories used by the application include impact craters, rock fields, large boulders, steep slopes and other obstructions. The displayed records and classifications are educational/illustrative unless explicitly identified as sourced observations.

### 7. Weather & Environment
1. Open **Weather & Environment**.
2. Allow the page to request data from `api/weather.php`.
3. Review the temperature, pressure, wind and dust information displayed by the page.
4. Inspect the available historical chart and observation information.
5. Read the status/notice shown by the module to determine whether the response is NASA data or the bundled fallback sample.

**Data warning:** The project uses NASA InSight archived observations. NASA's InSight lander mission ended on **December 15, 2022**. Do not interpret the bundled October 2020 fallback sample as current Martian weather.

### 8. Water & Resources
1. Open **Water & Resources**.
2. Browse the resource catalogue returned by the resource endpoint.
3. Review the available resource information.
4. Use the inventory controls to add or update planned quantities.
5. Use the checklist controls to mark planning tasks complete or incomplete.
6. Reload the page to confirm that browser-saved inventory and checklist information remains available.

### 9. Rover Route Planner
1. Open **Rover Route Planner**.
2. Wait for the Leaflet route map to load.
3. Use the map and route controls to select route locations/waypoints.
4. Review the route information and any displayed distance or planning outputs.
5. Use the save action to store a route in the current browser.
6. Reopen saved routes using the page's saved-route controls.
7. Review the route before using it in any hypothetical mission scenario.

Route calculations and displayed paths are educational planning outputs, not validated rover navigation commands.

### 10. Survival Planner
1. Open **Survival Planner**.
2. Enter a mission name, crew size and mission duration.
3. Adjust the reserve percentage and per-person resource consumption rates.
4. Enter available starting stock for water, food, oxygen and power.
5. Review the calculated mission requirements, remaining supplies and shortage/surplus indicators.
6. Add mission notes if required.
7. Save the plan using the page's available control.
8. Reopen the page to confirm that the saved plan is restored in the same browser.

Always verify units and assumptions before interpreting any calculated result.

### 11. Mission Reports
1. Open **Mission Reports**.
2. Allow the report interface to collect the information available from the project's modules.
3. Review the displayed module status, planning records, charts and report sections.
4. Use the report actions available on the page to generate, print or export the report.
5. If some sections are empty, create the corresponding module data first and reload the report page.

Reports summarize the information available to the browser at the time of generation. They are not official NASA mission documents.

### 12. System Settings
1. Open **System Settings**.
2. Choose the supported appearance theme and accent color.
3. Configure compact mode and reduced-motion preference if available.
4. Select supported measurement, distance and temperature units.
5. Choose the available default map style.
6. Toggle the available hazard, resource and report alerts.
7. Save/apply the settings using the page controls.
8. Navigate to another module and verify that the shared interface preferences are applied.

### 13. Mission Notes
Where a module note panel is present:
1. Enter a short mission note.
2. Submit it using the note form.
3. Review the note in the module's note list.
4. Use the available delete control to remove an entry.

Notes are stored separately for each module in the current browser.

---

## Data, Saving and Browser Storage

The current application uses browser `localStorage` for many user-created planning records and preferences. This means:

- Data is saved in the browser on the device where it was entered.
- Data is generally available after a refresh in the same browser profile.
- Data does not automatically sync between browsers, devices or users.
- Clearing site data/browser storage can delete saved records.
- Private/incognito sessions may discard data when the session ends.
- A different domain or subdomain has a separate storage origin. For example, `project-astro.in` and `www.project-astro.in` do not share localStorage.
- Vercel container files are not a reliable persistent database.

Examples of storage keys used by the JavaScript modules include:

| Feature | Browser storage key |
|---|---|
| Appearance and preferences | `projectAstro.settings.v1` |
| Commander profile | `projectAstro.commanderProfile.v1` |
| Explorer saved locations | `projectAstro.marsExplorer.savedLocations.v1` |
| Landing assessments | `projectAstro.landingAssessments.v1` |
| Pending Explorer location hand-off | `projectAstro.landingZone.pendingLocation.v1` |
| Hazard records | `projectAstro.hazards.v1` |
| Safe candidates | `projectAstro.safeCandidates.v1` |
| Resource inventory | `projectAstro.resources.inventory.v1` |
| Resource checklist | `projectAstro.resources.checklist.v1` |
| Survival plan | `projectAstroSurvivalPlanV1` |
| Saved rover routes | `projectAstroSavedRoutesV1` |
| Module notes | `projectAstro.notes.<page>.v1` |
| Mission report archive | `projectAstroReportsArchiveV1` |

Do not manually edit these keys unless you understand the data format. The names are implementation details and may change in future versions.

---

## NASA API Configuration

The NASA API key is read server-side from the `NASA_API_KEY` environment variable by `config/nasa.php`.

### Get an API key
1. Visit the [NASA Open APIs portal](https://api.nasa.gov/).
2. Request your own API key.
3. Keep the key private.

### Configure locally
Set the environment variable in the environment that runs PHP/container services:

```bash
NASA_API_KEY=your_actual_key
```

### Configure on Vercel
1. Open the Vercel project.
2. Go to **Settings → Environment Variables**.
3. Add:
   - **Name:** `NASA_API_KEY`
   - **Value:** your NASA API key
4. Select the required deployment environments (Production and Preview as needed).
5. Save the variable.
6. Redeploy the project so the running container receives the updated value.

The repository's `.env.example` is only a template. It is not a place to store a real key.

### API behavior
- `config/nasa.php` reads the environment variable; it does not contain a hard-coded key.
- The weather endpoint may request NASA data when a key and upstream service are available.
- If the key is missing or NASA cannot be reached, the weather endpoint can return the bundled historical sample with a warning.
- A successful weather response may be cached in `storage/`, but this write is best-effort and must not be treated as durable storage on Vercel.

Never commit a real API key. If a key is exposed publicly, revoke it and create a replacement.

---

## Run Locally with Docker

### Requirements
- Git (optional, for cloning)
- Docker Desktop
- Internet access for external libraries and NASA requests

### Steps

1. Open a terminal in the project root — the folder containing `Dockerfile.vercel` and `Caddyfile`.
2. Build the container:

```bash
docker build -f Dockerfile.vercel -t project-astro .
```

3. Run it:

```bash
docker run --rm -p 8080:80 -e NASA_API_KEY="YOUR_NASA_API_KEY" project-astro
```

4. Open:

```text
http://localhost:8080
```

To stop the container, press `Ctrl + C` in the terminal.

If you do not want to configure a NASA key for a basic UI check, omit the `-e NASA_API_KEY=...` option. The weather module may then use its historical fallback sample and display the relevant warning.

---

## Deploy to Vercel

This repository is configured for a **Vercel Container deployment** using FrankenPHP. It is not a static HTML deployment.

### Before importing the repository
The GitHub repository root must contain these files:

```text
Dockerfile.vercel
Caddyfile
index.php
config/
includes/
assets/
api/
```

If the files are inside a nested `Nasa-Space-Challenge-2026/` directory, either:
- Set Vercel **Root Directory** to `Nasa-Space-Challenge-2026`, or
- Move/upload the contents of that folder to the repository root.

Do not leave Vercel's Root Directory pointing at a folder that does not contain `Dockerfile.vercel`.

### Deployment steps
1. Push the complete project to a GitHub repository.
2. Open [Vercel](https://vercel.com/) and choose **Add New → Project**.
3. Import the correct GitHub repository.
4. Set the **Root Directory** to the folder containing `Dockerfile.vercel` and `Caddyfile`.
5. If Vercel asks for an application preset/runtime, choose the **Container** option where available.
6. Add the `NASA_API_KEY` environment variable as described above.
7. Deploy the project.
8. Open the deployment's generated `vercel.app` URL and verify the homepage and modules.
9. Add the custom domain in **Project → Settings → Domains** only after the generated Vercel URL works correctly.
10. Follow Vercel's displayed DNS instructions at your domain provider. DNS changes can take time to propagate.

### Custom domain and `www`
- Add the apex domain (for example, `project-astro.in`) and the `www` hostname separately if both should work.
- Follow the exact DNS records shown by Vercel for each hostname.
- Choose one canonical hostname and configure the other to redirect to it using Vercel's domain settings.
- Test both HTTPS URLs after DNS verification.
- Browser storage is origin-specific, so switching between apex and `www` can make previously saved browser data appear absent.

### Important deployment detail
The `Dockerfile.vercel` copies the project into `/app`, enables PHP cURL and uses the supplied Caddy configuration. The container must listen on the port expected by the deployment platform. Do not change the Dockerfile, Caddyfile or runtime settings without checking their compatibility together.

---

## Post-Deployment Verification

After every deployment, use this checklist:

- [ ] The root URL opens the PROJECT ASTRO gateway.
- [ ] The page is not blank and the browser console has no fatal JavaScript errors.
- [ ] CSS, fonts and images load.
- [ ] Navigation links open the correct PHP pages.
- [ ] Mission Dashboard loads.
- [ ] Mars Explorer map loads and responds to pan/zoom.
- [ ] Selecting a map point updates the displayed coordinates.
- [ ] Landing Zone Analyzer accepts an assessment and displays a result.
- [ ] Landing Site Comparison can read available assessments.
- [ ] Hazard Detection loads its records and controls.
- [ ] Weather page shows either an API response or a clearly labelled fallback warning.
- [ ] Resources page loads its catalogue and local inventory/checklist controls.
- [ ] Rover Route Planner map loads and route controls respond.
- [ ] Survival Planner calculations respond to changed inputs.
- [ ] Mission Reports opens and displays available report content.
- [ ] Settings persist after refresh in the same browser.
- [ ] No secret API key is visible in page source, JavaScript assets or GitHub.

### Useful direct endpoint checks

Replace the hostname with your deployed domain:

```text
https://project-astro.in/
https://project-astro.in/dashboard.php
https://project-astro.in/explorer.php
https://project-astro.in/api/weather.php
https://project-astro.in/api/resources.php
https://project-astro.in/locations.php
https://project-astro.in/api/hazards.php
```

An endpoint returning JSON or a documented error is different from a PHP page rendering successfully. Check both the page and its dependent API requests.

---

## Troubleshooting

### 1. Vercel deployment opens a blank page
1. Confirm that the deployment is using the Container runtime and the repository root is correct.
2. Confirm `Dockerfile.vercel`, `Caddyfile` and `index.php` are in the selected Root Directory.
3. Open the deployment's **Build Logs** and **Runtime Logs** in Vercel.
4. Open browser Developer Tools → **Console** and **Network**.
5. Check whether the document request returns a successful HTTP status and whether CSS/JavaScript/API requests fail.
6. Test the generated `vercel.app` URL before debugging the custom domain.
7. Use the deployed URL's exact hostname consistently while testing.

A blank page alone does not identify the cause. Check the HTTP response, PHP/container logs and browser console before changing code.

### 2. Homepage works but a module does not
- Open the failing PHP page directly.
- Check the Network tab for failed JavaScript, CSS or API requests.
- Confirm that relative paths remain valid from the deployed root.
- Check runtime logs for PHP errors.
- Verify that the module's JavaScript file is present in `assets/js/`.

### 3. Mars map is blank
- Check the browser console for Leaflet errors.
- Confirm Leaflet CSS and JavaScript CDN requests are not blocked.
- Check the internet connection, browser extensions and content blockers.
- Reload after the CDN resources become available.

### 4. Weather data is unavailable
- Verify that `NASA_API_KEY` is configured in the correct Vercel environment.
- Redeploy after changing the environment variable.
- Check the response from `/api/weather.php`.
- Confirm whether the page is displaying the historical fallback rather than current observations.
- Do not label archived observations as live weather.

### 5. Saved information is missing
- Use the same browser profile and hostname that originally saved the data.
- Check whether browser site data was cleared.
- Remember that apex and `www` domains have separate local storage.
- Remember that local storage does not sync across devices.

### 6. CSS or JavaScript changes do not appear
- Hard refresh the page (`Ctrl + Shift + R`).
- Check the Network tab to confirm the latest asset was loaded.
- Check for cached assets or an old deployment URL.
- Redeploy after pushing the changes to the connected Git branch.

---

## Known Limitations and Scientific Context

1. **Historical weather:** The weather module uses NASA InSight archived observations. The InSight mission ended on December 15, 2022. The bundled October 2020 sample is historical fallback data, not current weather.
2. **Illustrative hazards:** Hazard records and some mission-planning values are educational examples, not verified real-time NASA hazard observations.
3. **Screening scores:** Landing-zone and route-planning scores are educational screening aids, not certified mission-safety assessments.
4. **No shared user database:** The current implementation does not provide durable multi-user accounts or synchronized planning data.
5. **Browser-local records:** Saved routes, settings, notes and plans are tied to the browser/device and origin.
6. **Stateless hosting:** Vercel container instances are not persistent file storage. Do not depend on writes to `storage/` for long-term data.
7. **External dependencies:** Leaflet, Chart.js, Bootstrap, Bootstrap Icons and Google Fonts may require network access.
8. **Not operational guidance:** Do not use this prototype to make real spacecraft, rover, crew-health, landing or survival decisions.

---

## Security and Contribution Guidelines

- Keep `NASA_API_KEY` in environment variables; never put it in source code or frontend JavaScript.
- Do not commit `.env` files containing secrets.
- Keep `.env.example` limited to placeholder values.
- Do not expose PHP errors to public production visitors.
- Validate and sanitize all new user-controlled input on the server and escape output when rendering HTML.
- Do not treat browser `localStorage` as secure authentication or authorization.
- Before adding shared accounts or cross-device persistence, design and configure a managed database and proper server-side authorization.
- Preserve the existing PHP routes, API paths, asset paths and module integrations when modifying the project.
- Test the affected module and the full post-deployment checklist after changes.
- Clearly distinguish NASA-sourced information, historical observations, calculated estimates and illustrative sample data.

### Suggested development workflow
1. Inspect the existing page, its PHP includes, related JavaScript and CSS before editing.
2. Identify the root cause or exact requested change.
3. Make the smallest targeted change that preserves existing behavior.
4. Test the affected interaction locally.
5. Check browser console and Network errors.
6. Verify related modules that share data or settings.
7. Deploy and repeat the post-deployment verification checklist.

---

## Credits and Data Sources

- [NASA Open APIs](https://api.nasa.gov/)
- [NASA InSight Mission](https://mars.nasa.gov/insight/)
- [NASA](https://www.nasa.gov/)
- [Leaflet](https://leafletjs.com/)
- [Chart.js](https://www.chartjs.org/)
- [Bootstrap](https://getbootstrap.com/)

---

**PROJECT ASTRO — Explore Mars. Plan Beyond Earth.**
