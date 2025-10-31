<?php
// backend/utils/auth.php
// Small authentication helpers used by API endpoints.

// Note: many API files include response helpers before this file. These helpers
// rely on `error_response()` and `success_response()` being available in scope.

if (session_status() === PHP_SESSION_NONE) {
    // do not start session automatically in some CLI contexts, start lazily
}

/**
 * Determine current session user id and role.
 * Returns array: ['id' => int|null, 'role' => string|null]
 */
function get_session_user(): array
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    $role = $_SESSION['user_role'] ?? null;
    $userId = null;
    if ($role) {
        $roleKey = preg_replace('/[^a-z0-9_]/', '', strtolower($role)) . '_id';
        if (!empty($_SESSION[$roleKey])) {
            $userId = (int)$_SESSION[$roleKey];
        }
    }

    if (!$userId && !empty($_SESSION['user_id'])) {
        $userId = (int)$_SESSION['user_id'];
    }

    return ['id' => $userId, 'role' => $role];
}

/**
 * Require that the current session belongs to a user with one of the allowed roles.
 * If allowed_roles includes 'any' then just require authentication.
 * Returns the numeric user id when successful.
 * On failure this will call error_response() and exit.
 */
function require_role($allowed_roles = [])
{
    if (is_string($allowed_roles)) $allowed_roles = [$allowed_roles];
    if (!is_array($allowed_roles)) $allowed_roles = [];

    $session = get_session_user();
    $userId = $session['id'];
    $role = $session['role'];

    if (!$userId) {
        if (function_exists('error_response')) {
            error_response('Not authenticated', null, 401);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }
    }

    // If allowed_roles contains 'any' or is empty, only authentication is required
    if (empty($allowed_roles) || in_array('any', $allowed_roles)) {
        return $userId;
    }

    // Role may be null for legacy sessions; deny in that case unless allowed explicitly
    if (!$role || !in_array($role, $allowed_roles)) {
        if (function_exists('error_response')) {
            error_response('Forbidden', null, 403);
        } else {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            exit;
        }
    }

    return $userId;
}
