<?php
require_once('../connect.php');
$qu = "SELECT show_id, show_name FROM shows WHERE show_name <> '' ORDER BY show_name ASC";

$showr = mysql_query($qu) or die(mysql_error());

while($row = mysql_fetch_array($showr, MYSQL_ASSOC)){
	echo "<option value='".$row['show_id']."'>".stripslashes($row['show_name'])."</option>\n";
}


?>