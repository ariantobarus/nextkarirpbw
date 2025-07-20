<?php
/**
 * db.php
 *
 * Database connection and session initialization.
 * This file should be included in every PHP file that requires database access or session management.
 */

// --- Database Configuration ---
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'projec15_root'); // Replace with your database username
define('DB_PASSWORD', '@kaesquare123');     // Replace with your database password
define('DB_NAME', 'projec15_nextkarir_db');

// --- Establish Connection ---
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection for errors
if ($conn->connect_error) {
    // Stop execution and display an error message if connection fails.
    die("Koneksi Gagal: " . $conn->connect_error);
}

// --- Session Management ---
// Start a new session or resume the existing one if not already started.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
