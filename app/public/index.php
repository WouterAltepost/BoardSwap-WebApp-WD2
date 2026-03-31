<?php

/** Start user session */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Set environment variables and enable error reporting */
require_once(__DIR__ . "/lib/env.php"); // sets global env variables (database configuration)
require_once(__DIR__ . "/lib/error_reporting.php"); // enables error reporting locally

/** Require routing library */
require_once(__DIR__ . "/lib/Route.php");

/** Require routes */
require_once(__DIR__ . "/routes/index.php");

// Start the router, enabling handling requests
Route::run();
