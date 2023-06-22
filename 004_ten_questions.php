<?php
// Include the server connection file
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

    // Store the result of the current question in the array
    $hiddenFieldValue['q' . $currentQuestionNum] = $isCorrect;

    // Increment the question number for the next question
    $currentQuestionNum++;

    // Update the hidden field value with the updated array
    $hiddenFieldValue['questionNum'] = $currentQuestionNum;

    // Check if the quiz is completed
    if ($currentQuestionNum > 10) {
        // Redirect to the result page
        $resultUrl = "006_result.php?hiddenfield=" . urlencode(serialize($hiddenFieldValue));
        header("Location: $resultUrl");
        exit();
    }
} else {
    // Initialize the hidden field value if it's not submitted
    $hiddenFieldValue = [
        'q1' => null,
        'q2' => null,
        'q3' => null,
        'q4' => null,
        'q5' => null,
        'q6' => null,
        'q7' => null,
        'q8' => null,
        'q9' => null,
        'q10' => null,
        'questionNum' => 1
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
    $wrongContinentQuery = "SELECT `Continent` FROM `countries` WHERE `Continent` != :continent AND `Country/Territory` != :country ORDER BY RAND() LIMIT 2";
    $wrongContinentStmt = $pdo->prepare($wrongContinentQuery);
    $wrongContinentStmt->bindParam(':continent', $continent);
    $wrongContinentStmt->bindParam(':country', $country);
    $wrongContinentStmt->execute();
    $wrongContinents = $wrongContinentStmt->fetchAll(PDO::FETCH_COLUMN);
    $options = array_merge($options, $wrongContinents);
} else {
    // Create an array with the correct capital and two random wrong capitals
    $options = [$capital];
    $wrongCapitalsQuery = "SELECT `Capital` FROM `countries` WHERE `Capital` != :capital AND `Country/Territory` != :country ORDER BY RAND() LIMIT 2";
    $wrongCapitalsStmt = $pdo->prepare($wrongCapitalsQuery);
    $wrongCapitalsStmt->bindParam(':capital', $capital);
    $wrongCapitalsStmt->bindParam(':country', $country);
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
    <title>Ten Questions</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .result {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin: 5px;
            border-radius: 50%;
        }

        .correct {
            background-color: green;
        }

        .wrong {
            background-color: red;
        }

        .black {
            background-color: black;
        }
    </style>
    <script>
        // Client-side form validation
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
    <h1>Ten Questions</h1>

    <form action="004_ten_questions.php" method="post" onsubmit="return validateForm();">
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
            <input type="radio" id="<?php echo $option; ?>" name="answer" value="<?php echo $option; ?>">
            <label for="<?php echo $option; ?>"><?php echo $option; ?></label><br>
        <?php } ?>

        <button type="submit" style="width: 100%;">Submit</button>
    </form>

    <div class="main">
        <?php for ($i = 1; $i <= 10; $i++) { ?>
            <div style=" justify-content: center; align-items: center; "  class="result <?php echo $hiddenFieldValue['q' . $i] === true ? 'correct' : ($hiddenFieldValue['q' . $i] === false ? 'wrong' : 'black'); ?>"></div>
        <?php } ?>
    </div>
    
</body>
</html>
