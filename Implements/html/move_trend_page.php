<html>
<head>
    <title>Move Trends Page</title>
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
    
    <?php

echo file_get_contents("./main_menu.html", false);

?>

<div class="main">
<div class="header">
<h2>Welcome to the <i><u>Move Trend</u></i> Page!</h2>
<p>The popularity of moves taught by the trainer is most popular to least popular.</p>
</div>
<div class="contents">
<table>
    <tbody>
    <?php
    $conn->query("USE move_tutor;");
    $sql="SELECT * FROM moves
            INNER JOIN
            (SELECT learn_history.move_name, COUNT(learn_history.move_name) AS times_taught 
            FROM learn_history
            GROUP BY learn_history.move_name) AS a
            USING (move_name)
            ORDER BY times_taught DESC;";
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