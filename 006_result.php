<?php
// Check if the hidden field value is set
if (isset($_GET['hiddenfield'])) {
    // Retrieve the hidden field value
    $hiddenFieldValue = unserialize($_GET['hiddenfield']);

    // Count the number of correct answers
    $correctAnswers = array_filter($hiddenFieldValue, function ($value) {
        return $value === true;
    });

    // Calculate the score and percentage
    $score = count($correctAnswers);
    $percentage = ($score / 10) * 100;
} else {
    // Set default values if the hidden field value is not set
    $score = 0;
    $percentage = 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Result</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .progress-bar {
            width: 100%;
            height: 30px;
            background-color: lightgray;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .progress {
            height: 100%;
            background-color: green;
            border-radius: 5px;
        }

        .progress-label {
            margin-top: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<a href="003_main_index.php"><button style="width: 100%;">home</button></a>
    <h1>Quiz Result</h1>
    <p>Your Score: <?php echo $score; ?>/10</p>
    <div class="progress-bar">
        <div class="progress" style="width: <?php echo $percentage; ?>%;"></div>
    </div>
    <p class="progress-label">Percentage: <?php echo $percentage; ?>%</p>
    <?php if ($score > 0) { ?>
        <button onclick="location.href='004_ten_questions.php';">Try again</button>

    <?php } else { ?>
        <p>No quiz results found.</p>
        <button onclick="location.href='004_ten_questions.php';">Try again</button>

    <?php } ?>
</body>
</html>
