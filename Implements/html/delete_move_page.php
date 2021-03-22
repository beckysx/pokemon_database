<!DOCTYPE html>
<html>
<head>
    <title>Delete a Move</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php

// Show all PHP errors.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbhost = 'localhost';
$dbuser = 'webuser';
$dbpass = 'pass';
$dbname = 'move_tutor';

/* Make connection, and return error if it fails. */
if (!$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname)){
    echo "Error: Failed to make a MySQL connection: " . "<br>";
    echo "Errno: $conn->connect_errno; i.e. $conn->connect_error \n";
    exit;
}

?>

<div class="sidebar">
  <a class="active" href="./menu_page.php">Menu</a>
  <a class="mainpage" href="./moves_page.php">Main Move</a>
  <a href="./update_move_page.php">Update Move</a>
  <a href="./delete_move_page.php">Delete Move</a>
  <a href="./move_trend_page.php">Move Trend</a>
</div>

<div class="main">

<div class="header">
    <h2>Select a move by name to delete it.</h2>
</div>

<div class="contents">
<form action="delete_move_page.php" method="post">

    <label for="move_name">Select move by name:</label><br>
    <input list="names" name="move_name" id="move_name">
    <datalist id="names">
    <?php
        $sql = "SELECT move_name FROM moves";
        if (!$result = $conn->query($sql)){ // get move names.
            echo "Failed to get move names.";
            exit;
        }
        $names_arr = $result->fetch_all(); // convert to array.

        for ($i = 0; $i < $result->num_rows; $i++){ // iterate
            $movename = $names_arr[$i][0];
            echo "<option value=$movename>"; // add option for every move.
        }
    ?>
    </datalist>

    <input type="submit" value="Submit">

</form>
</div>

</div>

<?php

if (isset($_POST["move_name"])){
    // prepared statement, obviously.
    $del_stmt = $conn->prepare("DELETE FROM moves WHERE move_name = ?;");
    $del_stmt->bind_param('s',$_POST["move_name"]);
    // now, for the deletion.
    if (!$del_stmt->execute()){ 
        echo $conn->error;
        echo "\n Deletion query failed!";
    }
    else { // redirect, so refreshing doesn't break everything.
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();
    }
}

$conn->close();

?>

</body>
</html>