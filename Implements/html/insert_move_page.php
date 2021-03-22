<!DOCTYPE html>
<html>
<head>
    <title>Add a New Move</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php

// Show all PHP errors.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* These four lines are meant for local 
   testing and should be changed when 
   added to the group repository. */

$dbhost = 'localhost';
$dbuser = 'webuser';
$dbpass = 'pass';
$dbname = 'move_tutor'; /* Change to move_tutor */

/* Make connection, and return error if it fails */
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
<h2>Insert A <i><u>New Move</u></i> !</h2>
<p>Insert a move here, you can do other operations through sidebar....</p>
</div>
<div class="contents">
<form action="insert_move_page.php" method="post">

    <label for="move_name">Move name:</label><br>
    <input type="text" id="move_name" name="move_name" 
    placeholder="Move name...You can update later... Please use underscore to connect words!!!"><br>

    <!-- <label for="move_type">Move type:</label><br>
    <input type="text" id="move_type" name="move_type"> -->

    <label for="move_type">Move type:</label><br>
    <input list="types" name="move_type" id="move_type">
    <datalist id="types">
    <?php
        $sql = "SELECT poke_type FROM types";
        if (!$result = $conn->query($sql)){ // get types
            echo "Failed to get types.";
            exit;
        }
        $types_arr = $result->fetch_all();

        for ($i = 0; $i < $result->num_rows; $i++){
            $typename = $types_arr[$i][0];
            echo "<option value=$typename>";
        }
    ?>
    </datalist>

    <p>Move time:</p>

    <input type="radio" id="past" name="move_time" value="past">
    <label for="past">Past</label><br>

    <input type="radio" id="present" name="move_time" value="present">
    <label for="present">Present</label><br>

    <input type="radio" id="future" name="move_time" value="future">
    <label for="future">Future</label><br>

    <input type="radio" id="N/A" name="move_time" value="N/A">
    <label for="N/A">N/A</label><br>

    <p>Is it a Hidden Move?</p>

    <!-- Since the value is an int, not a string, there may be a
         problem with the value here. -->

    <input type="radio" id="1" name="is_hm" value="yes">
    <label for="1">Yes</label><br>

    <input type="radio" id="0" name="is_hm" value="no">
    <label for="0">No</label><br><br>

    <input type="submit" value="Submit">
    
</form>
</div>
</div>

<?php

/* Check if every entry has been filled out by the user. */
if (isset($_POST["move_name"]) && isset($_POST["move_type"]) && 
    isset($_POST["move_time"]) && isset($_POST["is_hm"])){
        // prepared statement, of course.
        $add_stmt = $conn->prepare("INSERT INTO moves (move_name, move_type, move_time, is_hm)
                                         VALUES (?,?,?,?);"); // replace with file name??
        $add_stmt->bind_param('ssss', $_POST["move_name"], $_POST["move_type"], $_POST["move_time"], $_POST["is_hm"]);

        if (!$add_stmt->execute()) { // insert user data, return possible errors
            echo $conn->error;
            echo "\n Insert query failed!";
        }else {
            //redirect so refresh works properly
            header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
            exit();}
          }


    $conn->close();

?>
</div>
</body>
</html>