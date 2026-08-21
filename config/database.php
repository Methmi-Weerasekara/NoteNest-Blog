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

if ($isLocalhost) {
    // Localhost (XAMPP) Database Settings
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "notenest";
    define('BASE_URL', '/NoteNest');
} else {
    // InfinityFree Database Settings (Replace with your actual vPanel MySQL details)
    $host = "sql102.infinityfree.com";     // e.g., sql300.infinityfree.com
    $username = "if0_42711415";            // e.g., if0_38123456
    $password = "aT4HVotkvVAnQy";     // Your InfinityFree Account Password
    $database = "if0_42711415_notenest_db";   // e.g., if0_38123456_notenest
    define('BASE_URL', '');
}

$conn = @mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Database connection failed (" . ($isLocalhost ? "Localhost" : "InfinityFree") . "): " . mysqli_connect_error());
}

?>