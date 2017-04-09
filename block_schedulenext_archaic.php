<?php
	
	require_once("conn.php");
echo "<h3 style='text-align: center'>Radio Listens to You!</h3>
<hr>";



$time = time();
$hour = date("G", $time); //24hrs, no leading zeros
$day = date("w", $time); //0 for sun, 6 for sat
$next_day = date('w', strtotime('+1 day'));
//$day = 6; $hour = 20;

if($day+1 == 7)
	$next_day = 0;
else $next_day = $day + 1;

// changed shows to `$showtable` -DAC
//$showtable = "shows";
	require_once("showtable.php");
$qu = "SELECT * FROM `$showtable` WHERE ";
$qu .= "(day=$day AND start_hour > ".($hour)." AND start_hour < ";

if($hour + 8 > 24)
	$qu .= "24) OR (day=$next_day AND start_hour < ".($hour-16).")";
else
	$qu .= ($hour+8) . ")";

$qu .= " ORDER BY day ASC, start_hour ASC";

//echo "<pre>$qu</pre>";
$rsc = mysql_query($qu) or die("Error: shows_upcoming.php: \n ".mysql_error()."\n");
//echo mysql_error();

/** print out current show (special case) **/
/** 9400 is a kludge that avoids broken entries from old logbook **/
	$qu = "SELECT * FROM lbshow WHERE sEndTime=0 AND sID >'9400' ORDER BY sID DESC LIMIT 1";
	$rs = mysql_query($qu);
	$output = "<b>On now!</b> <a href='/listen'>";
	if(mysql_num_rows($rs) == 1) {
		$now = mysql_fetch_array($rs, MYSQL_ASSOC);
		$name = $now['sDJName'];
			$query = "SELECT * from `djs` WHERE `name`='$name'";
			$result2 = mysql_query($query) or die("Query failed : " . mysql_error());
			$dj_info = mysql_fetch_array($result2);
			$alias = $dj_info['alias'];
			if (empty($alias)) 
				$alias = $name;
		if($now['sShowName'] == "")
			$output .= $alias;
		else 
			$output .= $now['sShowName'] . ", with " . $alias . "";
		/**
		if($nowinfo[4] < 12)
			$output .= $nowinfo[4] . " a.m. to ";
		$output .= ($nowinfo[4] - 12) . " p.m. to ";
		if($nowinfo[4] + 2 < 12)
			$output .= ($nowinfo[4] + 2) . " a.m.)";
		$output .= ($nowinfo[4] + 2 - 12) . " p.m.)";
			**/
		echo "$output</a><hr>";
	}
	else echo "$output Automation</a><hr>";
	

/** $freeform is bound in showtable.php **/

if(isset($freeform) && $freeform === TRUE)
	echo "WSBF is in freeform mode.";
 $freeform = FALSE;

/** print out upcoming shows **/

while($row = mysql_fetch_array($rsc, MYSQL_ASSOC) && $freeform !== TRUE) {
// 	show_id	dj_name	day	start_hour	show_name	show_desc	num_ratings	avg_rating	image	archivestart	specialty
	$output = "";
	$name_out = "";
	
	
	$names = mysql_real_escape_string($row['dj_name']);
	$names = explode(", ", $names);
	$names_out = array();
	for($i = 0; $i < count($names); $i++) {
		$isdj = true;
		$alq = "SELECT alias FROM djs WHERE name='" . $names[$i] . "'";
		$rs = mysql_query($alq);
		$alias = "";
		if(mysql_num_rows($rs) == 1)
			$alias = mysql_result($rs, 0, 0);
		else if (mysql_num_rows($rs) == 0)
			$isdj = false;
		else die();
		
		if($alias != "")
			$nameout = $alias;
		else $nameout = $names[$i];
		
		if($isdj)
			$names_out[$i] = "<a href='/schedule?name=".$names[$i]."'>".$nameout."</a>";
		else 
			$names_out[$i] = "Wizbif Deejay";

	}
	
	
//echo "<pre>";	
	
	$timestr = "";
	if($row['start_hour'] > 12)
		$timestr .= ($row['start_hour'] - 12) . " p.m.";
	else $timestr .= $row['start_hour'] . " a.m.";
	$timestr .= " to ";
	
	//the following if-else should account for the 3-hour summer shows
if($showtable=="shows"){
	if ( ($row['start_hour'] + 2) > 24 )
		$timestr .= ($row['start_hour'] + 2 - 24) . " a.m.";
	else if( ($row['start_hour'] + 2) > 12) 
		$timestr .= ($row['start_hour'] + 2 - 12) . " p.m.";
	else $timestr .= ($row['start_hour'] + 2) . " a.m.";
}

else {
	if ( ($row['start_hour'] + 3) > 24 )
		$timestr .= ($row['start_hour'] + 3 - 24) . " a.m.";
	else if( ($row['start_hour'] + 3) > 12) 
		$timestr .= ($row['start_hour'] + 3 - 12) . " p.m.";
	else $timestr .= ($row['start_hour'] + 3) . " a.m.";
     }
	
	
	if($row['show_name'] != "")
		$output .= "<a href='/schedule?id=".$row['show_id']."'>".$row['show_name']."</a>, with ";


	$output .= implode(", ", $names_out);
	$output .= " ($timestr)<hr>";
	
	echo $output;
}
/** This line's absence MAY have been causing the connection errors **/
/** by filling up the TCP pool... maybe **/
if(is_resource($link)) mysql_close($link);
?>
