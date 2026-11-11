<?php
declare(strict_types=1);

/**
 * NASA API configuration.
 * Set NASA_API_KEY in Vercel > Project > Settings > Environment Variables.
 * Never paste a real API key into this file or commit it to GitHub.
 */
$apiKey = getenv('NASA_API_KEY');

return [
    'api_key' => ($apiKey !== false && trim($apiKey) !== '')
        ? trim($apiKey)
        : '',
];
