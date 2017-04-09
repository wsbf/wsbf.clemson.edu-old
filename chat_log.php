<?php

require_once('conn.php');
$sq ="SELECT * FROM chatlog ORDER BY id DESC LIMIT 100";
$query = "SELECT * FROM ($sq) as t1 ORDER BY id ASC";
//echo "<pre>$query";
$q = mysql_query($query) or die(mysql_error());
while($row = mysql_fetch_array($q, MYSQL_ASSOC)){
	//echo "<pre>";
	//print_r($row);
	$id = $row['id'];
	
	$dStr = strtotime($row['time']);
	$time = "(" .date('g:i a', $dStr) .")";

	$name = $row['name'];
	$entry = $row['entry'];
	echo "<div class='msgln'>$time <b>$name</b>: $entry<br /></div>";
}
?>