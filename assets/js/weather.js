
document.addEventListener('DOMContentLoaded', () => {

    const API_URL = 'api/weather.php';

    let temperatureChart = null;
    let pressureChart = null;

    const $ = id => document.getElementById(id);

    const elements = {
        temperature: $('temperature'),
        pressure: $('pressure'),
        wind: $('windSpeed'),
        dust: $('dustCondition'),
        status: $('weatherStatus'),
        updated: $('weatherUpdated'),
        history: $('weatherHistory'),
        insight: $('missionInsight')
    };

    function setStatus(message, state = 'normal') {
        if (!elements.status) return;
        const dot = document.createElement('span');
        dot.className = 'status-dot';
        elements.status.replaceChildren(dot, document.createTextNode(message));
        elements.status.className = `weather-status ${state}`;
    }

    function createChart(canvasId, label, values, labels, color, unit) {

        const canvas = $(canvasId);

        if (!canvas || typeof Chart === 'undefined') return null;

        const styles = getComputedStyle(document.documentElement);
        const textColor = styles.getPropertyValue('--text-secondary').trim() || '#A3AFBE';
        const mutedColor = styles.getPropertyValue('--text-muted').trim() || '#788697';
        const gridColor = styles.getPropertyValue('--border').trim() || 'rgba(255,255,255,.08)';

        return new Chart(canvas, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label,
                    data: values,
                    borderColor: color,
                    backgroundColor: color + '20',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    spanGaps: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        labels: {
                            color: textColor,
                            font: { family: 'Inter', size: 12 }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: mutedColor, font: { family: 'Inter', size: 11 }, maxRotation: 0, autoSkip: true },
                        grid: { color: gridColor }
                    },
                    y: {
                        title: {
                            display: true,
                            text: unit,
                            color: textColor,
                            font: { family: 'Inter', size: 12 }
                        },
                        ticks: { color: mutedColor, font: { family: 'Inter', size: 11 }, maxRotation: 0, autoSkip: true },
                        grid: { color: gridColor }
                    }
                }
            }
        });
    }

    function renderCharts(observations) {

        const records = observations.slice().reverse();

        const labels = records.map(item => `Sol ${item.sol}`);

        const temperatures = records.map(item => item.temperature);
        const pressures = records.map(item => item.pressure);

        if (temperatureChart) temperatureChart.destroy();
        if (pressureChart) pressureChart.destroy();

        temperatureChart = createChart(
            'temperatureChart',
            'Temperature',
            temperatures,
            labels,
            '#E66A45',
            '°C'
        );

        pressureChart = createChart(
            'pressureChart',
            'Pressure',
            pressures,
            labels,
            '#50C8E8',
            'Pa'
        );
    }

    function renderHistory(observations) {

        if (!observations.length) {
            elements.history.innerHTML =
                '<tr><td colspan="5">No observations available.</td></tr>';
            return;
        }

        elements.history.replaceChildren();

        observations.slice().reverse().forEach(item => {

            const row = document.createElement('tr');

            const values = [
                `Sol ${item.sol}`,
                item.temperature !== null
                    ? `${item.temperature.toFixed(2)} °C` : 'N/A',
                item.pressure !== null
                    ? `${item.pressure.toFixed(2)} Pa` : 'N/A',
                item.wind_speed !== null
                    ? `${item.wind_speed.toFixed(2)} m/s` : 'N/A',
                item.source || 'NASA'
            ];

            values.forEach(value => {
                const cell = document.createElement('td');
                cell.textContent = value;
                row.appendChild(cell);
            });

            elements.history.appendChild(row);
        });
    }

    function updateDashboard(data) {

        const observations = data.observations || [];

        if (!observations.length) {
            throw new Error('No historical observations were returned.');
        }

        const latest = observations[observations.length - 1];

        elements.temperature.textContent =
            latest.temperature !== null
                ? latest.temperature.toFixed(2) : 'N/A';

        elements.pressure.textContent =
            latest.pressure !== null
                ? latest.pressure.toFixed(2) : 'N/A';

        elements.wind.textContent =
            latest.wind_speed !== null
                ? latest.wind_speed.toFixed(2) : 'N/A';

        // No dust-opacity measurement is provided by this API.
        elements.dust.textContent = 'N/A';

        $('temperatureState').textContent =
            latest.temperature !== null ? 'Available' : 'Unavailable';

        $('pressureState').textContent =
            latest.pressure !== null ? 'Available' : 'Unavailable';

        $('windState').textContent =
            latest.wind_speed !== null ? 'Available' : 'Unavailable';

        $('dustState').textContent = 'Not provided';

        $('temperatureRange').textContent =
            data.cached ? 'Bundled historical sample (Oct 2020)' : 'NASA archived observation average';

        $('dustDescription').textContent =
            'No verified opacity measurement';

        elements.updated.textContent =
            latest.date
                ? `Observation: ${new Date(latest.date).toUTCString()}`
                : `Sol ${latest.sol}`;

        elements.insight.textContent =
            (data.message ? data.message + ' ' : '') +
            'These are historical environmental observations, not current Mars conditions. ' +
            'They are informational and are not a certified mission-safety assessment.';

        renderCharts(observations);
        renderHistory(observations);

        setStatus(data.cached ? 'Showing bundled historical sample' : 'NASA archived data loaded', data.cached ? 'warning' : 'success');
    }

    async function loadWeather() {

        const button = $('refreshWeather');

        button.disabled = true;
        setStatus('Retrieving historical observations...');

        try {

            const response = await fetch(API_URL, {
                headers: { Accept: 'application/json' },
                cache: 'no-store'
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(
                    data.message || 'Weather service unavailable.'
                );
            }

            updateDashboard(data);

        } catch (error) {

            console.error('Weather error:', error);

            setStatus('Weather data unavailable', 'error');

            elements.history.innerHTML =
                '<tr><td colspan="5">Unable to retrieve verified observations.</td></tr>';

            elements.insight.textContent =
                (error.message || 'The NASA InSight archive could not be reached.') +
                ' Check the server internet connection and PHP cURL/OpenSSL settings, then refresh.';

        } finally {
            button.disabled = false;
        }
    }

    $('refreshWeather').addEventListener('click', loadWeather);

    loadWeather();

});
