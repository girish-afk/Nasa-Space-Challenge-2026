document.addEventListener("DOMContentLoaded", () => {
    const $ = id => document.getElementById(id);
    const mapElement = $("marsRouteMap");
    if (!mapElement) return;

    if (typeof L === "undefined") {
        $("routeMessage").textContent = "Map library did not load. Check your internet connection and reload.";
        $("routeMessage").classList.add("error");
        return;
    }

    const API = "api/";
    const SAVED_ROUTES_KEY = "projectAstroSavedRoutesV1";

    function readSavedRoutes() {
        try {
            const value = JSON.parse(localStorage.getItem(SAVED_ROUTES_KEY) || "[]");
            return Array.isArray(value) ? value : [];
        } catch (_) {
            return [];
        }
    }

    function writeSavedRoutes(routes) {
        localStorage.setItem(SAVED_ROUTES_KEY, JSON.stringify(routes.slice(-100)));
    }
    const MARS_RADIUS_KM = 3389.5;
    const points = [];
    let hazards = [];
    let locations = [];

    // Reuse the same Mars Contour Imagery service as Hazard Detection.
    // The tile service uses Leaflet's standard Web Mercator map coordinates.
    const map = L.map("marsRouteMap", {
        center: [0, 0],
        zoom: 2,
        minZoom: 1,
        maxZoom: 18,
        zoomControl: true,
        attributionControl: true,
        preferCanvas: true
    });

    const marsTiles = L.tileLayer(
        "https://tiles.arcgis.com/tiles/Ezk9fcjSUkeadg6u/arcgis/rest/services/Mars_Contour_Imagery/MapServer/tile/{z}/{y}/{x}",
        {
            minZoom: 1,
            maxZoom: 18,
            attribution: 'Mars Contour Imagery | <a href="https://www.arcgis.com/" target="_blank" rel="noopener noreferrer">ArcGIS</a>'
        }
    ).addTo(map);

    const routeLayer = L.layerGroup().addTo(map);
    const hazardLayer = L.layerGroup().addTo(map);
    let referenceLayer;

    const mapStatus = document.createElement("span");
    mapStatus.className = "route-map-status";
    mapStatus.textContent = "CONNECTING TO MARS IMAGERY";
    mapStatus.setAttribute("role", "status");
    const mapToolbar = document.querySelector(".route-map-toolbar");
    if (mapToolbar) mapToolbar.appendChild(mapStatus);

    let loadedMarsTiles = 0;
    let tileErrors = 0;
    let fallbackGrid = null;

    function showCoordinateFallback() {
        if (fallbackGrid) return;
        fallbackGrid = L.gridLayer({ tileSize: 256 });
        fallbackGrid.createTile = function () {
            const tile = document.createElement("canvas");
            tile.width = tile.height = 256;
            const ctx = tile.getContext("2d");
            ctx.fillStyle = "#111C28";
            ctx.fillRect(0, 0, 256, 256);
            ctx.strokeStyle = "rgba(80,200,232,.18)";
            ctx.lineWidth = 1;
            ctx.strokeRect(.5, .5, 255, 255);
            ctx.fillStyle = "#A3AFBE";
            ctx.font = "11px Inter, sans-serif";
            ctx.fillText("MARS COORDINATE GRID", 12, 22);
            return tile;
        };
        fallbackGrid.addTo(map);
    }

    marsTiles.on("tileload", () => { loadedMarsTiles++; });
    marsTiles.on("tileerror", () => { tileErrors++; });
    marsTiles.on("load", () => {
        if (loadedMarsTiles === 0) {
            mapStatus.textContent = "MARS IMAGERY UNAVAILABLE · COORDINATE GRID ACTIVE";
            mapStatus.classList.add("error");
            showCoordinateFallback();
        } else {
            mapStatus.textContent = tileErrors
                ? "MARS IMAGERY LOADED · SOME TILES UNAVAILABLE"
                : "MARS IMAGERY LOADED";
            mapStatus.classList.toggle("error", tileErrors > 0);
        }
    });

    function toMapLongitude(eastLongitude) {
        return ((Number(eastLongitude) + 180) % 360 + 360) % 360 - 180;
    }

    function toMarsLongitude(mapLongitude) {
        return ((Number(mapLongitude) % 360) + 360) % 360;
    }

    function mapPosition(point) {
        return [Number(point.lat), toMapLongitude(point.lng)];
    }

    function escapeHTML(value) {
        return String(value).replace(/[&<>"']/g, char => ({
            "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;"
        })[char]);
    }

    function notify(message, error = false) {
        const el = $("routeMessage");
        el.textContent = message;
        el.classList.toggle("error", error);
    }

    function normalizeDeltaLongitude(delta) {
        return ((delta + 540) % 360) - 180;
    }

    function distance(a, b) {
        const rad = value => value * Math.PI / 180;
        const dLat = rad(b.lat - a.lat);
        const dLng = rad(normalizeDeltaLongitude(b.lng - a.lng));
        const lat1 = rad(a.lat);
        const lat2 = rad(b.lat);
        const h = Math.sin(dLat / 2) ** 2 +
            Math.cos(lat1) * Math.cos(lat2) * Math.sin(dLng / 2) ** 2;
        return 2 * MARS_RADIUS_KM * Math.asin(Math.min(1, Math.sqrt(h)));
    }

    // Local tangent-plane distance from a hazard point to a route segment.
    // Suitable for proximity screening, not precision navigation.
    function distanceToSegmentKm(point, start, end) {
        const rad = Math.PI / 180;
        const meanLat = ((start.lat + end.lat + point.lat) / 3) * rad;
        const project = (origin, target) => ({
            x: normalizeDeltaLongitude(target.lng - origin.lng) * rad *
                Math.cos(meanLat) * MARS_RADIUS_KM,
            y: (target.lat - origin.lat) * rad * MARS_RADIUS_KM
        });
        const endXY = project(start, end);
        const pointXY = project(start, point);
        const length2 = endXY.x * endXY.x + endXY.y * endXY.y;
        if (!length2) return Math.hypot(pointXY.x, pointXY.y);
        const t = Math.max(0, Math.min(1,
            (pointXY.x * endXY.x + pointXY.y * endXY.y) / length2
        ));
        return Math.hypot(pointXY.x - t * endXY.x, pointXY.y - t * endXY.y);
    }

    function totalDistance() {
        let km = 0;
        for (let i = 1; i < points.length; i++) km += distance(points[i - 1], points[i]);
        return km;
    }

    function renderReferenceLocations() {
        if (referenceLayer) map.removeLayer(referenceLayer);
        referenceLayer = L.layerGroup().addTo(map);

        locations.forEach(location => {
            const marker = L.circleMarker([Number(location.latitude), toMapLongitude(location.longitude)], {
                radius: 5, color: "#50C8E8", weight: 2,
                fillColor: "#50C8E8", fillOpacity: 0.85
            });
            marker.bindPopup(
                `<strong>${escapeHTML(location.name)}</strong><br>${escapeHTML(location.description)}<br>` +
                `<small>${Number(location.latitude).toFixed(2)}°, ${Number(location.longitude).toFixed(2)}°E</small>`
            );
            marker.addTo(referenceLayer);
        });

        const select = $("knownLocation");
        select.innerHTML = '<option value="">Select a known Mars site</option>';
        locations.forEach(location => {
            const option = document.createElement("option");
            option.value = String(location.id);
            option.textContent = location.name;
            select.appendChild(option);
        });
    }

    function renderHazards() {
        hazardLayer.clearLayers();
        hazards.forEach(hazard => {
            const severity = String(hazard.severity || "moderate").toLowerCase();
            const color = severity === "high" || severity === "critical"
                ? "#F05252" : severity === "low" ? "#39C58A" : "#E9B44C";
            L.circleMarker([Number(hazard.latitude), toMapLongitude(hazard.longitude)], {
                radius: 8, color, weight: 2, fillColor: color, fillOpacity: 0.22
            }).bindPopup(
                `<strong>${escapeHTML(hazard.name)}</strong><br>` +
                `${escapeHTML(hazard.description)}<br>` +
                `<small>${escapeHTML(severity.toUpperCase())} · Illustrative data</small>`
            ).addTo(hazardLayer);
        });
    }

    function assessHazards() {
        const found = [];
        if (points.length < 2) {
            $("hazardCount").textContent = "0";
            $("hazardResults").textContent = "Add at least two waypoints to evaluate the route.";
            return;
        }
        if (!hazards.length) {
            $("hazardCount").textContent = "—";
            $("hazardResults").textContent = "Hazard dataset unavailable. No assessment was performed.";
            return;
        }

        hazards.forEach(hazard => {
            const hazardPoint = {
                lat: Number(hazard.latitude),
                lng: Number(hazard.longitude)
            };
            const radius = Math.max(0, Number(hazard.radius_km) || 0);
            let nearest = Infinity;
            for (let i = 1; i < points.length; i++) {
                nearest = Math.min(nearest,
                    distanceToSegmentKm(hazardPoint, points[i - 1], points[i]));
            }
            if (nearest <= radius) found.push({ ...hazard, nearest });
        });

        $("hazardCount").textContent = String(found.length);
        if (!found.length) {
            $("hazardResults").innerHTML =
                '<p class="text-secondary">No illustrative hazard zones intersect the route corridor. This is not a safety certification.</p>';
            return;
        }

        $("hazardResults").innerHTML = found.map(h => `
            <div class="hazard-result">
                <strong>${escapeHTML(h.name)}</strong>
                <span class="hazard-${escapeHTML(h.severity)}">${escapeHTML(String(h.severity).toUpperCase())}</span>
                <p>${escapeHTML(h.description)} · Approx. ${h.nearest.toFixed(1)} km from route centreline (zone radius ${Number(h.radius_km).toFixed(1)} km).</p>
            </div>
        `).join("");
    }

    function render() {
        routeLayer.clearLayers();

        points.forEach((point, index) => {
            const marker = L.marker(mapPosition(point), { draggable: true }).addTo(routeLayer);
            marker.bindPopup(
                `<strong>${index + 1}. ${escapeHTML(point.name)}</strong><br>` +
                `Latitude: ${point.lat.toFixed(4)}°<br>East longitude: ${point.lng.toFixed(4)}°`
            );
            marker.on("dragend", event => {
                const position = event.target.getLatLng();
                point.lat = position.lat;
                point.lng = toMarsLongitude(position.lng);
                render();
            });
        });

        if (points.length > 1) {
            L.polyline(points.map(mapPosition), {
                color: "#E66A45", weight: 4, opacity: 0.95, dashArray: "8 7"
            }).addTo(routeLayer);
        }

        const list = $("waypointList");
        list.innerHTML = "";
        if (!points.length) {
            list.innerHTML = `<div class="empty-route"><i class="bi bi-geo-alt"></i><p>No waypoints selected.</p><small>Click the Mars map or add a reference site.</small></div>`;
        }

        points.forEach((point, index) => {
            const item = document.createElement("div");
            item.className = "waypoint-item";
            item.innerHTML = `
                <div class="waypoint-number">${index + 1}</div>
                <div class="waypoint-details">
                    <strong>${escapeHTML(point.name)}</strong>
                    <small>${point.lat.toFixed(3)}°, ${point.lng.toFixed(3)}°E</small>
                </div>
                <button type="button" class="waypoint-remove" aria-label="Remove waypoint ${index + 1}">
                    <i class="bi bi-x-lg"></i>
                </button>`;
            item.querySelector("button").addEventListener("click", () => {
                points.splice(index, 1);
                render();
            });
            list.appendChild(item);
        });

        const km = totalDistance();
        const speed = Math.max(0.1, Math.min(20, Number($("roverSpeed").value) || 1));
        $("routeDistance").innerHTML = `${km.toFixed(2)} <small>km</small>`;
        $("routeTime").innerHTML = `${(km / speed).toFixed(1)} <small>hrs</small>`;
        $("waypointCount").textContent = String(points.length);
        $("waypointLabel").textContent = `${points.length} locations`;
        $("startLocation").value = points[0]?.name || "";
        $("destinationLocation").value = points.length > 1 ? points[points.length - 1].name : "";
        $("mapCoordinates").textContent = points.length
            ? `Lat: ${points[points.length - 1].lat.toFixed(4)}° | Lon: ${points[points.length - 1].lng.toFixed(4)}°E`
            : "Select a location on the map";
        assessHazards();
    }

    function addWaypoint(point) {
        if (points.length >= 100) {
            notify("Maximum 100 waypoints allowed.", true);
            return;
        }
        points.push({
            name: String(point.name || `Waypoint ${points.length + 1}`).slice(0, 80),
            lat: Number(point.lat),
            lng: Number(point.lng)
        });
        render();
    }

    map.on("click", event => {
        addWaypoint({
            name: `Waypoint ${points.length + 1}`,
            lat: event.latlng.lat,
            lng: toMarsLongitude(event.latlng.lng)
        });
    });

    $("addKnownLocation").addEventListener("click", () => {
        const location = locations.find(item => String(item.id) === $("knownLocation").value);
        if (!location) {
            notify("Choose a Mars reference site first.", true);
            return;
        }
        addWaypoint({
            name: location.name,
            lat: location.latitude,
            lng: location.longitude
        });
        notify(`${location.name} added to the route.`);
        map.panTo([Number(location.latitude), toMapLongitude(location.longitude)]);
    });

    $("clearRoute").addEventListener("click", () => {
        points.length = 0;
        render();
        notify("Route cleared.");
    });

    $("roverSpeed").addEventListener("input", render);

    $("saveRoute").addEventListener("click", async () => {
        if (points.length < 2) return notify("Add at least two waypoints before saving.", true);
        const name = $("routeName").value.trim();
        if (!name) return notify("Enter a mission name.", true);

        const button = $("saveRoute");
        button.disabled = true;
        try {
            const routes = readSavedRoutes();
            const route = {
                id: (window.crypto && crypto.randomUUID) ? crypto.randomUUID() : `${Date.now()}-${Math.random().toString(16).slice(2)}`,
                name: name.slice(0, 240),
                waypoints: points.map(point => ({ name: String(point.name || "Waypoint").slice(0, 80), lat: Number(point.lat), lng: Number(point.lng) })),
                created_at: new Date().toISOString()
            };
            routes.push(route);
            writeSavedRoutes(routes);
            notify("Mission route saved in this browser.");
            await loadSavedRoutes();
        } catch (error) {
            notify(error.message || "Could not save this route. Check browser storage settings.", true);
        } finally {
            button.disabled = false;
        }
    });

    $("exportRoute").addEventListener("click", () => {
        if (!points.length) return notify("Add at least one waypoint before exporting.", true);
        const rows = [["Waypoint", "Latitude", "East Longitude"]];
        points.forEach(point => rows.push([point.name, point.lat, point.lng]));
        const csv = rows.map(row => row.map(value =>
            `"${String(value).replace(/"/g, '""')}"`
        ).join(",")).join("\r\n");
        const url = URL.createObjectURL(new Blob([csv], { type: "text/csv;charset=utf-8" }));
        const link = document.createElement("a");
        link.href = url;
        link.download = "mars-rover-route.csv";
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(url);
        notify("Route CSV exported.");
    });

    async function loadSavedRoutes() {
        const container = $("savedRoutes");
        try {
            const routes = readSavedRoutes();
            if (!routes.length) {
                container.innerHTML = '<p class="text-secondary">No saved routes yet.</p>';
                return;
            }

            container.innerHTML = routes.map(route => `
                <div class="saved-route-item">
                    <div class="waypoint-details">
                        <strong>${escapeHTML(route.name)}</strong>
                        <small>${Array.isArray(route.waypoints) ? route.waypoints.length : 0} waypoints · ${escapeHTML(route.created_at || "")}</small>
                    </div>
                    <button type="button" class="btn-secondary-custom" data-route="${escapeHTML(route.id)}">
                        <i class="bi bi-folder2-open"></i> Load
                    </button>
                </div>
            `).join("");

            container.querySelectorAll("[data-route]").forEach(button => {
                button.addEventListener("click", () => {
                    const route = routes.find(item => String(item.id) === button.dataset.route);
                    if (!route || !Array.isArray(route.waypoints)) return;
                    points.splice(0, points.length, ...route.waypoints.map(point => ({
                        name: String(point.name || "Waypoint"),
                        lat: Number(point.lat),
                        lng: Number(point.lng)
                    })));
                    $("routeName").value = route.name;
                    render();
                    if (points.length) map.fitBounds(points.map(mapPosition), { padding: [25, 25], maxZoom: 6 });
                    notify("Saved route loaded.");
                });
            });
        } catch (error) {
            container.innerHTML = `<p class="route-message error">${escapeHTML(error.message || "Could not load saved routes.")}</p>`;
        }
    }

    async function initialize() {
        try {
            const [locationResponse, hazardResponse] = await Promise.all([
                fetch(API + "locations.php", { headers: { "Accept": "application/json" } }),
                fetch(API + "hazards.php", { headers: { "Accept": "application/json" } })
            ]);
            if (!locationResponse.ok || !hazardResponse.ok) throw new Error("Mars reference data request failed.");
            const locationData = await locationResponse.json();
            const hazardData = await hazardResponse.json();
            if (!locationData.success || !Array.isArray(locationData.locations)) throw new Error("Location data is invalid.");
            locations = locationData.locations;
            hazards = hazardData.success && Array.isArray(hazardData.hazards) ? hazardData.hazards : [];
            renderReferenceLocations();
            renderHazards();
            notify("Planner ready. Click the map or add a reference site.");
        } catch (error) {
            notify(`${error.message} You can still add waypoints by clicking the map.`, true);
        }
        render();
        loadSavedRoutes();
        // Leaflet needs a size refresh after the dashboard layout has settled.
        window.setTimeout(() => map.invalidateSize(), 100);
    }

    initialize();
});
