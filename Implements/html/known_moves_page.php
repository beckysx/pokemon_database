<html>
<head>
    <title>Kown_Move Page</title>
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
  <a href="./insert_known_move_page.php">Insert Known Move</a>
  <a href="./delete_known_move_page.php">Forget a known Move</a>
</div>

<div class="main">
<div class="header">
<h2>Welcom to <i><u>Known_Move</u></i> Page!</h2>
<p>See what moves existing Pokemons have learnt below! You can do other operations through sidebar....</p>
</div>
<div class="contents">
<table>
<tbody>
<?php
    $conn->query("USE move_tutor;");
    $sql="SELECT * FROM pokemons
    NATURAL JOIN
    (SELECT poke_id, GROUP_CONCAT(move_name) AS known_moves
    FROM known_moves
    GROUP BY poke_id) AS t;";
    $result = $conn->query($sql);
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