document.addEventListener("DOMContentLoaded", () => {
    const mapElement = document.getElementById("marsMap");
    if (!mapElement) return;

    const byId = (id) => document.getElementById(id);
    const feedback = byId("explorerFeedback");
    const statusText = byId("mapStatusText");
    const latOutput = byId("selectedLat");
    const lngOutput = byId("selectedLng");
    const zoomOutput = byId("mapZoom");
    const savedContainer = byId("savedLocations");
    const STORAGE_KEY = "projectAstro.marsExplorer.savedLocations.v1";
    const defaultCenter = [0, 0];
    const defaultZoom = 2;

    if (typeof L === "undefined") {
        if (statusText) statusText.textContent = "LEAFLET LIBRARY UNAVAILABLE";
        showFeedback("The map library could not be loaded. Check your internet connection and refresh.", true);
        return;
    }

    const map = L.map("marsMap", {
        center: defaultCenter,
        zoom: defaultZoom,
        minZoom: 1,
        maxZoom: 18,
        zoomControl: true,
        attributionControl: true,
        preferCanvas: true
    });

    const marsTerrain = L.tileLayer(
        "https://tiles.arcgis.com/tiles/Ezk9fcjSUkeadg6u/arcgis/rest/services/Mars_Contour_Imagery/MapServer/tile/{z}/{y}/{x}",
        {
            minZoom: 1,
            maxZoom: 18,
            opacity: 1,
            attribution: 'Mars Contour Imagery | <a href="https://www.arcgis.com/" target="_blank" rel="noopener noreferrer">ArcGIS</a>'
        }
    );
    marsTerrain.addTo(map);
    L.control.scale({ metric: true, imperial: false, position: "bottomleft" }).addTo(map);

    let selectedMarker = null;
    let selectedCoordinates = { lat: 0, lng: 0 };
    let hasSelectedPosition = false;
    let savedLocations = loadSavedLocations();

    function showFeedback(message, isError = false) {
        if (!feedback) return;
        feedback.textContent = message;
        feedback.classList.toggle("is-error", isError);
        feedback.classList.toggle("is-visible", Boolean(message));
    }

    function loadSavedLocations() {
        try {
            const parsed = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
            return Array.isArray(parsed) ? parsed.filter(item => item && Number.isFinite(item.lat) && Number.isFinite(item.lng) && typeof item.id === "string") : [];
        } catch (error) {
            return [];
        }
    }

    function updateCoordinates(lat, lng) {
        selectedCoordinates = { lat, lng };
        latOutput.textContent = `${lat.toFixed(6)}°`;
        lngOutput.textContent = `${lng.toFixed(6)}°`;
    }

    function selectPosition(latlng, openPopup = true) {
        hasSelectedPosition = true;
        if (byId("useForAssessment")) byId("useForAssessment").disabled = false;
        updateCoordinates(latlng.lat, latlng.lng);
        if (selectedMarker) selectedMarker.setLatLng(latlng);
        else selectedMarker = L.marker(latlng).addTo(map);

        const popup = document.createElement("div");
        popup.className = "astro-map-popup-content";
        const heading = document.createElement("strong");
        heading.textContent = "Selected Map Position";
        const line1 = document.createElement("div");
        line1.textContent = `Latitude: ${latlng.lat.toFixed(6)}°`;
        const line2 = document.createElement("div");
        line2.textContent = `Longitude: ${latlng.lng.toFixed(6)}°`;
        popup.append(heading, line1, line2);
        selectedMarker.bindPopup(popup, { className: "astro-map-popup" });
        if (openPopup) selectedMarker.openPopup();
    }

    map.on("click", event => {
        selectPosition(event.latlng);
        showFeedback("Map position selected.");
    });

    function updateZoom() {
        zoomOutput.textContent = `ZOOM: ${map.getZoom()}`;
    }
    map.on("zoomend", updateZoom);
    map.on("load", () => { if (statusText) statusText.textContent = "MAP SERVICE CONNECTED"; });
    marsTerrain.on("tileload", () => { if (statusText) statusText.textContent = "MAP TILES LOADING"; });
    marsTerrain.on("tileerror", () => {
        if (statusText) statusText.textContent = "SOME TILES UNAVAILABLE";
        showFeedback("The imagery service did not return one or more map tiles. Try another zoom level or check the service later.", true);
    });
    marsTerrain.on("load", () => {
        if (statusText) statusText.textContent = "MAP LAYER LOADED";
        if (!feedback?.classList.contains("is-error")) showFeedback("Mars imagery layer loaded.");
    });
    updateZoom();
    updateCoordinates(0, 0);

    function searchCoordinates() {
        const input = byId("locationSearch");
        const raw = input.value.trim();
        const parts = raw.split(",").map(value => value.trim());
        if (parts.length !== 2 || parts.some(value => value === "")) {
            showFeedback("Use latitude, longitude format. Example: 18.4, 77.5", true);
            input.focus();
            return;
        }
        const values = parts.map(Number);
        if (!values.every(Number.isFinite) || values[0] < -90 || values[0] > 90 || values[1] < -180 || values[1] > 180) {
            showFeedback("Enter numeric map coordinates: latitude −90 to 90 and longitude −180 to 180.", true);
            input.focus();
            return;
        }
        const position = L.latLng(values[0], values[1]);
        map.setView(position, 7);
        selectPosition(position);
        showFeedback("Map moved to the requested map coordinates. These are not validated Mars planetographic coordinates.");
    }

    byId("coordinateSearchForm")?.addEventListener("submit", event => {
        event.preventDefault();
        searchCoordinates();
    });

    byId("resetMap")?.addEventListener("click", () => {
        map.setView(defaultCenter, defaultZoom);
        if (selectedMarker) map.removeLayer(selectedMarker);
        selectedMarker = null;
        hasSelectedPosition = false;
        if (byId("useForAssessment")) byId("useForAssessment").disabled = true;
        updateCoordinates(0, 0);
        showFeedback("Map view reset.");
    });

    byId("copyCoordinates")?.addEventListener("click", async event => {
        const text = `${selectedCoordinates.lat.toFixed(6)}, ${selectedCoordinates.lng.toFixed(6)}`;
        try {
            if (!navigator.clipboard?.writeText) throw new Error("Clipboard API unavailable");
            await navigator.clipboard.writeText(text);
            event.currentTarget.innerHTML = '<i class="bi bi-check2"></i> Copied';
            showFeedback("Coordinates copied to clipboard.");
            window.setTimeout(() => { event.currentTarget.innerHTML = '<i class="bi bi-copy"></i> Copy'; }, 1500);
        } catch (error) {
            showFeedback("Clipboard access was blocked. Select and copy the coordinates manually.", true);
        }
    });

    byId("useForAssessment")?.addEventListener("click", () => {
        if (!hasSelectedPosition) {
            showFeedback("Select a position on the Mars map before sending it to the Landing Zone Analyzer.", true);
            return;
        }
        const payload = {
            latitude: selectedCoordinates.lat,
            longitude: selectedCoordinates.lng,
            source: "PROJECT ASTRO Martian Explorer map selection",
            coordinateStatus: "map-service coordinates; planetary CRS not independently validated",
            createdAt: new Date().toISOString()
        };

        try {
            localStorage.setItem("projectAstro.landingZone.pendingLocation.v1", JSON.stringify(payload));
            window.location.href = "landing-zone.php";
        } catch (error) {
            showFeedback("Could not pass this position to the Landing Zone Analyzer. Browser storage may be unavailable.", true);
        }
    });

    byId("layerOpacity")?.addEventListener("input", event => {
        const opacity = Number(event.target.value) / 100;
        marsTerrain.setOpacity(opacity);
        byId("opacityValue").textContent = `${event.target.value}%`;
    });

    byId("fullscreenMap")?.addEventListener("click", async () => {
        const panel = byId("mapPanel");
        try {
            if (!document.fullscreenElement) await panel.requestFullscreen();
            else await document.exitFullscreen();
        } catch (error) {
            showFeedback("Fullscreen mode is unavailable in this browser.", true);
        }
    });
    document.addEventListener("fullscreenchange", () => window.setTimeout(() => map.invalidateSize(), 150));

    function persistSavedLocations() {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(savedLocations));
            return true;
        } catch (error) {
            showFeedback("Could not save locations in this browser. Check local storage settings.", true);
            return false;
        }
    }

    function renderSavedLocations() {
        savedContainer.replaceChildren();
        if (!savedLocations.length) {
            const empty = document.createElement("p");
            empty.className = "empty-saved";
            empty.textContent = "No saved locations yet. Select a position and save it.";
            savedContainer.appendChild(empty);
            return;
        }
        savedLocations.forEach((location, index) => {
            const item = document.createElement("div");
            item.className = "saved-location-item";
            const details = document.createElement("button");
            details.type = "button";
            details.className = "saved-location-open";
            details.textContent = `${location.name} · ${location.lat.toFixed(3)}, ${location.lng.toFixed(3)}`;
            details.addEventListener("click", () => {
                const point = L.latLng(location.lat, location.lng);
                map.setView(point, 7);
                selectPosition(point);
                showFeedback(`Opened saved location: ${location.name}`);
            });
            const remove = document.createElement("button");
            remove.type = "button";
            remove.className = "saved-location-remove";
            remove.setAttribute("aria-label", `Remove ${location.name}`);
            remove.innerHTML = '<i class="bi bi-trash3"></i>';
            remove.addEventListener("click", () => {
                savedLocations.splice(index, 1);
                if (persistSavedLocations()) renderSavedLocations();
            });
            item.append(details, remove);
            savedContainer.appendChild(item);
        });
    }

    byId("saveLocation")?.addEventListener("click", () => {
        if (!hasSelectedPosition) {
            showFeedback("Select a position on the Mars map before saving it.", true);
            return;
        }
        if (savedLocations.length >= 20) {
            showFeedback("You can save up to 20 locations in this browser. Remove one before adding another.", true);
            return;
        }
        const name = `Site ${savedLocations.length + 1}`;
        savedLocations.push({
            id: `${Date.now()}-${Math.random().toString(36).slice(2, 8)}`,
            name,
            lat: selectedCoordinates.lat,
            lng: selectedCoordinates.lng
        });
        if (persistSavedLocations()) {
            renderSavedLocations();
            showFeedback(`${name} saved in this browser. These bookmarks are not uploaded to a server.`);
        } else savedLocations.pop();
    });

    byId("clearSavedLocations")?.addEventListener("click", () => {
        if (!savedLocations.length) {
            showFeedback("There are no saved locations to clear.");
            return;
        }
        if (!window.confirm("Delete all saved map locations from this browser?")) return;
        savedLocations = [];
        if (persistSavedLocations()) {
            renderSavedLocations();
            showFeedback("All saved locations were cleared from this browser.");
        }
    });

    renderSavedLocations();
    window.setTimeout(() => map.invalidateSize(), 250);
});
