<html>
<head>
    <title>Check a Pokemon</title>
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
    <a href="./pokemons_page.php">Pokemon</a>
    <a href="./poke_types_page.php">Poke_Type</a>
    <a href="./known_moves_page.php">Known_Moves</a>
    <a href="./moves_page.php">Moves</a>
    <a href="./types_page.php">Types</a>
    <a href="./learn_history_page.php">Learn_History</a>
    <a href="./move_trend_page.php">Move Trend</a>
</div>

<div class="main">
<div class="header">
<h2>Come check <i><u>Pokemon</u></i> Full Information!</h2>
<p>This page shows general information about all Pokemon.</p>
</div>
<div class="contents">
<table>
    <tbody>
    <?php
    $conn->query("USE move_tutor;");
    $sql="SELECT * FROM pokemons
    INNER JOIN
    (SELECT poke_id, GROUP_CONCAT(move_name) AS Moves
    FROM known_moves
    GROUP BY poke_id) AS t USING(poke_id)
    INNER JOIN
    (SELECT poke_id, GROUP_CONCAT(poke_type) AS Types
    FROM poke_types
    GROUP BY poke_id) AS a USING(poke_id)
    INNER JOIN
    (SELECT poke_id, COUNT(move_name) AS Learning_Time
    FROM learn_history
    GROUP BY poke_id) AS b USING(poke_id);";
            
    $result = $conn->query($sql) or die($conn->error);
    $qryres = $result->fetch_all();
    $n_rows = $result->num_rows; // num_rows
    $n_cols = $result->field_count; // num_col
    $fields = $result->fetch_fields();
    echo '<tr>';
    for ($i=0; $i<$n_cols; $i++){
        echo "<th>". $fields[$i]->name ."</th>";
    }
    echo '</tr>'; 
        
    // Should displayu
    for ($i=0; $i<$n_rows; $i++){
        echo "<tr>";
        for($j=0; $j<$n_cols; $j++){ echo "<td>" . $qryres[$i][$j] . "</td>";}
        echo "</tr>\n";
    }

    ?>
    </tbody>
    </table>
</div>
</div>
</body>
</html>