<?php
$debug = false;
//Accessed via AJAX call from logging software
//Outputs the current 'shows' primary key, or -1 if we're on automation
//Zach Musgrave, WSBF-FM Clemson, Dec 2009

require_once("../conn.php");

$q = "SELECT * FROM lbshow ORDER BY sID DESC LIMIT ";
if($debug) $q .= 2; else $q .= 1;

$rsc = mysql_query($q);
$row = mysql_fetch_array($rsc, MYSQL_ASSOC);


//if($debug) $row = mysql_fetch_array($rsc, MYSQL_ASSOC);

// show_id - correlates with 'shows' primary key
// sID - primary key of 'lbshow'

$time_end = strtotime($row['sEndTime']);
//echo $row['sDJName'];

$listeners = getNumConnections("http://stream.wsbf.net:8000/status.xsl");


if($row['show_id'] != "" && trim($row['sShowName']) =="") {
	$q2 = "SELECT * FROM shows WHERE show_id=" . $row['show_id'];
	$r2 = mysql_query($q2);
	$row2 = mysql_fetch_array($r2, MYSQL_ASSOC);
	$row['sShowName'] = $row2['show_name'];
	//if($row2['specialty'] != 0)
	//	$row['sShowType'] = 
	
}

$ttime = strtotime($row['sStartTime']);
//$ttime = date("Y-m-d <b\\r> h:i:s A",$ttime);
$ttime = date("F j, Y, g:i a", $ttime);

if($time_end === FALSE) {
	echo $row['sID']."|".$ttime."|".$row['sDJName']."|";
	echo $row['sShowType']."|".htmlspecialchars_decode($row['sShowName'])."|".$row['show_id']."|";
	echo $listeners;
}	
else echo -1;


mysql_close();

?>