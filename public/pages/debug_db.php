<?php
// Database test
require_once '../../backend/config/conn.php';

echo "<h1>Database Test</h1>";

try {
    $pdo = getDBConnection();
    echo "<p>✅ Database connectie succesvol!</p>";
    
    // Test query om alle events te tonen
    $stmt = $pdo->prepare("SELECT * FROM events");
    $stmt->execute();
    $events = $stmt->fetchAll();
    
    echo "<h2>Alle Events in Database:</h2>";
    echo "<pre>";
    print_r($events);
    echo "</pre>";
    
    // Test users
    $stmt = $pdo->prepare("SELECT user_id, first_name, last_name, email FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    echo "<h2>Alle Users in Database:</h2>";
    echo "<pre>";
    print_r($users);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p>❌ Database fout: " . $e->getMessage() . "</p>";
}
?>
