<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| PROJECT ASTRO - GLOBAL HELPER FUNCTIONS
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../config/app.php';

/*
|--------------------------------------------------------------------------
| HTML ESCAPING
|--------------------------------------------------------------------------
*/

function e(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}
