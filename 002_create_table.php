<?php
// this php code will import the headlines names from the world_population.csv
//  will create table if not exists in database "game"
     
include "001_server.php";

// Create the database if it doesn't exist
$databaseName = "game";
$sql = "CREATE DATABASE IF NOT EXISTS $databaseName";

$pdo->exec($sql);  // Execute the CREATE DATABASE SQL statement

// Switch to the newly created database
$pdo->exec("USE $databaseName");

$csvFile = 'world_population.csv';  // CSV file

// Read the first row of the CSV file to get the column names
$handle = fopen($csvFile, 'r');
$columns = fgetcsv($handle);
fclose($handle);

$tableName = 'countries';  // Table name

// Generate the CREATE TABLE SQL statement
$sql = "CREATE TABLE IF NOT EXISTS $tableName (";
foreach ($columns as $column) {
    $sql .= "`$column` VARCHAR(255), ";
}
$sql = rtrim($sql, ', ');
$sql .= ");";

$pdo->exec($sql);  // Execute the CREATE TABLE SQL statement

// Insert the data from the CSV file into the table
$handle = fopen($csvFile, 'r');
fgetcsv($handle);  // Skip the first row (column names)

while (($data = fgetcsv($handle)) !== false) {
    $sql = "INSERT INTO $tableName (";
    foreach ($columns as $column) {
        $sql .= "`$column`, ";
    }
    $sql = rtrim($sql, ', ');
    $sql .= ") VALUES (";
    foreach ($data as $value) {
        $sql .= "'" . addslashes($value) . "', ";
    }
    $sql = rtrim($sql, ', ');
    $sql .= ");";

    $pdo->exec($sql);  // Execute the INSERT SQL statement
}

fclose($handle);

echo "Table $tableName created and data inserted successfully.";


// Create the highscore table
$tableName = 'highscore';  // Table name
$sql = "CREATE TABLE IF NOT EXISTS $tableName (
    `id` int(11) NOT NULL,
    `player_name` varchar(50) NOT NULL,
    `score` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `score_index` (`score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

$pdo->exec($sql);

// Insert data into the highscore table
$data = [
    [1, 'rick', 100, '2023-06-21 23:07:07'],
    [2, 'Mano', 200, '2023-06-21 23:14:37'],
    [3, 'maggie', 300, '2023-06-21 23:14:41'],
    [4, 'marga', 400, '2023-06-21 23:16:54'],
    [5, 'glen', 600, '2023-06-21 23:28:50'],
    [6, 'thor', 500, '2023-06-21 23:52:45'],
    [7, 'oleg', 800, '2023-06-22 00:29:14'],
    [8, 'sandra', 700, '2023-06-22 00:36:23'],
    [9, 'anna', 1000, '2023-06-22 00:48:59'],
];

foreach ($data as $row) {
    $id = $row[0];
    $playerName = $row[1];
    $score = $row[2];
    $createdAt = $row[3];

    $sql = "INSERT INTO $tableName (`id`, `player_name`, `score`, `created_at`) 
            VALUES ($id, '$playerName', $score, '$createdAt');";
    
    $pdo->exec($sql);
}

echo "<br> Table $tableName created and data inserted successfully.";
?>