<?php
include "001_server.php";

// Retrieve the high scores with ranks
$query = "SELECT player_name, score, created_at, (SELECT COUNT(*)+1 FROM highscore AS h2 WHERE h2.score > h1.score) AS rank FROM highscore AS h1 ORDER BY score DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Scoreboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<a href="003_main_index.php"><button style="width: 100%;">home</button></a>
    <h1>Scoreboard</h1>

    <table>
        <tr>
            <th>Rank</th>
            <th>Player Name</th>
            <th>Score</th>
            <th>Created At</th>
        </tr>
        <?php foreach ($scores as $score): ?>
            <tr>
                <td><?php echo $score['rank']; ?></td>
                <td><?php echo $score['player_name']; ?></td>
                <td><?php echo $score['score']; ?></td>
                <td><?php echo $score['created_at']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <a href="005_unlimited_questions.php" ><button style="width: 100%;">Unlimited Questions</button></a>
</body>
</html>
