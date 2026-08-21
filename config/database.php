<?php

// Suppress uncaught PHP 8.1+ exception crash on connection error
mysqli_report(MYSQLI_REPORT_OFF);

// Detect if running on Localhost or InfinityFree
$httpHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isLocalhost = (
    strpos($httpHost, 'localhost') !== false ||
    strpos($httpHost, '127.0.0.1') !== false ||
    strpos($httpHost, '::1') !== false
);

// Load environment variables from .env if it exists
$envFile = __DIR__ . '/../.env';
$env = [];
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
}

if ($isLocalhost) {
    // Localhost (XAMPP) Database Settings
    $host = $env['DB_HOST_LOCAL'] ?? "localhost";
    $username = $env['DB_USER_LOCAL'] ?? "root";
    $password = $env['DB_PASS_LOCAL'] ?? "";
    $database = $env['DB_NAME_LOCAL'] ?? "notenest";
    define('BASE_URL', $env['BASE_URL'] ?? '/NoteNest');
} else {
    // InfinityFree Database Settings
    $host = $env['DB_HOST_PROD'] ?? "sql102.infinityfree.com";
    $username = $env['DB_USER_PROD'] ?? "if0_42711415";
    $password = $env['DB_PASS_PROD'] ?? "aT4HVotkvVAnQy";
    $database = $env['DB_NAME_PROD'] ?? "if0_42711415_notenest_db";
    define('BASE_URL', '');
}

$conn = @mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Database connection failed (" . ($isLocalhost ? "Localhost" : "InfinityFree") . "): " . mysqli_connect_error());
}

?>