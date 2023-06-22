
<?php
include "001_server.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $playerName = $_POST['player_name'];
    $score = $_POST['score'];

    // Insert the player's name and score into the highscore table
    $insertQuery = "INSERT INTO highscore (player_name, score) VALUES (:playerName, :score)";
    $insertStmt = $pdo->prepare($insertQuery);
    $insertStmt->bindParam(':playerName', $playerName);
    $insertStmt->bindParam(':score', $score);
    $insertStmt->execute();

    // Calculate the rank based on the score
    $rankQuery = "SELECT COUNT(*) AS rank FROM highscore WHERE score > :score";
    $rankStmt = $pdo->prepare($rankQuery);
    $rankStmt->bindParam(':score', $score);
    $rankStmt->execute();
    $rankResult = $rankStmt->fetch(PDO::FETCH_ASSOC);
    $rank = $rankResult['rank'] + 1;

    // Retrieve the score from the query parameter
    $score = $_GET['score'];

    // Redirect to the scoreboard page
    header("Location: 008_scoreboard.php");
    exit();
}

// Retrieve the score from the query parameter
$score = $_GET['score'];
?>


<!DOCTYPE html>
<html>
<head>
    <title>Save Score</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>Save Score</h1>

    <p>Your score: <?php echo $score; ?></p>

    <form action="007_save_score.php" method="post">
        <label for="player_name">Enter your name:</label>
        <input type="text" name="player_name" id="player_name" required>

        <input type="hidden" name="score" value="<?php echo $score; ?>">
        <button type="submit">Submit</button>

    </form>
</body>
</html>


