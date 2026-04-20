<?php
// ============================================================
//  LibraryQuiet – includes/config.php
// ============================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'u442411629_dev_library');
define('DB_PASS', '6nV6$5BSLjjl');
define('DB_NAME', 'u442411629_librarysaba');
define('BASE',    '/NOISE_MONITOR');

if (session_status() === PHP_SESSION_NONE) {
    session_name('LQ_SESSION');
    session_start();
}

function getDB() {
    static $conn = null;
    if ($conn !== null && $conn->ping()) return $conn;
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'DB Error: ' . $conn->connect_error]);
        exit;
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function getBody() {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

// ── AUTH HELPERS ────────────────────────────────────────────
function isLoggedIn(): bool { return !empty($_SESSION['user_id']); }

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    return [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role'  => $_SESSION['user_role'],
    ];
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE . '/index.php');
        exit;
    }
}

function requireRole(string $role): void {
    requireLogin();
    if ($_SESSION['user_role'] !== $role) {
        header('Location: ' . BASE . '/index.php');
        exit;
    }
}

function requireAnyRole(array $roles): void {
    requireLogin();
    if (!in_array($_SESSION['user_role'], $roles)) {
        header('Location: ' . BASE . '/index.php');
        exit;
    }
}