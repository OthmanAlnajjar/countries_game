<?php
include "001_server.php";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted answer and the current question number from the hidden field
    $submittedAnswer = $_POST['answer'];
    $hiddenFieldValue = unserialize($_POST['hiddenfield']);
    $currentQuestionNum = intval($_POST['questionNum']);

    // Retrieve the correct answer for the current question
    $correctAnswer = $_POST['correct_answer'];

    // Check if the submitted answer is correct
    $isCorrect = $submittedAnswer === $correctAnswer;

    // Increment the score by 100 for each correct answer
    if ($isCorrect) {
        $hiddenFieldValue['score'] += 100;
    }

    // Decrement the number of lives if the answer is incorrect
    if (!$isCorrect) {
        $hiddenFieldValue['lives'] -= 1;
        $hiddenFieldValue['consecutiveCorrect'] = 0; // Reset consecutive correct count
    } else {
        // Increment the consecutive correct count
        $hiddenFieldValue['consecutiveCorrect'] += 1;

        // Check if the consecutive correct count reaches 5
        if ($hiddenFieldValue['consecutiveCorrect'] === 5) {
            $hiddenFieldValue['consecutiveCorrect'] = 0; // Reset consecutive correct count
            $hiddenFieldValue['lives'] += 1; // Increment the number of lives
        }
    }

    // Increment the question number for the next question
    $currentQuestionNum++;

    // Update the hidden field value with the updated number of lives and question number
    $hiddenFieldValue['questionNum'] = $currentQuestionNum;

    // Check if the quiz is completed or lives are exhausted
    if ($hiddenFieldValue['lives'] <= 0) {
        // Redirect to the save score page
        $resultUrl = "007_save_score.php?score=" . urlencode($hiddenFieldValue['score']);
        header("Location: $resultUrl");
        exit();
    }
} else {
    // Initialize the hidden field value if it's not submitted
    $hiddenFieldValue = [
        'lives' => 5,
        'questionNum' => 1,
        'consecutiveCorrect' => 0,
        'score' => 0 // Initialize the score to 0
    ];
}

// Retrieve the current question number
$currentQuestionNum = $hiddenFieldValue['questionNum'];

// Generate a random question type (1: Continent, 2: Capital)
$questionType = rand(1, 2);

// Generate a random question
$query = "SELECT `Country/Territory`, `Capital`, `Continent` FROM `countries` ORDER BY RAND() LIMIT 1";
$result = $pdo->query($query);

// Fetch the data from the result set
$row = $result->fetch(PDO::FETCH_ASSOC);
$country = $row['Country/Territory'];
$capital = $row['Capital'];
$continent = $row['Continent'];

// Generate options based on the question type
if ($questionType === 1) {
    // Create an array with the correct Continent and two random wrong Continents
    $options = [$continent];
    $wrongContinentQuery = "SELECT `Continent` FROM `countries` WHERE `Continent` != :continent ORDER BY RAND() LIMIT 2";
    $wrongContinentStmt = $pdo->prepare($wrongContinentQuery);
    $wrongContinentStmt->bindParam(':continent', $continent);
    $wrongContinentStmt->execute();
    $wrongContinents = $wrongContinentStmt->fetchAll(PDO::FETCH_COLUMN);
    $options = array_merge($options, $wrongContinents);
} else {
    // Create an array with the correct capital and two random wrong capitals
    $options = [$capital];
    $wrongCapitalsQuery = "SELECT `Capital` FROM `countries` WHERE `Capital` != :capital ORDER BY RAND() LIMIT 2";
    $wrongCapitalsStmt = $pdo->prepare($wrongCapitalsQuery);
    $wrongCapitalsStmt->bindParam(':capital', $capital);
    $wrongCapitalsStmt->execute();
    $wrongCapitals = $wrongCapitalsStmt->fetchAll(PDO::FETCH_COLUMN);
    $options = array_merge($options, $wrongCapitals);
}

// Shuffle the options randomly
shuffle($options);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unlimited Questions</title>
    <link rel="stylesheet" href="style.css">
    <style>
    .progress-bar {
        width: 100%;
        height: 30px;
        background-color: #f2f2f2;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar-inner {
        height: 100%;
        width: <?php echo ($hiddenFieldValue['lives'] / 5) * 100; ?>%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
        position: relative;
    }

    .progress-bar-inner span {
        position: absolute;
        top: 50%;
        left: 4px;
        transform: translateY(-50%);
    }

    .consecutive-tracker {
        width: 100%;
        height: 20px;
        background-color: #ffffff; /* White color for empty tracker */
        border-radius: 4px;
        overflow: hidden;
        margin-top: 10px;
    }

    .consecutive-tracker-inner {
        height: 100%;
        width: <?php echo ($hiddenFieldValue['consecutiveCorrect'] / 5) * 100; ?>%;
        background-color: #ffd700; /* Golden color for filled tracker */
        transition: width 0.2s; /* Add transition for smooth width change */
    }

    .score-counter {
        text-align: center;
        margin-top: 20px;
        font-size: 18px;
        font-weight: bold;
    }

    body.correct-answer {
        background-color: lightgreen;
    }

    body.wrong-answer {
        background-color: lightcoral;
    }
    </style>
    <script>
        function validateForm() {
            var answerRadios = document.getElementsByName('answer');
            var checked = false;
            for (var i = 0; i < answerRadios.length; i++) {
                if (answerRadios[i].checked) {
                    checked = true;
                    break;
                }
            }
            if (!checked) {
                alert('Please select an answer.');
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<a href="003_main_index.php"><button style="width: 100%;">home</button></a>
    <h1>Unlimited Questions</h1>

    <form action="005_unlimited_questions.php" method="post" onsubmit="return validateForm();">
        <input type="hidden" name="hiddenfield" value="<?php echo htmlentities(serialize($hiddenFieldValue)); ?>">
        <input type="hidden" name="correct_answer" value="<?php echo $questionType === 1 ? $continent : $capital; ?>">
        <input type="hidden" name="questionNum" value="<?php echo $currentQuestionNum; ?>">
        <p>
            Question <?php echo $currentQuestionNum; ?>:
            <?php if ($questionType === 1) { ?>
                What continent is <?php echo $country; ?> located in?
            <?php } else { ?>
                What is the capital of <?php echo $country; ?>?
            <?php } ?>
        </p>

        <?php foreach ($options as $option) { ?>
            <input type="radio" name="answer" value="<?php echo $option; ?>" required> <?php echo $option; ?><br>
        <?php } ?>

        <button type="submit" style="width: 100%;">Submit</button>
    </form>

    <div class="progress-bar">
        <div class="progress-bar-inner" style="background-color: #4CAF50;">
            <span><?php echo $hiddenFieldValue['lives']; ?> Lives</span>
        </div>
    </div>

    <div class="consecutive-tracker">
        <div class="consecutive-tracker-inner"></div>
    </div>

    <div class="score-counter">
        Score: <?php echo $hiddenFieldValue['score']; ?>
    </div>

    <script>
        // Add class to body based on answer correctness
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') { ?>
            var body = document.querySelector('body');
            body.classList.add('<?php echo $isCorrect ? "correct-answer" : "wrong-answer"; ?>');

            // Remove class after 0.2 seconds
            setTimeout(function() {
                body.classList.remove('<?php echo $isCorrect ? "correct-answer" : "wrong-answer"; ?>');
            }, 200);
        <?php } ?>
    </script>
</body>
</html>
