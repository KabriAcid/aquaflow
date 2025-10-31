<?php

/**
 * Serve the states -> cities JSON file
 * GET /backend/api/location/states_cities.php
 */

// lightweight responder — reuse response helper if available
require_once __DIR__ . '/../../../backend/utils/response.php';

// path to the JSON data file
$file = __DIR__ . '/../../data/states_cities.json';
if (!file_exists($file)) {
    error_response('States data not found', null, 500);
}

$json = @file_get_contents($file);
if ($json === false) {
    error_response('Failed to read states data', null, 500);
}

// Return raw JSON with correct headers
set_json_headers();
http_response_code(200);
echo $json;
exit;
