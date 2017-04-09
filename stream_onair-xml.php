<?php
//ztm on 21oct10
//	this script takes the former functionality of ondj, ontrack, and onlisteners.php
//	and combines them into one script. this will drop two GET requests off the overall
//	page load of wsbf.net/listen - it will be an issue only at high capacities due to overhead.


require_once('stream_conn.php');
require_once('utils_ccl.php');


//pull current song - originally in ontrack.php
function getCurrentSong(&$output) {
	$query = "SELECT * FROM `now_playing`";
	$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$output[] = "<track>".htmlspecialchars($row['lb_track_name'])."</track>";
		$output[] = "<artist>".htmlspecialchars($row['lb_artist_name'])."</artist>";
}



$output = array();

$listeners = getNumConnections("http://130.127.17.4:8000/status.xsl");
$output[] = "<listeners>".$listeners."</listeners>";

// pull the current show - originally in ondj.php
$q = sprintf("SELECT show.showID, show.start_time, show.end_time, show.show_name, show.scheduleID, show.max_listeners, def_show_types.type FROM `show`, `def_show_types` WHERE show.show_typeID = def_show_types.show_typeID ORDER BY show.showID DESC LIMIT 1");
$rs = mysql_query($q) or die(mysql_error());



// if this show started more than 24 hours ago...
//if( time() - strtotime($show['sStartTime']) > 86400) {
if(mysql_num_rows($rs) < 1) {	// should never happen again
	//echo time() - strtotime($show['sStartTime']);
	//echo "<br>";
	$output[] = "<showname>The Best of WSBF</showname>";
	$output[] = "<showid>-1</showid>";


	$output[] = "<djname>Automation</djname>";
	/** THIS IS INTERNET COURT: THE HONORABLE ED LOLLINGTON PRESIDING. ALL RISE. **/
	$output[] = "<track>My friend was eaten</track>";
	$output[] = "<artist>a Wizbif shark!</artist>";

}
else {
	$show = mysql_fetch_array($rs, MYSQL_ASSOC);
	htmlDisplaySanitize($show);
	$output[] = "<showname>".$show['show_name']."</showname>";
	$output[] = "<showid>".$show['showID']."</showid>";

	$djq = sprintf("SELECT users.preferred_name, show_hosts.show_alias FROM `users`, `show_hosts` WHERE show_hosts.showID = '%d' AND users.username = show_hosts.username", $show['showID']);
	$rdj = mysql_query($djq) or die("MySQL Error near line " . __LINE__ . ": " . mysql_error());

	$djs = array(); // figure out what to add to dj names
	while($djrow = mysql_fetch_assoc($rdj)){
		if(!empty($djrow['show_alias']))
			$djs[] = $djrow['show_alias'];
		else
			$djs[] = $djrow['preferred_name'];
	}

	if(count($djs) == 1)
		$output[] = "<djname>".$djs[0]."</djname>";
	if(count($djs) == 2)
		$output[] = "<djname>".$djs[0].' and '.$djs[1]."</djname>";
	if(count($djs) > 2)
		$output[] = "<djname>".implode(', ', $djs)."</djname>";

	/** this bit keeps track of the max # of listeners for each discrete show! **/
	$real_listeners = $listeners - 1;
	if($real_listeners > $show['max_listeners']) {
		$qu = sprintf("UPDATE `show` SET max_listeners='$real_listeners' WHERE showID='%d'", $show['showID']);
		mysql_query($qu) or die(mysql_error());
	}

	getCurrentSong($output);
}

header("Content-type: application/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
echo "<stuff>\n";
foreach ($output as $line) echo "\t".$line."\n";
echo "</stuff>\n";


?>
