<!DOCTYPE html>
<html>
<head>
    <title>Mini Game Site</title>
    <link rel="stylesheet" href="style.css">
    </head>
<body>
<a href="003_main_index.php"><button style="width: 100%;">home</button></a>
    <h1>Geo Quiz</h1>
    <div class="welcome-message" id="welcome-message">
        <?php
        // PHP code to display a dynamic welcome message
        $hour = date('H');
        $welcomeMessage = '';

        if ($hour < 12) {
            $welcomeMessage = 'Good morning!';
        } elseif ($hour < 18) {
            $welcomeMessage = 'Good afternoon!';
        } else {
            $welcomeMessage = 'Good evening!';
        }
        ?>

        <div>
            <h2>How to Play</h2>
            <ul>
                <li>Step 1: Click on the "Start Game" button to begin.</li>
                <li>Step 2: Answer each question by selecting the correct option.</li>
                <li>Step 3: You will be notified immediately whether your answer is correct or incorrect.</li>
                <li>Step 4: there is no time limit so take your time.</li>
                <li>Step 5: If you answer incorrectly, you will lose a life.</li>
                <li>Step 6: The game continues until you lose all your lives.</li>
                <li>Step 7: Once the game is over, enter your name and submit your score to see how you rank among other players.</li>
            </ul>
        </div>

            <p>Are you ready to test your knowledge? Click the button below to start the game and embark on an exciting journey of discovery and fun.</p>
        <div>

    </div>
    <button class="welcome-message" id="start-button" onclick="showQuizButtons()" style="width: 100%;">Start</button></div>
    <div id="quiz-buttons" style="display: none; hei">

  <a href="005_unlimited_questions.php" ><button style="width: 100%;">Unlimited Questions</button></a>
  <a href="004_ten_questions.php"><button style="width: 100%;">Ten Questions</button></a>
  <a href="008_scoreboard.php"><button style="width: 100%;">Scoreboard</button></a>
</div>

<script>
  function showQuizButtons() {
    document.getElementById("welcome-message").style.display = "none";
    document.getElementById("quiz-buttons").style.display = "block";
  }
</script>
</body>
</html>

