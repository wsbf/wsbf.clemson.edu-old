<?php
require_once('../connect.php');

//$djr = mysql_query("SELECT name FROM djs WHERE still_here=1 ORDER BY sort_by, name ASC") or die(mysql_error());

$djr = mysql_query("SELECT name FROM djs WHERE still_here=1 ORDER BY name ASC") or die(mysql_error());


while($row = mysql_fetch_array($djr, MYSQL_ASSOC)) {
	$dj = $row['name'];
	echo "<option value='$dj'>$dj</option>\n";
}



?>