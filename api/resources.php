<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=3600');

$resources = [
    [
        'id' => 'water-ice', 'name' => 'Subsurface water ice', 'category' => 'Water',
        'icon' => 'bi-droplet-half', 'status' => 'Orbital evidence', 'status_type' => 'info',
        'description' => 'Potentially useful water ice may occur beneath the surface, especially in selected mid- and high-latitude regions. Accessibility and purity require local verification.',
        'uses' => ['Crew water after processing', 'Oxygen and hydrogen production after processing', 'Agriculture and scientific operations'],
        'evidence' => 'SWIM combines multiple orbital indicators; SHARAD radar, neutron data, thermal observations and geomorphology have different sensitivities and limitations.',
        'source' => 'NASA / Planetary Science Institute SWIM', 'url' => 'https://swim.psi.edu/'
    ],
    [
        'id' => 'co2', 'name' => 'Atmospheric carbon dioxide', 'category' => 'Atmosphere',
        'icon' => 'bi-cloud-haze2', 'status' => 'Atmospheric constituent', 'status_type' => 'success',
        'description' => 'Mars has a carbon-dioxide-dominated atmosphere. CO₂ is a potential feedstock for oxygen production and, with additional inputs and processing, chemical manufacturing.',
        'uses' => ['Oxygen production concepts', 'Chemical feedstock research', 'In-situ resource utilization studies'],
        'evidence' => 'Atmospheric composition is characterized by Mars missions. Collection, compression and processing require dedicated hardware and energy.',
        'source' => 'NASA Mars Exploration Program', 'url' => 'https://science.nasa.gov/mars/'
    ],
    [
        'id' => 'regolith', 'name' => 'Martian regolith', 'category' => 'Surface material',
        'icon' => 'bi-layers', 'status' => 'Surface material', 'status_type' => 'warning',
        'description' => 'Loose surface material can be studied as a construction, shielding or feedstock resource. Composition varies by location and must be measured before use.',
        'uses' => ['Radiation shielding concepts', 'Landing pad and construction research', 'Mineralogy and soil science'],
        'evidence' => 'Rover and lander instruments have analyzed local soils and rocks. A measurement at one site should not be generalized to all of Mars.',
        'source' => 'NASA Mars Science', 'url' => 'https://science.nasa.gov/mars/'
    ],
    [
        'id' => 'solar', 'name' => 'Solar energy', 'category' => 'Energy',
        'icon' => 'bi-sun', 'status' => 'Location dependent', 'status_type' => 'info',
        'description' => 'Sunlight can provide electrical power through photovoltaic systems. Available energy depends on season, latitude, dust, terrain and array design.',
        'uses' => ['Rover and habitat electricity', 'Communications and instruments', 'Resource extraction and processing'],
        'evidence' => 'Solar availability must be modeled for the selected site and season; dust accumulation and atmospheric dust events can reduce output.',
        'source' => 'NASA Mars Exploration Program', 'url' => 'https://science.nasa.gov/mars/'
    ]
];

echo json_encode([
    'success' => true,
    'catalog_type' => 'curated_reference_catalog',
    'updated' => '2026-11-10',
    'notice' => 'This catalog describes resource classes and research context. It does not report measured local reserves or live extraction availability.',
    'resources' => $resources
], JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
