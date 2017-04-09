<?php

include('conn.php');
define('FIRST_SHOW', 9545);
define('BASE_PATH', "E:/WAMP/www/new/archpk/");
define('WEB_PATH', "http://wsbf.net/archpk/");

if(!isset($_GET['sid'])) {
	
	$query = "SELECT * FROM lbshow WHERE sID >= ".FIRST_SHOW." ORDER BY sID DESC";
	$rsc = mysql_query($query) or die(mysql_error());


	echo "<h2 style='margin: 0 auto; width: 300px'>Download MP3 Show Archives</h2>";
	echo "<table><tr><th>Click</th><th>DJ</th><th>Show(s)</th><th>Date</th></tr>";

	while($arr = mysql_fetch_array($rsc, MYSQL_ASSOC)) {
		echo "<tr><td><a href='?sid=".$arr['sID']."'>".$arr['sID']."</a></td>".
				"<td style='width: 25%'>".$arr['sDJName']."</td>".
				"<td style='width: 25%'>".$arr['sShowName']."</td>".
				"<td>".date("l, F j, Y", strtotime($arr['sStartTime']))."</td></tr>";
	
	
	}
	echo "</table>";
}
else {
	$query = "SELECT * FROM lbshow WHERE sID = ".mysql_real_escape_string($_GET['sid'])." LIMIT 1";
	$rsc = mysql_query($query) or die(mysql_error());
	$arr = mysql_fetch_array($rsc, MYSQL_ASSOC);
	$playlist = "playlists?showid=".$_GET['sid'];
	echo "<h2 style='margin: 20px auto; width: 30%; text-align: center'>Records for ".$arr['sID']."</h2>";
	echo "<p><a href='?'>Go back</a></p>";
	
	echo "<table style='margin: 0 auto; width: 90%'><tr><td>DJ</td><td>".$arr['sDJName']."</td></tr>".
		"<tr><td>Show Name</td><td>".$arr['sShowName']."</td></tr>".
		"<tr><td>Show Type</td><td>".$arr['sShowType']."</td></tr>".
		"<tr><td>Start Time</td><td>".date("g:i a, l, F j, Y", strtotime($arr['sStartTime']))."</td></tr>".
		"<tr><td>End Time</td><td>".date("g:i a, l, F j, Y", strtotime($arr['sEndTime']))."</td></tr>".
		"</table>";
	echo "<p><h2><a href='$playlist'>View playlist</a></h2></p>";
	echo "<h2 style='margin: 20px auto; width: 30%; text-align: center'>Download Links</h2>";
	echo "<table style='margin: 20px auto; width: 25%'>";
	
	$count = 0;
	$append = "";
	
	while(TRUE) {
		if($count > 0) $append = " ($count)";
		$relpath = BASE_PATH.$arr['sID'].$append.".mp3";
		
		if(file_exists($relpath))
			echo "<tr><td><a href='".WEB_PATH.$arr['sID'].$append.".mp3'>".$arr['sID'].$append.".mp3</a></td></tr>";
		else break;
		$count++;
		$relpath = BASE_PATH.$arr['sID'].$append.".mp3";
	}
	echo "</table>";
	
}




?>