<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$locations = [
    [
        'id' => 'jezero',
        'name' => 'Jezero Crater',
        'latitude' => 18.38,
        'longitude' => 77.58,
        'description' => 'Perseverance rover exploration region.'
    ],
    [
        'id' => 'gale',
        'name' => 'Gale Crater',
        'latitude' => -5.40,
        'longitude' => 137.80,
        'description' => 'Curiosity rover exploration region.'
    ],
    [
        'id' => 'olympus',
        'name' => 'Olympus Mons',
        'latitude' => 18.65,
        'longitude' => 226.20,
        'description' => 'Large shield volcano.'
    ],
    [
        'id' => 'valles',
        'name' => 'Valles Marineris',
        'latitude' => -13.90,
        'longitude' => 300.00,
        'description' => 'Extensive canyon system.'
    ],
    [
        'id' => 'arcadia',
        'name' => 'Arcadia Planitia',
        'latitude' => 46.00,
        'longitude' => 180.00,
        'description' => 'Region investigated for subsurface ice evidence.'
    ]
];

echo json_encode([
    'success' => true,
    'source' => 'Curated planetary location references',
    'locations' => $locations
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
