<?php
// ============================================================
//  LibraryQuiet – auth.php (ROOT level)
//  Include at top of every protected page
// ============================================================

require_once __DIR__ . '/includes/config.php';

function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

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
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

function requireRole(string $role): void {
    requireLogin();
    if ($_SESSION['user_role'] !== $role) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

function requireAnyRole(array $roles): void {
    requireLogin();
    if (!in_array($_SESSION['user_role'], $roles)) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}