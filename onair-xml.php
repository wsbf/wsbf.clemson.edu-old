<?php
//ztm on 21oct10
//	this script takes the former functionality of ondj, ontrack, and onlisteners.php
//	and combines them into one script. this will drop two GET requests off the overall
//	page load of wsbf.net/listen - it will be an issue only at high capacities due to overhead.


require_once('conn.php');
require_once('utils_ccl.php');


//pull current song - originally in ontrack.php
function getCurrentSong(&$output) {
	$query = "SELECT * FROM lbplaylist WHERE pCurrentlyPlaying = 1 ORDER BY pDTS DESC LIMIT 1";
	$result = mysql_query($query) or die(mysql_error());
	if(mysql_num_rows($result) == 1) {
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$output[] = "<track>".htmlspecialchars($row['pSongTitle'])."</track>";
		$output[] = "<artist>".htmlspecialchars($row['pArtistName'])."</artist>";
	}
}



$output = array();

$listeners = getNumConnections("http://stream.wsbf.net:8000/status.xsl");
$output[] = "<listeners>".$listeners."</listeners>";

// pull the current show - originally in ondj.php
$qu = "SELECT lbshow.sID, lbshow.sStartTime, lbshow.sShowName, lbshow.sDJName, lbshow.sMaxListeners
		FROM lbshow WHERE lbshow.sEndTime='0000-00-00 00:00:00'
		AND sID > 9400
		ORDER BY lbshow.sID DESC LIMIT 1";
$rs = mysql_query($qu) or die(mysql_error());


// if this show started more than 24 hours ago...
//if( time() - strtotime($show['sStartTime']) > 86400) {
if(mysql_num_rows($rs) < 1) {
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
	$output[] = "<showname>".$show['sShowName']."</showname>";
	$output[] = "<showid>".$show['sID']."</showid>";

	$djs = explode(', ', $show['sDJName']);
	foreach($djs as &$dj) {
		$q = "SELECT * FROM djs WHERE name='$dj' LIMIT 1";
		$r = mysql_query($q) or die(mysql_error());
		$row = mysql_fetch_array($r, MYSQL_ASSOC);
		htmlDisplaySanitize($row);
		if($row['alias'] != '')
			$dj = $row['alias'];
	}
	if(count($djs) == 1)
		$output[] = "<djname>".$djs[0]."</djname>";
	if(count($djs) == 2)
		$output[] = "<djname>".$djs[0].' and '.$djs[1]."</djname>";
	if(count($djs) > 2)
		$output[] = "<djname>".implode(', ', $djs)."</djname>";

	/** this bit keeps track of the max # of listeners for each discrete show! **/
	$real_listeners = $listeners - 1;
	if($real_listeners > $show['sMaxListeners']) {
		$qu = "UPDATE lbshow SET sMaxListeners='$real_listeners' WHERE sID='".$show['sID']."'";
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
