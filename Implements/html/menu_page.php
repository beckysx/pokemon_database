<html>
<head>
<title>Manu Page</title>
<link rel="stylesheet" href="styles.css">
<style>
ul {
  list-style-type: none;
  padding: 0;
  border-bottom: 1px solid #ddd;
  font-size: 20px;
}
ol{
    font-size:18px;
}

ul li {
  padding: 15px 20px;
  border-bottom: 1px solid #ddd;
}

ul li:last-child {
  border-bottom: none
}
ol li:hover{
    background: #D8BFD8;
    font-weight:bold;
    cursor:pointer;
}
li:hover{
    background: #E6E6FA;
    cursor:pointer;
}

.header {
  padding: 30px;
  text-align: center;
  background: #2EAEBD;
  color: white;
  font-size:27px;
}



</style>
</head>
<body>

<?php 

  echo file_get_contents("./main_menu.html", false);
?>

<!-- <div class="sidebar">
  <a class="active" href="./menu_page.php">Menu</a>
  <a href="./pokemons_page.php">Pokemon</a>
  <a href="./poke_types_page.php">Poke_Type</a>
  <a href="./known_moves_page.php">Known_Moves</a>
  <a href="./moves_page.php">Moves</a>
  <a href="./types_page.php">Types</a>
  <a href="./learn_history_page.php">Learn_History</a>
  <a href="./specific_pokemon_page.php">Specific Pokemon</a>
  <a href="./move_trend_page.php">Move Trend</a>
</div> -->



<div class="main">
<div class="header">
<h1>Welcome to <i><b><u>Tutor Database</u></b></i></h2>
<h2>Which operation do you want to do?</h2>
</div>


<div class="contents">
<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

// set variables to connect mariadb
$dbhost = 'localhost';
$dbuser = 'webuser';
$dbpass = 'pass';

// connect to mariadb
$conn = new mysqli($dbhost, $dbuser, $dbpass);

// choose the target database
$conn->query("USE move_tutor;");

echo "<ul>";
    echo "<li><b>Tables</b> (Inserting, Updating, Deleting)";
    echo "<ol>";
    // show all the table names in the target database
    $tableresult = $conn->query("SHOW TABLES;")or die("Last error: {$conn->error}\n");
        while ($tablename = $tableresult->fetch_array()) {
            // print each table name
            $name=$tablename['Tables_in_move_tutor'];
            echo "<li>";
            echo "<a href="."./".$name."_page.php".">".$name."</a>";
            echo "</li>";
        }
    // end connection
    $conn->close();

?>
</ol>
</li>
<li><b><a href="./move_trend_page.php"> Check on Move Trend</a></b></li>
<li><b><a href="./specific_pokemon_page.php">Check on the specific Pokemon</a></b></li>
</ul>
</div>
</div>
</body>

</html>
