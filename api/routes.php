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

$file = dirname(__DIR__) . '/storage/routes.json';
$directory = dirname($file);

function readRoutes(string $file): array
{
    if (!is_file($file)) return [];
    $handle = fopen($file, 'rb');
    if ($handle === false) throw new RuntimeException('Could not open saved routes.');
    try {
        if (!flock($handle, LOCK_SH)) throw new RuntimeException('Could not lock saved routes.');
        $contents = stream_get_contents($handle);
        flock($handle, LOCK_UN);
    } finally {
        fclose($handle);
    }
    $routes = json_decode($contents === false ? '' : $contents, true);
    return is_array($routes) ? $routes : [];
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        respond(200, ['success' => true, 'routes' => readRoutes($file)]);
    } catch (Throwable $exception) {
        error_log('PROJECT ASTRO routes read error: ' . $exception->getMessage());
        respond(500, ['success' => false, 'message' => 'Could not read saved routes.']);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: GET, POST');
    respond(405, ['success' => false, 'message' => 'Method not allowed.']);
}

// Only accept saves coming from this site (blocks cross-site POSTs) and limit payload size.
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '' && strcasecmp((string) parse_url($origin, PHP_URL_HOST), (string) ($_SERVER['HTTP_HOST'] ?? '')) !== 0 &&
    strcasecmp(((string) parse_url($origin, PHP_URL_HOST)) . ':' . (string) parse_url($origin, PHP_URL_PORT), (string) ($_SERVER['HTTP_HOST'] ?? '')) !== 0) {
    respond(403, ['success' => false, 'message' => 'Cross-site requests are not allowed.']);
}
if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 65536) {
    respond(413, ['success' => false, 'message' => 'Request too large.']);
}

$raw = file_get_contents('php://input');
$input = json_decode($raw === false ? '' : $raw, true);
if (!is_array($input)) respond(400, ['success' => false, 'message' => 'Invalid JSON request.']);

$name = trim((string)($input['name'] ?? ''));
$waypoints = $input['waypoints'] ?? [];

if ($name === '' || strlen($name) > 240 || !is_array($waypoints) ||
    count($waypoints) < 2 || count($waypoints) > 100) {
    respond(422, ['success' => false, 'message' => 'Enter a mission name and provide 2–100 waypoints.']);
}

$clean = [];
foreach ($waypoints as $point) {
    if (!is_array($point)) respond(422, ['success' => false, 'message' => 'A waypoint is invalid.']);
    $lat = $point['lat'] ?? null;
    $lng = $point['lng'] ?? null;

    if (!is_numeric($lat) || !is_numeric($lng) ||
        !is_finite((float)$lat) || !is_finite((float)$lng) ||
        (float)$lat < -90 || (float)$lat > 90 ||
        (float)$lng < 0 || (float)$lng > 360) {
        respond(422, ['success' => false, 'message' => 'Invalid Mars coordinates. Latitude must be −90 to 90 and east longitude 0 to 360.']);
    }

    $pointName = trim((string)($point['name'] ?? 'Waypoint'));
    $clean[] = [
        'name' => substr($pointName !== '' ? $pointName : 'Waypoint', 0, 80),
        'lat' => (float)$lat,
        'lng' => (float)$lng
    ];
}

if (!is_dir($directory) || !is_writable($directory)) {
    respond(500, ['success' => false, 'message' => 'Storage directory is not writable by PHP.']);
}

try {
    // Lock the file for the complete read/append/write operation to avoid lost saves.
    $handle = fopen($file, 'c+');
    if ($handle === false) throw new RuntimeException('Could not open route storage.');
    try {
        if (!flock($handle, LOCK_EX)) throw new RuntimeException('Could not lock route storage.');
        $contents = stream_get_contents($handle);
        $routes = json_decode($contents === false || trim($contents) === '' ? '[]' : $contents, true);
        if (!is_array($routes)) $routes = [];

        $route = [
            'id' => bin2hex(random_bytes(8)),
            'name' => $name,
            'waypoints' => $clean,
            'created_at' => date(DATE_ATOM)
        ];
        if (count($routes) >= 500) throw new RuntimeException('Route storage limit reached.');
        $routes[] = $route;
        $json = json_encode($routes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($json === false) throw new RuntimeException('Could not encode route data.');

        rewind($handle);
        if (!ftruncate($handle, 0) || fwrite($handle, $json) === false || !fflush($handle)) {
            throw new RuntimeException('Could not write route data.');
        }
        flock($handle, LOCK_UN);
    } finally {
        fclose($handle);
    }
    respond(201, ['success' => true, 'message' => 'Mission route saved.', 'route' => $route]);
} catch (Throwable $exception) {
    error_log('PROJECT ASTRO routes save error: ' . $exception->getMessage());
    respond(500, ['success' => false, 'message' => 'Could not save route. Check storage permissions.']);
}
