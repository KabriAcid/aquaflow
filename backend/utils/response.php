<?php

/**
 * Small response helpers for JSON APIs
 */

function set_json_headers(): void
{
    header('Content-Type: application/json; charset=utf-8');
    // CORS - adjust in production as needed
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}

function success_response(string $message = 'OK', $data = null, int $code = 200): void
{
    set_json_headers();
    http_response_code($code);
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data]);
    exit;
}

function error_response(string $message = 'Error', $errors = null, int $code = 400): void
{
    set_json_headers();
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message, 'errors' => $errors]);
    exit;
}
