<?php

//Outputs a complete XHTML page (for inclusion via iframe)

//All songs DJ has played (so far) in their show
//Zach Musgrave, WSBF-FM Clemson, Oct 2009 (Revised from XML output Aug 2010)

/** header("Content-type: application/xml"); **/
//require_once('logging_header.php');
require_once('../conn.php');
require_once('../utils_ccl.php');

$day = date("j");
$day = str_pad($day, 2, "0", STR_PAD_LEFT);
$sID = mysql_real_escape_string($_GET['sid']);
if($sID != '' && $sID != -1) { //
	$q = "SELECT * FROM lbshow WHERE sID='$sID'";
	$rsc = mysql_query($q) or die(mysql_error());
	$show_record = mysql_fetch_array($rsc);
	$sID = $show_record['sID'];
}
else {
	//$q = "SELECT * FROM lbshow WHERE sEndTime=0 ORDER BY sID DESC LIMIT 1";
	
	/** this change was made for automation mode to show no playlist **/
	die("<table id='log'></table>");
	echo "";
}

/** Modification. Because the old software leaves orphaned sEndTime... **/
//$q = "SELECT * FROM lbshow WHERE sEndTime=0 AND ORDER BY sID DESC LIMIT 1";
//$q = "SELECT * FROM lbshow WHERE sEndTime=0 AND ***** ORDER BY sID DESC LIMIT 1";
/** End mod **/

//debug statement
//$sID = 8921;

//used to be ORDER BY pDTS DESC

//echo "TROLL". $sID; die();

$query = "SELECT * FROM lbplaylist WHERE p_sID = '$sID' ORDER BY pNumInShow ASC";
if($sID == '-1')
	$query = "SELECT * FROM lbplaylist WHERE p_sID='$sID' ORDER BY pDTS DESC LIMIT 10";

$result = mysql_query($query) or die(mysql_error());

/**
echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
echo "<playlist>\n";
echo "<show>$sID</show>\n";
**/

?>
<!--<div id="users-contain" class="ui-widget">-->
	<table id='log'>
		<tr id='top' class='ui-widget-header'>
			<th class='small'>Now Playing</th>
			<th class='small'>Album #</th>
			<th class='small'>Track #</th>
			<th>Rotation</th>
			<th class='std'>Track Name</th>
			<th class='std'>Artist</th>
			<th class='std'>Album Name</th>
			<th class='std'>Record Label</th>
		</tr>
	<?php
while($record = mysql_fetch_array($result)) {
	
		$rot = $record['pRotation'];
		$num = $record['pNumInShow'];
		$albnum = $record['pAlbumNo'];
		$trknum = $record['pTrackNo'];
	
		$pID = $record['pID'];
		$artist = htmlspecialchars($record['pArtistName']);
	    $track = htmlspecialchars($record['pSongTitle']);
	    //$time = strtotime($record['pDTS']);
		$label = htmlspecialchars($record['pRecordLabel']);
		$album = htmlspecialchars($record['pAlbumTitle']);
		$nowP = $record['pCurrentlyPlaying'];


/*	while($record = mysql_fetch_array($result)) {

		$rot = $record['pRotation'];
		$num = $record['pNumInShow'];
		$albnum = $record['pAlbumNo'];
		$trknum = $record['pTrackNo'];
	
		$pID = $record['pID'];
		$artist = $record['pArtistName'];
	    $track = $record['pSongTitle'];
	    //$time = strtotime($record['pDTS']);
		$label = $record['pRecordLabel'];
		$album = $record['pAlbumTitle'];
		$nowP = $record['pCurrentlyPlaying'];
*/	
		/**
		echo "<entry>\n";
		echo "<id>" . $pID . "</id>\n";
		echo "<albnum>" . $albnum . "</albnum>\n";
		echo "<trknum>" . $trknum . "</trknum>\n";
		echo "<numinshow>" . $num . "</numinshow>\n";
		echo "<nowplaying>" . $nowP . "</nowplaying>\n";
		echo "<rotation>" . $rot . "</rotation>\n";
		echo "<track>" . htmlspecialchars($track) . "</track>\n";
		echo "<album>" . htmlspecialchars($album) . "</album>\n";
		echo "<artist>" . htmlspecialchars($artist) . "</artist>\n";
		echo "<label>" . htmlspecialchars($label) . "</label>\n";
		echo "</entry>";
		**/
		?>
		<tr>
			<td><img <?php if(!$nowP) echo "class='gray' alt='not playing' "; else echo "alt='playing!' " ?> 
				src='next32.png' onclick='nowPlaying(<?php echo $pID; ?>)' /></td>
			<td><?php echo $albnum; ?></td>
			<td><?php echo $trknum; ?></td>
			<td><?php echo $rot; ?></td>
			<td><?php echo $track; ?></td>
			<td><?php echo $artist; ?></td>
			<td><?php echo $album; ?></td>
			<td><?php echo $label; ?></td>
		</tr>
	
	
		<?php
	}
	/** echo "</playlist>"; **/
	?>
	
	</table>

<!--</div>
</body></html>-->