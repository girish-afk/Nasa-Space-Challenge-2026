# PROJECT ASTRO

A PHP-based educational Mars exploration and mission-planning interface. The existing PHP pages, CSS, and JavaScript modules are retained.

## Deploy to Vercel

This project uses Vercel's Container runtime with FrankenPHP. The repository root must contain `Dockerfile.vercel` and `Caddyfile`.

1. Upload the **contents** of this `Project-Astro` folder to the root of a GitHub repository. Do not put them one folder deeper unless you set Vercel's Root Directory to that folder.
2. In Vercel, import the GitHub repository. Choose the **Container** Application Preset if Vercel asks for one. Keep Root Directory as `./` when the files above are at repository root.
3. Expand **Environment Variables** and add `NASA_API_KEY` with your new NASA API key. Add it to Production and Preview (Development is optional).
4. Deploy. After changing an environment variable, redeploy so the container receives it.
5. Open the generated `*.vercel.app` URL and test the dashboard, modules, and `/api/weather.php`.

Vercel detects `Dockerfile.vercel` at the project root and builds it as a container deployment. The container must run an HTTP server that listens on `$PORT`.

## Run locally

Requirements: Docker Desktop.

From this folder:

```bash
docker build -f Dockerfile.vercel -t project-astro .
docker run --rm -p 8080:80 -e NASA_API_KEY="YOUR_NEW_NASA_KEY" project-astro
```

Open `http://localhost:8080`.

## NASA API key

- `config/nasa.php` reads only the `NASA_API_KEY` environment variable. No key is stored in source code.
- If the variable is missing or NASA cannot be reached, `/api/weather.php` serves the bundled October 2020 InSight sample with an explicit historical-data warning.
- Never paste the key into PHP files, JavaScript, GitHub, screenshots, or chat.
- If an earlier key was committed or shared, revoke it and generate a new key.

## Vercel storage limitations

Vercel container instances are stateless. Rover routes and planning settings are stored in the visitor's browser using `localStorage`; they are private to that browser/device and do not sync across devices or users. The old `api/routes.php` file-backed endpoint is not suitable for durable shared storage. For shared accounts/routes, connect a managed database before enabling multi-user features.

The weather endpoint may attempt to cache a successful NASA response in `storage/`, but that write is best-effort only. The checked-in `storage/mars-weather-cache.json` is a small October 2020 historical sample used only as a fallback; it must not be presented as current or the latest archived reading.

## Project modules

- Mission dashboard and Mars explorer
- Landing-zone analyzer and comparison
- Hazard detection
- Mars weather archive
- Water/resources and survival planning
- Rover route planning
- Mission reports and settings

## Data and safety notes

- The weather module uses NASA InSight archived observations, not live Mars weather. The InSight mission ended on December 15, 2022.
- Hazard records and some mission-planning values are illustrative educational data, not verified real-time NASA hazard observations.
- Landing-zone and route-planning scores are educational screening aids, not certified mission-safety assessments.
- Browser-stored routes, settings, and planning notes are not a multi-user database or authentication system.
