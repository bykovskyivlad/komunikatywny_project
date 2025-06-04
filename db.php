<?php
try {
    $pdo = new PDO(
        "pgsql:host=127.0.0.1;port=5432;dbname=bankowa_app",
        "postgres",        
        "08052023v&S"      
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Błąd połączenia z bazą: " . $e->getMessage());
}
?>