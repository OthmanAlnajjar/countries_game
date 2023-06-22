<?php

try {              
    $pdo = new PDO("mysql:host=localhost","root","",[                // Connect to MySQL without specifying a database
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION    
    ]);

    // Create the "game" database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS game");

    // Switch to the "game" database
    $pdo->exec("USE game");
}
catch(PDOException $php_errormsg) {                                 // erzeugt einen Fehler
    echo "Probleme mit der Datenbank<br>";
    echo $php_errormsg;                                             // Ausgabe des Fehlers, nur bei Entwicklung
    die();                                                          // bei Fehler Script beenden.
}

?>