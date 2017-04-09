<?php
// WSBF Current Playlist
// David Bowman - created 03-25-2004
// this php file shows the current play list for the dj on the air
// Heavily modified by Thomas Davidson

// Heavily modified (again) by Zach Musgrave, 5 Oct 2009
// Now serves as catalog of all recent playlists
// Moar useful, yo.
// Re-re-modified by David Cohen to add links to mp3 archives
// re-re-re-modified by David Cohen for the new database
require_once("stream_conn.php");

//echo "<html><head></head><body>";
//begin search for specific djs -dac

$altBG = array(
	0 => '#292929', 
	1 => '#404040'
);

$djlist = "SELECT * FROM `users` WHERE statusID = 0 ORDER BY preferred_name ASC";
$djquery = mysql_query($djlist) or die("MySQL Error near line " . __LINE__ . ": "  . mysql_error());

//$showlist = "SELECT * FROM shows";
//$qshow = mysql_query($showlist) or die(mysql_error());

if(!isset($_GET["showid"])) {
	//name selector (only shows up when no playlist is currently showing.)
	echo "<br /><form method='POST'><select name='djs'><option value='%'>Select a DJ</option>";
	while($res = mysql_fetch_assoc($djquery)){
		$name = $res['preferred_name'];
		$username = $res['username'];
		echo "<option value='$username'>$name</option>";
	}
	echo "</select>";
//	while($rshow = mysql_fetch_assoc($qshow)){
//		$showname = $rshow['show_name'];
//	}
	echo "<input type='submit' name='search' value='Get Playlists' /></form><br />";
	
	
	if(isset($_GET["page"]))
		$page = $_GET["page"];
	else $page = 1;
	
	if($page == 1) $prev = 1; else $prev = $page - 1;
	$next = $page + 1;
	//no ending case... script will just error
	$end = $page * 50;
	$start = $end - 50;
	
//chages query to get only the dj's playlists		
	if(isset($_POST['djs'])){
	$uname = $_POST['djs'];
//	$q = "SELECT * FROM lbshow WHERE sDJName LIKE '%$djname%' ORDER BY sStartTime DESC LIMIT $start, $end";
	$q = sprintf("SELECT show.showID, show.show_name, show.start_time, show.end_time, GROUP_CONCAT(users.preferred_name) AS names 
			FROM `show` 
			LEFT JOIN `show_hosts`
			ON show_hosts.showID = show.showID
       		INNER JOIN `users`
			ON users.username = '%s'
			AND show_hosts.username = users.username
			GROUP BY show.showID
		 	ORDER BY show.start_time DESC LIMIT %d, %d
	
	", $uname, $start, $end);
	}



//	elseif(isset($_POST['show_name'])){
//		$showname = $_POST['show_name'];
//		$q = "SELECT * FROM lbshow WHERE sShowName LIKE '$showname' ORDER BY sStartTime DESC LIMIT $start, $end";
//	}
	
	else {
	$q = sprintf("SELECT show.showID, show.show_name, show.start_time, show.end_time, GROUP_CONCAT(users.preferred_name) AS names 
			FROM `show` 
			LEFT JOIN `show_hosts`
			ON show_hosts.showID = show.showID
       		INNER JOIN `users`
			ON show_hosts.username = users.username
			GROUP BY show.showID
		 	ORDER BY show.start_time DESC LIMIT %d, %d", $start, $end);
	echo "<h2 style='text-align:center'>Fifty Most Recent Shows</h2>";
	}

	$rsc = mysql_query($q) or die("MySQL Error near line " . __LINE__ . ": "  . mysql_error());
	?>
	<table class="chart" width="100%"  border="0">
	<tr class='show'><td>DJ</td><td></td><td>Start Time</td><td>End Time</td></tr><?php
	$alt = 0;
	while($result = mysql_fetch_assoc($rsc)) {
		$sid = $result['showID'];
		$stime = strtotime($result['start_time']);
		$etime = strtotime($result['end_time']);
		$dj = $result['names'];
		$showname = $result['show_name'];
		$arclink = "archives?sid=".$sid; //dac
		$alt = 1-$alt;
			echo "<tr style=' background-color:".$altBG[$alt]."'>";

		echo "<td><b>$dj</b>";
		if(!empty($showname))
			echo "<br /><i>$showname</i>";
		echo"<br /></td>
		<td><a href='?showid=$sid'>View Playlist</a></td>";
//		if($etime != 0)
//			echo "<td><a href='$arclink'>Archive</td>";
//		else
//			echo "<td></td>";
		echo "<td>" . date("D, j M Y, g:i a",$stime);
//			"</td><td>" . date("l, M j Y, g:i a",$etime) . "</td></tr>\r\n";
		if($etime != 0)
			echo "</td><td>" . date("M j, g:i a",$etime) . "</td></tr>\r\n";
		else 
			echo "</td><td><a href='listen'>On Now!</a></td></tr>\r\n";
	}
	
	echo "</table>
	<p><a href='?page=$prev'>Newer...</a> | <a href='?page=$next'>Older...</a>";
}


else {
//	$nowinfo = getinfo();
//	$dj = $nowinfo[1];
//	$show = $nowinfo[2];
	
	$q = sprintf("SELECT * FROM `show`, `show_hosts` WHERE show.showID=%d AND show_hosts.showID = show.showID", $_GET['showid']);
	
	$rsc = mysql_query($q) or die("MySQL Error near line " . __LINE__ . ": "  . mysql_error());
	$result = mysql_fetch_array($rsc);
	$dj = $result[""];
	$stime = strtotime($result['start_time']);
	$showType = $result['type'];

	//$dbSQL = "SELECT * from lbplaylist WHERE p_sID = " . $maxPL['p_sid'].";";
	$dbSQL = sprintf("SELECT * from `logbook` WHERE showID=%d ORDER BY time_played ASC", $_GET["showid"]);
	$dbPL = mysql_query($dbSQL) or die("MySQL Error near line " . __LINE__ . ": "  . mysql_error());
if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/archpk/".$_GET['showid'].".mp3"))
	$archive = "http://wsbf.net/archpk/".$_GET['showid'].".mp3"; //dac
elseif(file_exists($_SERVER['DOCUMENT_ROOT'] . "/bc_china/archpk/".$_GET['showid'] . ".mp3"))
	$archive = "http://wsbf.net/bc_china/archpk/".$_GET['showid'] . ".mp3"; //dac
else $archive = "#";
	echo "<body><h2 style='text-align:center'>Playlist for $dj on " . date("l, M j Y", $stime) . "<br></h2><p><a href='$archive'>Download the MP3 Archive</a></p>"; //archive-link dac
	?>	
	 <p><a href='playlists'>Go Back</a></p>
	 <table class="chart" width="100%"  border="0">
	 <tr>
	  <td class="show"><p class="show">Artist</p></td>
	  <td class="show"><p class="show">Song</p></td>  
	  <td class="show"><p class="show">Album</p></td>
	  <td class="show"><p class="show">Label</p></td>
	  <td class="show"><p class="show">Rotation</p></td>
	  <td class="show"><p class="show">Time Played</p></td>
	 </tr>
<?php
	$alternator=0;

//numRot and count are for counting the rotation for seeing the ratio of rotation tracks to total (should be 75%)
	$numRot = 0;
	$count = 0;
   while($result = mysql_fetch_assoc($dbPL))
   {
// logbookID	showID	lb_album_code	lb_rotation	lb_track_num	lb_track_name	lb_artist	lb_album	lb_label	time_played	played	deleted
	 $Cartist= $result['lb_artist'];
	 $Csong=   $result['lb_track_name'];
	 $Calbum=  $result['lb_album'];
	 $Clabel=  $result['lb_label'];
	 $Crot=	   $result['lb_rotation'];
	 $Ctime=		$result['time_played'];
		// don't count underwritings
	if($Crot != 'UNDERWRITING' && $Crot != 'STATION' && $Crot == 'PROMOTION' && $Crot == 'PSA')
		$count++;
		
		// calculate rotation
	if($Crot == 'N' || $Crot == 'H' || $Crot == 'M' || $Crot == 'L' || $Crot == 'R')
		$numRot++;
	
		$alt = 1 - $alt;
		echo "<tr style=' background-color:".$altBG[$alt]."'>";

	echo "<td><a href='http://www.last.fm/music/$Cartist' target='_blank'>$Cartist</a></td>
		<td>$Csong</td><td>$Calbum</td><td>$Clabel</td><td>$Crot</td><td>$Ctime</td></tr>";
		
	  
	}
	$ratio = "nil";
	if($count > 0)
		$ratio = number_format(($numRot / $count),2) * 100 ."%";
	if($showType == 'Rotation')
	echo "<tr><td></td><td></td><td></td><td>Rotation:</td><td>$ratio</td></tr>";

	echo "</table>";

	//echo "</body></html>";
}
mysql_close();
?>
