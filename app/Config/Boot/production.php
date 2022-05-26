<?php

use Sentry\Integration\IgnoreErrorsIntegration;
use function Sentry\init;

/*
 |--------------------------------------------------------------------------
 | ERROR DISPLAY
 |--------------------------------------------------------------------------
 | Don't show ANY in production environments. Instead, let the system catch
 | it and display a generic error message.
 */
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);

/*
 |--------------------------------------------------------------------------
 | DEBUG MODE
 |--------------------------------------------------------------------------
 | Debug mode is an experimental flag that can allow changes throughout
 | the system. It's not widely used currently, and may not survive
 | release of the framework.
 */
defined('CI_DEBUG') || define('CI_DEBUG', false);

/*
 *---------------------------------------------------------------
 * SENTRY.IO
 *---------------------------------------------------------------
 * Initialize Sentry for exception reporting.
 */
if (getenv('sentry.dsn')) {
    init([
        'dsn'          => getenv('sentry.dsn'),
        'integrations' => [
            new IgnoreErrorsIntegration([
                'ignore_exceptions' => ['CodeIgniter\Exceptions\PageNotFoundException'],
            ]),
        ],
    ]);
}
