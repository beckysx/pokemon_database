<!DOCTYPE html>
<html>
<head>
    <title>Add a New Pokemon</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php

/* Show all PHP errors. */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbhost = 'localhost';
$dbuser = 'webuser';
$dbpass = 'pass';
$dbname = 'move_tutor';

/* Make connection, and return error if it fails */
if (!$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname)){
    echo "Error: Failed to make a MySQL connection: " . "<br>";
    echo "Errno: $conn->connect_errno; i.e. $conn->connect_error \n";
    exit;
}

function insertType($conn, $id, $type_name) {
    /* Check that the type has been entered. */
    if (!empty($_POST[$type_name])){
        /* prepared statement. */
        $ins_stmt = $conn->prepare("INSERT INTO poke_types (poke_id, poke_type)
                                         VALUES (?, ?);");
        $ins_stmt->bind_param('is',$id,$_POST[$type_name]);

        if (!$ins_stmt->execute()) { // add type, return possible errors.
            echo $conn->error;
            echo "\n Failed to insert type.";
        } 
    }

}

function insertMove($conn, $id, $move_name) {
    /* Check that the move has been entered. */
    if (!empty($_POST["move_name"])){
        // prepared statement
        $ins_stmt = $conn->prepare("INSERT INTO known_moves (poke_id,move_name)
                                         VALUES (?,?);");
        $ins_stmt->bind_param('is',$id,$_POST[$move_name]);

        if (!$ins_stmt->execute()) { // add move, return possible errors.
            echo $conn->error;
            echo "\n Failed to insert move.";
        }

    }
}

function validateMoveType($conn, $move_name) {
    // check that move has been entered.
    if (!empty($_POST[$move_name])){
        // prepared statement to select move type.
        $sel_move_stmt = $conn->prepare("SELECT move_type FROM moves WHERE move_name = ?");
        $sel_move_stmt->bind_param('s',$_POST[$move_name]);

        if ($sel_move_stmt->execute()){ // execute select statement, return possible errors.
            echo $conn->error;
            echo "\n failed to select move type.";
        }

        $result = $sel_move_stmt->get_result(); // $result is an object
        $move_type_arr = $result->fetch_assoc(); // turn $result into associative array
        $move_type = $move_type_arr["move_type"]; // select only value of move_type field (our answer)

        /* Now, compare move type and pokemon types for validity. */
        
        if ($move_type != $_POST["poke_type1"] && // move type does not match type 1
           (!empty($_POST["poke_type2"]) || $move_type != $_POST["poke_type2"]) && // move type does not match type 2
           ($move_type != "normal")){ // move type is not normal
            echo "\n ERROR: Move type $move_type does not match pokemon types. Pokemon has not been inserted.";
            exit;
        }
    }
}

?>

<!-- <div class="sidebar">
  <a class="active" href="./menu_page.php">Menu</a>
  <a href="./pokemons_page.php">View Pokemon</a>
  <a href="./insert_pokemon_page.php">Insert Pokemon</a>
  <a href="./update_pokemon_both_page.php">Change Name & Species</a>
  <a href="./update_pokemon_name_page.php">Change Name</a>
  <a href="./update_pokemon_species_page.php">Evolve to New Species</a>
  <a href="./specific_pokemon_page.php">Check a Pokemon</a>
</div> -->
<?php

    echo file_get_contents("./pokemon_control_menu.html", false);

?>
<div class="main">

<div class="header">
    <h2>Add a New Pokemon!</h2>
</div>

<div class="contents">

<form action="insert_pokemon_page.php" method="post">

    <label for="poke_species">Pokemon Species:</label><br>
    <input type="text" id="poke_species" name="poke_species"><br>

    <label for="poke_name">Pokemon Nickname (optional):</label><br>
    <input type="text" id="poke_name" name="poke_name"><br>

    <label for="poke_type1">Pokemon Type 1:</label><br>
    <input list="types" name="poke_type1" id="poke_type1">
    <datalist id="types">
    <?php
        $sql = "SELECT poke_type FROM types";
        if (!$result = $conn->query($sql)){ // get result object.
            echo "Failed to get types.";
            exit;
        }
        $types_arr = $result->fetch_all(); // turn object into array.

        for ($i = 0; $i < $result->num_rows; $i++){ // iterate through array
            $typename = $types_arr[$i][0];
            echo "<option value=$typename>"; // print out every value into datalist
        }
    ?>
    </datalist><br>

    <label for="poke_type2">Pokemon Type 2 (optional)</label><br>
    <input list="types" name="poke_type2" id="poke_type2">
    <datalist id="types"></datalist><br>

    <label for="move_1">Move 1:</label><br>
    <input list="names" name="move_1" id="move_1">
    <datalist id="names">
    <?php
        $sql = "SELECT move_name FROM moves";
        if (!$result = $conn->query($sql)){ // get move names.
            echo "\n Failed to get move names.";
            exit;
        }
        $names_arr = $result->fetch_all(); // convert to array.

        for ($i = 0; $i < $result->num_rows; $i++){ // iterate
            $movename = $names_arr[$i][0];
            echo '<option value="'.$movename.'">'; // add option for every move.
        }
    ?>
    </datalist><br>

    <label for="move_2">Move 2 (optional):</label><br>
    <input list="names" name="move_2" id="move_2">
    <datalist id="names"></datalist><br>

    <label for="move_3">Move 3 (optional):</label><br>
    <input list="names" name="move_3" id="move_3">
    <datalist id="names"></datalist><br>

    <label for="move_4">Move 4 (optional):</label><br>
    <input list="names" name="move_4" id="move_4">
    <datalist id="names"></datalist><br>

    <input type="submit" value="Submit">

</form>

<?php
    /* Check that the mandatory form fields have been filled. */
    if (!empty($_POST["poke_species"]) && !empty($_POST["poke_type1"]) && !empty($_POST["move_1"])){

        /* Validate that the entered move types are either the same as one of the pokemon's
           types, or the normal type (as per business rule). */

        validateMoveType($conn,"move_1");
        validateMoveType($conn,"move_2");
        validateMoveType($conn,"move_3");
        validateMoveType($conn,"move_4");

        /* Firstly, insert the species and name (if entered) into the pokemon table. */

        if (!empty($_POST["poke_name"])){ // poke_name IS entered
            // prepared statement, of course.
            $ins_stmt = $conn->prepare("INSERT INTO pokemons (poke_species,poke_name)
                                             VALUES (?,?);");
            $ins_stmt->bind_param('ss',$_POST["poke_species"],$_POST["poke_name"]);

            if (!$ins_stmt->execute()) { // insert user data, return possible errors
                echo $conn->error;
                echo "\n Insert query failed!";
                exit;
            }
        }
        else { // poke_name is NOT entered
            // a different prepared statment.
            $ins_stmt = $conn->prepare("INSERT INTO pokemons (poke_species)
                                             VALUES (?);");
            $ins_stmt->bind_param('s',$_POST["poke_species"]);

            if (!$ins_stmt->execute()) { // insert pokemon species, return possible errors
                echo $conn->error;
                echo "\n Insert query failed!";
                exit;
            }
        }
        
        /* Now, get the ID of the pokemon we just added to use as a foreign key
           in poke_types and known_moves. */

        $id_object = $conn->query("SELECT max(poke_id) FROM pokemons;"); // get id object.
        if (!$id_object){ // check for failure
            echo "\n Failed to get pokemon id.";
            exit();
        }
        $id_arr = $id_object->fetch_all(); // turn object into array.
        $inserted_id = $id_arr[0][0]; // get value [0][0] from the array: the integer id.

        /* Now, insert into poke_types. */

        insertType($conn, $inserted_id, "poke_type1");
        insertType($conn, $inserted_id, "poke_type2");

        /* Now, insert into known_moves. */

        insertMove($conn, $inserted_id, "move_1");
        insertMove($conn, $inserted_id, "move_2");
        insertMove($conn, $inserted_id, "move_3");
        insertMove($conn, $inserted_id, "move_4");

        header("Location: {$_SERVER['REQUEST_URI']}", true, 303); // redirect.

        $conn->close();

    }
?>

</div>
</div>

</body>
</html>