<?php
/** David Cohen **/

require_once('conn.php');
//require_once('review_lib.php');
require_once('utils_ccl.php');
sanitizeInput();


$tabs = array("W"=>"Full Week",
					"1"=>"Monday", 
					"2"=>"Tuesday", 
					"3"=>"Wednesday", 
					"4"=>"Thursday", 
					"5"=>"Friday",
					"6"=>"Saturday",
					"0"=>"Sunday");


$tab = $_GET['tab'];
if(strlen($tabs[$tab]) < 1)
	die("Invalid tab code.");
elseif($tab == "W"){
	//include("schedule_week.php");
	require_once('schedule_week.php');
}

else{ //this includes all the individual days
echo "<h2>".$tabs[$tab]."</h2>\n";

echo "<table id='$tab'><tr><td>Time</td><td>DJ</td><td>Show Name</td><td>Description</td></tr>";

// AND libcd.cAlbumNo != '' 
$q = "SELECT * FROM shows WHERE day = $tab ORDER BY start_hour ASC";
$rs = mysql_query($q) or die(mysql_error());
while($row = mysql_fetch_assoc($rs)){
	sanitize($row);
	$show_id = $row['show_id'];
	$show_name = htmlspecialchars($row['show_name']);
	$show_desc = htmlspecialchars($row['show_desc']);
	$start_hour = $row['start_hour'];
	$start_min = $row['start_min'];
	$show_length = $row['show_length'];
	$show_type = $row['specialty'];

	$djq = "SELECT djs.name, djs.alias FROM djs, show_dj WHERE show_dj.dj_id = djs.dj_id AND show_dj.show_id = $show_id";
	$result = mysql_query($djq) or die(mysql_error());

//the following checks to see if alias exists, and if it does, it makes dj_name the alias
//in addition, it can handle multiple djs, separating them with commas
//making dj_name into an array and imploding with a comma puts commas in the middle but none at the end.

		$dj_name = array();
		while($row = mysql_fetch_assoc($result)){
			if(!isset($row['alias']) || $row['alias'] == '')
				$dj_name[] = $row['name'];
			else
				$dj_name[] = htmlspecialchars($row['alias']);
		}
		$dj_name = implode($dj_name, ', ');
		
$start_time = date("g:i a", strtotime("$start_hour:$start_min"));
$offset = strtotime("+$show_length minutes", strtotime("$start_hour:$start_min"));
$end_time = date("g:i a", $offset);
if($show_type == 0)
	echo "<tr class='rotation'>";
elseif($show_type == 1)
		echo "<tr class='specialty'>";
elseif($show_type == 2)
		echo "<tr class='sportstalk'>";
elseif($show_type == 3)
		echo "<tr class='jazz'>";
else
	echo "<tr>";

echo "<td>$start_time - $end_time</td><td>$dj_name</td><td>$show_name</td>";
//shows a button if the dj has written a description for the show
if($show_desc != '')
	echo "<td><button type='button' onclick=\"openDialog('$show_name','$dj_name','$show_desc')\">About This Show</button></td></tr>";
else 
	echo "<td></td></tr>";
}
echo "</table>";
}
?>