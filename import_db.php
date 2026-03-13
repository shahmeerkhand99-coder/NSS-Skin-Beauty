<?php
// Quick database import script
require_once 'config/config.php';

try {
    // Read SQL file
    $sql = file_get_contents('ns_beauty.sql');
    
    // Remove comments and split statements
    $sql = preg_replace('/--.*$/m', '', $sql);
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "<h2>Importing NS Beauty Database...</h2>";
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                db()->exec($statement);
                echo "<div style='color:green'>✓ Executed: " . substr($statement, 0, 50) . "...</div>";
            } catch (Exception $e) {
                echo "<div style='color:orange'>⚠ Skipped: " . substr($statement, 0, 50) . "...</div>";
            }
        }
    }
    
    echo "<h3 style='color:green'>✓ Database import completed!</h3>";
    echo "<p><a href='index.php'>Go to Homepage</a></p>";
    
} catch (Exception $e) {
    echo "<div style='color:red'>Error: " . $e->getMessage() . "</div>";
}
?>
