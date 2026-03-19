<?php
session_start();

function loginUser($user) {
    $_SESSION['user'] = $user;
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../index.php");
        exit;
    }
}

function redirectByRole($role) {
    if ($role == 'Administrator') return "dashboards/admin.php";
    if ($role == 'Library Manager') return "dashboards/manager.php";
    if ($role == 'Library Staff') return "dashboards/staff.php";
   
    return "../index.php";
}