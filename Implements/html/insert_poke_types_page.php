<!--
    This file will update the name or species of a pokemon in the
    pokemons table
-->

<!DOCTYPE html>
<html>
<head>
<title>Insert Pokemon Type</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<div class='sidebar'>
    <a class="active" href="./menu_page.php">Menu</a>
</div>


<div class="main">
<div class="header">
<h2>Insert <i><u>Pokemon Type</u></i> !</h2>
<p>Insert Pokemon type here, you can do other operations through sidebar....</p>
</div>
<div class="contents">
<?php
    // Show ALL PHP's errors.
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    function get_pokemon($conn) {
        $sql = "SELECT poke_id FROM pokemons";
        if (!$result = $conn->query($sql)){ // get types
            echo "Failed to get types.";
            exit;
        }
        return $result;
    }

    function get_types($conn) {
        $sql = "SELECT poke_type FROM types";
        if (!$result = $conn->query($sql)){ // get types
            echo "Failed to get types.";
            exit;
        }
        return $result;
    }


    function result_to_table($conn) {
        //$qryres = $result->fetch_all(); // get array of rows from result object, so we can iterate more than once
        // $n_rows = $result->num_rows; // num_rows
        // $n_cols = $result->field_count; // num_col
        
        // Description of table -------------------------------------
        // echo "<p>This table has $n_rows rows and $n_cols columns.</p>\n";
        

        //wrap table in a form and call self
        echo '<form action="insert_poke_type_page.php" method=POST>';
        
        echo "<label for='pkmnlbl'>Pokemon ID:</label><br>";
        echo "<input list='pkmndatalist' name='pkmn_list' id='pkmn_list'>";

        echo "<datalist id='pkmndatalist'>";

        $pokemon_qry = get_pokemon($conn);

        $pokemon_list = $pokemon_qry->fetch_all();

        for ($i = 0; $i < $pokemon_qry->num_rows; $i++) {
            $pkmn_id = $pokemon_list[$i][0];
            echo "<option value='$pkmn_id'>";
        }

        echo "</datalist><br>";

        echo "<label for='typelbl'>Type:</label><br>";
        echo "<input list='typedatalist' name='type_list' id='type_list'>";

        echo "<datalist id='typedatalist'>";

        $type_qry = get_types($conn);

        $type_list = $type_qry->fetch_all();

        for ($i = 0; $i < $type_qry->num_rows; $i++) {
            $type = $type_list[$i][0];
            echo "<option value=$type>";
        }

        echo "</datalist><br>";
        
        echo '<p><input type="submit"/></p></form>';
    }


    $dbhost = 'localhost';
    $dbuser = 'webuser';
    $dbpass = 'pass';

    $dbname = "move_tutor";

    
    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    
    if ($conn->connect_errno) {
        echo "Error: Failed to make a MySQL connection: " . "<br>";
        echo "Errno: $conn->connect_errno \n";
        echo "Error: $conn->connect_error \n";
        exit;
    }

    // $sql = "SELECT poke_id, poke_species, poke_name FROM pokemons";
    $sql = "SELECT poke_id, poke_name FROM pokemons";
    

    if(!$result = $conn->query($sql)){
        echo "Query failed!";
        exit;
    }

    // $result = $conn->query($sql);
    // $qryres = $result->fetch_all();

    result_to_table($conn); 

    //check if a name was entered into the textbox for UPDATE
    if(isset($_POST["pkmn_list"]) && $_POST["pkmn_list"] != "" && isset($_POST["type_list"]) && $_POST["type_list"] != "") {
        

        $id = $_POST["pkmn_list"];
        $type = $_POST["type_list"];
        //UPDATE statement found in same directory as self
        $file = file_get_contents('./../poketypes_insert.sql', false);
        $stmt = $conn->prepare($file);
        $stmt->bind_param('is', $id, $type); 

        if (!$stmt->execute()) {
            echo $conn->error;
            echo "\n Insert query failed!";
        } else {
            //redirect so refresh works properly
            header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
            exit();
        }

        
            
        
    }
    
    
    

    


    $conn->close();

    
?>
</div>
</div>
</body>
</html>

