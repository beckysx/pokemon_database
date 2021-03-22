<!DOCTYPE html>
<html>
<head>
    <title>Insert a Known Move</title>
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
  <a href="./delete_known_move_page.php">Forget a Known Move</a>
</div>

<div class="main">

<div class="header">
<h2>Learn a <i><u>New_Move</u></i>!</h2>
<p>Select a Pokemon and choose (or type) the new move it just learnt!</p>
</div>

<div class="contents">
<?php
function validateMoveType($conn, $move_name, $pokmn_id) {

    // prepared statement to select move type.
    $get_move_type_sql="SELECT move_type FROM moves WHERE move_name ='".$move_name."'";
    $get_poke_type_sql="SELECT poke_type FROM poke_types WHERE poke_id ='".$pokmn_id."'";

    $move_type_result = $conn->query($get_move_type_sql)or die($conn->error);
    $poke_type_result = $conn->query($get_poke_type_sql)or die($conn->error);
    while ($movetype = $move_type_result->fetch_array()) {
        $move_type = $movetype['move_type'];}

    /* Now, compare move type and pokemon types for validity. */

    if($move_type != "normal"){
        $match=false;
        while ($poketype = $poke_type_result->fetch_array()) {
            if ($poketype['poke_type']==$move_type){
                $match=true;
            break;}
        }
        if($match==false){
            echo "\n ERROR: Move type $move_type does not match pokemon types. Fail the insertion.";
            exit;
        }
    }
    
}

function result_to_table($result, $qryres,$conn) {
        $n_rows = $result->num_rows; // num_rows
        $n_cols = $result->field_count; // num_col
    
        //wrap table in a form and call self
        echo '<form action="insert_known_move_page.php" method=POST>';
        
        // Begin header ---------------------------------------------
        echo "<table>\n<thead>\n<tr>";
        
        $fields = $result->fetch_fields();
        for ($i=0; $i<$n_cols + 1; $i++){
            if ($i == 0) {
                echo "<th>Select?</th>";
            } else {
                echo "<th>" . $fields[$i - 1]->name . "</th>";}   
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

        echo '<label for="newMoveName">Move Name:</label><br>';
        echo '<input list="names" name="move_name" id="move_name">';
        echo '<datalist id="names">';     

        $sql = "SELECT move_name FROM moves;";
        if (!$result = $conn->query($sql)){ // get move names.
            echo "\n Failed to get move names.";
            exit;
        }
        $names_arr = $result->fetch_all(); // convert to array.

        for ($i = 0; $i < $result->num_rows; $i++){ // iterate
            $movename = $names_arr[$i][0];
            echo '<option value='.$movename.'>'; // add option for every move.
        }
        echo '</datalist>'; 
         
        //add a submit button to the form and close out the form
        echo '<input type="submit" value="Add" />';
        echo '</form>';
    }

// end of function
$conn->query("USE move_tutor;");
$sql="SELECT * FROM pokemons
NATURAL JOIN 
(SELECT poke_id, move_name FROM known_moves) AS a
ORDER BY poke_id ASC;";

if(!$result = $conn->query($sql)){
echo "Query failed!";
exit;}

$result = $conn->query($sql);
$qryres = $result->fetch_all();

result_to_table($result, $qryres,$conn); 
$file = file_get_contents('./../knownmoves_insert.sql', false);
$stmt = $conn->prepare($file);
if (isset($_POST["move_name"])){
    $name=$_POST["move_name"];
    for($i = 0; $i < $result->num_rows; $i++) {
        $id = $qryres[$i][0];
        //UPDATE statement found in same directory as self
        $stmt->bind_param('is', $id, $name);
        if (isset($_POST["checkbox$id"]) ){
            validateMoveType($conn,$name,$id);
            if (!$stmt->execute()) {
                echo $conn->error;
            } else {
                //redirect so refresh works properly
                header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
                exit();}    
        }
    }
}


$conn->close();

?>
</div>
</div>

</body>
</html>