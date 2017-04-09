<?php
//DAVID COHEN
//REWRITE of block_schedulenex.php 5/1/12 due to schema changes
require_once('stream_conn.php');
require_once('utils_ccl.php');
include('showtable.php');
//drupal_add_js('misc/block_now_playing.js');
drupal_add_js('misc/listen_stream.js');
sanitizeInput();

?>
<b><i>On Now!</i></b>
<div id="side_dj_ajax"><img src="/misc/ajax-loader.gif"/></div>
<b><i>Current Song:</i></b>
<div id="side_track_ajax"><img src="/misc/ajax-loader.gif"/></div>
<hr />

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

$nextOdd = $currTime % 2 == 0 ? $currTime + 1 : $currTime + 2;

if($currMin > 30)
	$currTime++;
$add = $currTime + 10;
$newHour = date('G', strtotime('+10 hours'));

$q = sprintf("SELECT schedule.scheduleID, schedule.start_time, schedule.end_time, schedule.show_name, schedule_hosts.schedule_alias, schedule.dayID, users.username, users.preferred_name
	FROM `schedule`, `schedule_hosts`, `users`
	WHERE schedule.active = 1
		AND (schedule.dayID = '%d' AND start_time >= '%d' 
			OR schedule.dayID = '%d' AND start_time <= '%d')
		AND schedule_hosts.scheduleID = schedule.scheduleID
		AND schedule_hosts.username = users.username
	ORDER BY schedule.dayID, schedule.start_time, users.username ASC LIMIT 4", $today, $nextOdd, $tomorrow, $newHour);

$result = mysql_query($q) or die("Fatal error near line " . __LINE__ . ": " . mysql_error());

$upcomingShows = array();
/** generate array of upcoming shows, in which each show has an array of hosts */

while($row = mysql_fetch_assoc($result)){
	// set name depending on schedule_alias
	$name = !empty($row['schedule_alias']) ? $row['schedule_alias'] : $row['preferred_name'];
	// is this show already in the array? if so, it's another host, so just add the host's name
	if(array_key_exists($row['scheduleID'], $upcomingShows))
		$upcomingShows[$row['scheduleID']]['hosts'][] =  $name;
	else{
		$upcomingShows[$row['scheduleID']] = array();
		$upcomingShows[$row['scheduleID']] = $row;
		$upcomingShows[$row['scheduleID']]['hosts'] = array($name);
	}

} 


foreach($upcomingShows as $show){
	if($show['dayID'] == date('w',strtotime('now')))
		$start_disp = date("g:i a", strtotime($show['start_time']));
	else
		$start_disp = date('D', strtotime('tomorrow')) . " " . date("g:i a", strtotime($show['start_time']));

//	echo $show_id . " " .$curr_id;
	echo $start_disp . " - ";
	if(!empty($show['show_name']))
		echo $show['show_name'] . ", with ";

	for($i = 0; $i < count($show['hosts']); $i++)
		if($i == 0)
			echo $show['hosts'][$i];
		else if($i == count($show['hosts']) - 1)
			echo " and " . $show['hosts'][$i];
		else
			echo ", " . $show['hosts'][$i];

	echo "<br />";

}
echo "<hr />";
}
?>