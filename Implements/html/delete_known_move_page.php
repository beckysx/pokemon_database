<!DOCTYPE html>
<html>
<head>
    <title>Delete a Known Move</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php

/* Show all PHP errors. */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* Set connection info. */

$dbhost = 'localhost';
$dbuser = 'webuser';
$dbpass = 'pass';
$dbname = 'move_tutor';

/* Make connection, and return an error if it fails. */

if (!$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname)){
    echo "Error: Failed to make a MySQL connection: " . "<br>";
    echo "Errno: $conn->connect_errno; i.e. $conn->connect_error \n";
    exit;
}

?>

<div class="sidebar">
  <a class="active" href="./menu_page.php">Menu</a>
  <a class="mainpage" href="./known_moves_page.php">Main Known Move</a>
  <a href="./insert_known_move_page.php">Add a Known Move</a>
</div>

<div class="main">

<div class="header">
<h2>Forget a <i><u>Known_Move</u></i>!</h2>
<p>Select a the name of the move you want to forget....</p>
</div>

<div class="contents">
<?php
function result_to_table($result, $qryres) {
        $n_rows = $result->num_rows; // num_rows
        $n_cols = $result->field_count; // num_col
    
        //wrap table in a form and call self
        echo '<form action="delete_known_move_page.php" method=POST>';
        // Begin header ---------------------------------------------
        echo "<table>\n<thead>\n<tr>";
        
        $fields = $result->fetch_fields();
        for ($i=0; $i<$n_cols + 1; $i++){
            if ($i == 0) {
                echo "<th>Select?</th>";
            } else {
                echo "<th>" . $fields[$i - 1]->name . "</th>";
            }   
        }
        echo "</tr>\n</thead>\n";
        
        // Should displayu
        for ($i=0; $i<$n_rows; $i++){
            echo "<tr>";
            for($j=0; $j<$n_cols + 1; $j++){
                if ($j == 0) {
                    //checkbox
                    echo '<td><input type="checkbox" name="checkbox' . $qryres[$i][$j] . '" value=' . $qryres[$i][$j] . '/></td>';
                } else {
                    echo "<td>" . $qryres[$i][$j - 1] . "</td>";
                }
            }
            echo "</tr>\n";
        }
        echo "</tbody>\n</table>\n";
        
        //add a submit button to the form and close out the form
        echo '<input type="submit" value="Delete">';
        echo '</form>';
    }


    $conn->query("USE move_tutor;");
    $sql="SELECT * FROM pokemons
    NATURAL JOIN 
    (SELECT poke_id, move_name FROM known_moves) AS a
    ORDER BY poke_id ASC;";
    
if(!$result = $conn->query($sql)){
    echo "Query failed!";
    exit;
}
$result = $conn->query($sql);
$qryres = $result->fetch_all();

result_to_table($result, $qryres); 

    for($i = 0; $i < $result->num_rows; $i++) {

        $id = $qryres[$i][0];
        $name = $qryres[$i][3];
        //UPDATE statement found in same directory as self
        $file = file_get_contents('./../knownmoves_delete.sql', false);
        $stmt = $conn->prepare($file);
        $stmt->bind_param('is', $id, $name); 

        if (isset($_POST["checkbox$id"]) ){
            if (!$stmt->execute()) {
                echo $conn->error;
            } else {
                //redirect so refresh works properly
                header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
                exit();}    
        }
    }


$conn->close();



?>
</div>
</div>

</body>
</html>