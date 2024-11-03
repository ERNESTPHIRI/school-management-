<?php
function getDBConnection() {
    $dbPath = __DIR__ . '/schooll.db'; // Path to the database file
    
    try {
        $db = new SQLite3($dbPath);
        return $db;
    } catch (Exception $e) {
        error_log("Database connection failed: " . $e->getMessage());
        return null;  // Return null if connection fails, handle in your script
    }
}
?>
