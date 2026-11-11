<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$hazards = [
    [
        'id' => 'HZ-001',
        'name' => 'Rocky Terrain',
        'latitude' => 18.42,
        'longitude' => 77.63,
        'radius_km' => 3,
        'severity' => 'moderate',
        'description' => 'Illustrative rocky terrain hazard.'
    ],
    [
        'id' => 'HZ-002',
        'name' => 'Steep Slope',
        'latitude' => 18.45,
        'longitude' => 77.68,
        'radius_km' => 2,
        'severity' => 'high',
        'description' => 'Illustrative steep slope hazard.'
    ],
    [
        'id' => 'HZ-003',
        'name' => 'Sand Accumulation',
        'latitude' => 18.30,
        'longitude' => 77.52,
        'radius_km' => 4,
        'severity' => 'low',
        'description' => 'Illustrative loose sand region.'
    ]
];

echo json_encode([
    'success' => true,
    'dataset_type' => 'illustrative',
    'message' => 'Demonstration hazard locations only. Not verified NASA observations.',
    'hazards' => $hazards
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
