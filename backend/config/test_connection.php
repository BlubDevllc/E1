<?php
/**
 * Database Connection Test
 * 
 * Test script om te checken of database connectie werkt
 * Gebruik dit ALLEEN voor development/testing!
 */

require_once 'config.php';
require_once 'conn.php';

echo "<h2>Database Connectie Test</h2>";

try {
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✅ Database connectie succesvol!</p>";
    
    // Test query
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch();
    echo "<p>Verbonden met database: <strong>" . $result['db_name'] . "</strong></p>";
    
    // Check of users tabel bestaat
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->fetch()) {
        echo "<p style='color: green;'>✅ Users tabel gevonden!</p>";
        
        // Count users
        $stmt = $pdo->query("SELECT COUNT(*) as user_count FROM users");
        $result = $stmt->fetch();
        echo "<p>Aantal gebruikers: <strong>" . $result['user_count'] . "</strong></p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Users tabel niet gevonden. Maak deze aan in phpMyAdmin.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connectie mislukt!</p>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<h3>Mogelijke oplossingen:</h3>";
    echo "<ul>";
    echo "<li>Check of Laragon/Apache en MySQL draaien</li>";
    echo "<li>Controleer database naam in config.php</li>";
    echo "<li>Maak database 'eventplanner' aan in phpMyAdmin</li>";
    echo "</ul>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2 { color: #2c3e50; }
p { margin: 10px 0; }
ul { margin-left: 20px; }
</style>
