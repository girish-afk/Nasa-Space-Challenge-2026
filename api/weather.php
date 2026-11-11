<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function respond(int $status, array $data): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

/** Fetch NASA JSON over HTTPS using cURL, with a stream fallback. */
function fetchNasaWeather(string $url): array
{
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 18,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_USERAGENT => 'Project-Astro/1.0 (Mars weather dashboard)',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $body = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = $body === false ? curl_error($curl) : '';
        curl_close($curl);

        if ($body !== false && $status >= 200 && $status < 300) {
            return ['body' => $body, 'error' => ''];
        }
        return [
            'body' => false,
            'error' => $error !== '' ? $error : 'NASA returned HTTP ' . $status . '.',
        ];
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 18,
            'ignore_errors' => true,
            'header' => "Accept: application/json\r\nUser-Agent: Project-Astro/1.0\r\n",
        ],
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);

    $body = @file_get_contents($url, false, $context);
    if ($body === false) {
        $last = error_get_last();
        return ['body' => false, 'error' => $last['message'] ?? 'Could not connect to NASA.'];
    }

    $status = 0;
    foreach ($http_response_header ?? [] as $header) {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches)) {
            $status = (int) $matches[1];
        }
    }
    if ($status < 200 || $status >= 300) {
        return ['body' => false, 'error' => 'NASA returned HTTP ' . $status . '.'];
    }
    return ['body' => $body, 'error' => ''];
}

function normalizeObservations(array $data): array
{
    $observations = [];
    foreach (($data['sol_keys'] ?? []) as $sol) {
        $record = $data[(string) $sol] ?? null;
        if (!is_array($record)) {
            continue;
        }

        $temperature = $record['AT']['av'] ?? null;
        $pressure = $record['PRE']['av'] ?? null;
        $wind = $record['HWS']['av'] ?? null;

        $observations[] = [
            'sol' => (string) $sol,
            'date' => is_string($record['First_UTC'] ?? null) ? $record['First_UTC'] : null,
            'temperature' => is_numeric($temperature) ? (float) $temperature : null,
            'pressure' => is_numeric($pressure) ? (float) $pressure : null,
            'wind_speed' => is_numeric($wind) ? (float) $wind : null,
            'source' => 'NASA InSight',
        ];
    }
    return $observations;
}

$projectRoot = dirname(__DIR__);
$cacheFile = $projectRoot . '/storage/mars-weather-cache.json';
$cachedPayload = null;

if (is_file($cacheFile) && is_readable($cacheFile)) {
    $candidate = json_decode((string) file_get_contents($cacheFile), true);
    if (is_array($candidate) && !empty($candidate['observations']) && is_array($candidate['observations'])) {
        $cachedPayload = $candidate;
    }
}

$nasaConfig = require $projectRoot . '/config/nasa.php';
$apiKey = trim((string) ($nasaConfig['api_key'] ?? ''));

if ($apiKey === '') {
    if ($cachedPayload !== null) {
        $cachedPayload['success'] = true;
        $cachedPayload['historical'] = true;
        $cachedPayload['cached'] = true;
        $cachedPayload['message'] = 'NASA_API_KEY is not configured. Showing the bundled historical InSight sample (October 2020), not live Mars weather.';
        respond(200, $cachedPayload);
    }

    respond(503, [
        'success' => false,
        'historical' => true,
        'source' => 'NASA InSight Mars Weather Service',
        'message' => 'NASA_API_KEY is missing. Add it in Vercel Environment Variables and redeploy.',
        'observations' => [],
    ]);
}

$url = 'https://api.nasa.gov/insight_weather/?api_key=' . rawurlencode($apiKey)
    . '&feedtype=json&ver=1.0';

$result = fetchNasaWeather($url);
if ($result['body'] !== false) {
    $data = json_decode($result['body'], true);
    if (is_array($data) && !empty($data['sol_keys']) && is_array($data['sol_keys'])) {
        $observations = normalizeObservations($data);
        if ($observations) {
            $payload = [
                'success' => true,
                'historical' => true,
                'cached' => false,
                'source' => 'NASA InSight Mars Weather Service',
                'mission_status' => 'ENDED',
                'message' => 'NASA InSight archived observations. The mission ended on December 15, 2022; this is not live Mars weather.',
                'observations' => $observations,
            ];

            // Cache writes are best-effort; Vercel container storage is ephemeral.
            if (is_dir($projectRoot . '/storage') && is_writable($projectRoot . '/storage')) {
                @file_put_contents($cacheFile, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
            }
            respond(200, $payload);
        }
        $result['error'] = 'NASA responded, but no usable observations were found.';
    } else {
        $result['error'] = 'NASA returned an unexpected response. Check the API endpoint response.';
    }
}

if ($cachedPayload !== null) {
    $cachedPayload['success'] = true;
    $cachedPayload['historical'] = true;
    $cachedPayload['cached'] = true;
    $cachedPayload['message'] = 'NASA could not be reached (' . $result['error'] . '). Showing the bundled historical InSight sample (October 2020), not live Mars weather.';
    respond(200, $cachedPayload);
}

respond(503, [
    'success' => false,
    'historical' => true,
    'source' => 'NASA InSight Mars Weather Service',
    'message' => 'NASA historical observations could not be retrieved. ' . $result['error'],
    'observations' => [],
]);
