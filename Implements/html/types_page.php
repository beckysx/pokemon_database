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
  <a href="#news">Pokemon</a>
  <a href="#news">Move</a>
  <a href="#news">Pokemon_type</a>
</div>

<div class="main">
<div class="header">
<h2>Insert A <i><u>New Type</u></i> !</h2>
<p>Sorry, you can only insert into Types table... you can't updating or deleting...</p>
</div>
<div class="contents">
<form action="types_page.php" method="post">
<label for="type_name">Type name:</label><br>
    <input type="text" id="type_name" name="type_name" 
    placeholder="Type name...Please check spelling...You can't update or delete in this table..."><br>

    <input type="submit" value="Submit">
</form>
<br> <br>
<table>
    <th>Existing Pokemon Types in the Database</th>
    <?php 
    $conn->query("USE $dbname;");
    $selectall_sql="SELECT * FROM types;";
    //get the tableresult object to make original table before inserting
    if (!$tableresult = $conn->query($selectall_sql)){ // get types
         echo "Failed to get types.";
         exit;
     }
    
    // prepare statement for insert a type
    $addstmt = $conn->prepare("INSERT INTO types (poke_type) VALUES (?)");
    $addstmt->bind_param('s', $type_name);

    // submit form
    if (isset($_POST["type_name"])){
      //get type name from user
      $type_name=$_POST["type_name"];
      // execute prepare stmt
      if(!$addstmt->execute()){
          echo $conn->error;
          echo "\n Insert query failed!";
       }else {
        echo "\n Insert query Successed! Check at table below!";
        //redirect so refresh works properly
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();}
      }
      
    // get table result object second time after inserting
    if (!$tableresult = $conn->query($selectall_sql)){ // get types
      echo "Failed to get types.";
      exit;}
    //make a table
    while ($type_names = $tableresult->fetch_array()) {
      echo "<tr>";
      echo "<td>".$type_names['poke_type']. "</td>";
      echo "</tr>";}

    $conn->close();
    ?>

</table>



</div>
</div>
</body>