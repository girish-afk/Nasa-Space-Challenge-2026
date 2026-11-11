<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| PROJECT ASTRO - APPLICATION CONFIGURATION
|--------------------------------------------------------------------------
*/

define('APP_NAME', 'PROJECT ASTRO');
define('APP_TITLE', 'Interplanetary Survival Guide: Martian Map');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'production');

/*
|--------------------------------------------------------------------------
| BASE URL
|--------------------------------------------------------------------------
*/

define('BASE_URL', '');

/*
|--------------------------------------------------------------------------
| TIMEZONE
|--------------------------------------------------------------------------
*/

date_default_timezone_set('UTC');

/*
|--------------------------------------------------------------------------
| ERROR REPORTING
|--------------------------------------------------------------------------
*/

if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}
