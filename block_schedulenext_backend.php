<?php
/* GETS UPCOMING SHOWS */
if(isset($freeform) && $freeform == TRUE)
	echo "WSBF is in freeform mode.";
else $freeform = FALSE;
if($freeform == FALSE){

echo "<b><i>Upcoming Shows:</i></b><br />";
$today = date('w');
$tomorrow = date('w', strtotime('+1 day'));
$currTime = date('G');
$currMin = date('i'); 

if($currMin > 30)
	$currTime++;
$add = $currTime + 10;
$newHour = date('G', strtotime('+10 hours'));

if($add < 24)
	$q = "SELECT * FROM shows 
	WHERE start_hour > $currTime 
	AND day = '$today' AND show_id != '$curr_id' ORDER BY start_hour,start_min ASC LIMIT 5";
 else
	// this query is actually not a great one beacuse it doens't take care of saturday - sunday (it displays sundays first)
	$q = "SELECT * FROM shows 
		WHERE(day = '$today' AND show_id != '$curr_id' AND start_hour >= $currTime) OR day = '$tomorrow'  ORDER BY day,start_hour,start_min ASC LIMIT 5";


$rs = mysql_query($q);
while($row = mysql_fetch_assoc($rs)){
	$show_id = $row['show_id'];

$djq = "SELECT djs.name,djs.alias,djs.drupal FROM djs, show_dj WHERE show_dj.dj_id = djs.dj_id AND show_dj.show_id = $show_id";
$result = mysql_query($djq) or die(mysql_error());

/*
The following checks to see if alias exists, and if it does, it makes dj_name the alias. In addition, it can handle multiple djs, separating them with commas.Making dj_name into an array and imploding with a comma puts commas in the middle but none at the end.
*/
				$dj_name = array();
				while($r = mysql_fetch_assoc($result)){
					if(!isset($r['alias']) || $r['alias'] == '')
						$dj_name[] = $r['name'];					
					elseif(!isset($r['name']) || $r['name'] == '')
						$dj_name[] = "Wizbif Deejay";
					else
						$dj_name[] = $r['alias'];
				}
//separates it by "and" if there are only two djs
	if(count($dj_name) == 2)
		$dj_name = implode($dj_name, ' and ');
	else
		$dj_name = implode($dj_name, ', ');
		
	$show_name = stripslashes($row['show_name']);
	$start_hour = $row['start_hour'];
	$start_min = $row['start_min'];	
	$start_time = date("g:i a", strtotime("$start_hour:$start_min"));


//	echo $show_id . " " .$curr_id;
	echo "$start_time - ";
	if($row['show_name'] != "")
	echo "$show_name with ";
	echo $dj_name;
	echo "<br />";

}
echo "<hr />";
}
?>