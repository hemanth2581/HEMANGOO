<?php
/**
 * Database Setup Script
 * Run this script to set up the unified database schema
 */

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hemango";

echo "Setting up Hemango database...\n";

try {
    // Create connection without specifying database
    $conn = new mysqli($servername, $username, $password);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "Connected to MySQL server successfully.\n";
    
    // Read and execute the unified schema
    $schemaFile = __DIR__ . '/database/hemango_unified_schema.sql';
    
    if (!file_exists($schemaFile)) {
        die("Schema file not found: $schemaFile\n");
    }
    
    $schema = file_get_contents($schemaFile);
    
    // Split the schema into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $schema)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    echo "Executing " . count($statements) . " SQL statements...\n";
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;
        
        try {
            if ($conn->query($statement)) {
                $successCount++;
                echo "✓ Executed successfully\n";
            } else {
                $errorCount++;
                echo "✗ Error: " . $conn->error . "\n";
                echo "Statement: " . substr($statement, 0, 100) . "...\n";
            }
        } catch (Exception $e) {
            $errorCount++;
            echo "✗ Exception: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\nDatabase setup completed!\n";
    echo "Successful statements: $successCount\n";
    echo "Failed statements: $errorCount\n";
    
    if ($errorCount === 0) {
        echo "\n✅ Database setup successful! You can now use the application.\n";
        echo "\nSample data has been inserted:\n";
        echo "- 2 users (1 admin, 1 farmer)\n";
        echo "- 3 factories\n";
        echo "- 5 mango varieties\n";
        echo "- Time slots for the next 7 days\n";
        echo "- Sample market data\n";
        echo "\nDefault credentials:\n";
        echo "Admin: admin@hemango.com / password\n";
        echo "Farmer: farmer@hemango.com / password\n";
    } else {
        echo "\n❌ Some errors occurred during setup. Please check the output above.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
