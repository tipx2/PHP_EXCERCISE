<?php

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('PHP Says: " . $output . "' );</script>";
}

$servername = "db";
$username = "root";
$password = "rootpassword";

try {
$conn = new PDO("mysql:host=$servername;dbname=bookstore", $username, $password);
// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
debug_to_console("Connected successfully");
} catch(PDOException $e) {
    debug_to_console("Connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") { 
    $insert = $conn->prepare("INSERT INTO Student (Name, Course, Subjects, Year) VALUES (?, ?, ?, ?)");

    $poss_grades = array('A', 'B', 'C', 'D', 'E', 'F');
    
    $grades = array($_POST["s1input"]=>$poss_grades[array_rand($poss_grades)],
                    $_POST["s2input"]=>$poss_grades[array_rand($poss_grades)],
                    $_POST["s3input"]=>$poss_grades[array_rand($poss_grades)],
                    $_POST["s4input"]=>$poss_grades[array_rand($poss_grades)]
    );

    try {
        $insert->execute([$_POST["nameinput"], $_POST["courseinput"],json_encode($grades), $_POST["yearinput"]]);
    } catch (PDOException $e) {
        debug_to_console($e->getMessage());
    }
    
} else if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // retrieve grades to display them
    $select = $conn->prepare("SELECT * FROM Student WHERE StudentId = ?");
    $select->execute([$_GET["querygradesinput"]]);

    $user = $select->fetch();

    if ($user) {

        echo '<b>Name</b>: ' . $user["Name"] . '<br>';
        echo '<b>Course</b>: ' . $user["Course"] . '<br>';
        
        $grades = json_decode($user["Subjects"], true);
        
        echo '<ul>';
        foreach ($grades as $subject => $grade) {
            echo "<li>Subject: $subject, Grade: $grade</li>";
        }
        echo '</ul>';
    } else {
        echo 'User ID ' . $_GET["querygradesinput"] . ' not present in database';
    }
}

?>