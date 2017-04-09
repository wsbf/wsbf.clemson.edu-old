<?php
require_once("stream_conn.php");
error_reporting (E_ALL ^ E_NOTICE); //undefined indices in big array
define(SECS_WEEK, 604800);
define(SECS_DAY, 86400);


/** ------------------------------------------------ **/

class Album {
	var $album, $artist, $label, $count, $rank;
	
	function Album($al, $ar, $la) {
		$this->album = $al;
		$this->artist = $ar;
		$this->label = $la;
		$this->count = 1;
		$this->rank = NULL;
	}
	function again() { $this->count++; }
	function getCount() { return $this->count; }
	function getArtist() { return $this->artist; }
	function setRank($rank) { $this->rank = $rank; }
	function getRank() { return $this->rank; }
	function genRank($old) { 
		if( $this->rank !== NULL ) {
			return($old - $this->rank);
		}
		else return NULL;
	}
	function printF() {
		return array($this->rank, $this->count, $this->artist, $this->album, $this->label);
		//echo "<td>".$this->rank."</td><td>".$this->count."</td><td>".$this->artist."</td><td>".
		//	$this->album."</td><td>".$this->label."</td>";
	}
}
function compare($a, $b) {
	if($a->getCount() == $b->getCount())
		return ($a->getArtist() < $b->getArtist()) ? -1 : 1;
	return ($a->getCount() < $b->getCount()) ? 1 : -1;
}
function validAlbumNo($ano) {
	$pattern = '/^[A-Z]{1}[0-9]{3}/';
	return (preg_match($pattern, $ano));
	
}

/** ------------------------------------------------ **/

if(!isset($_GET['date']))
	$now = time();
else $now = strtotime($_GET['date']);

$endlimit = strtotime( date("Y-m-d", $now) ); //Sunday to (end of) Saturday
while(date("w", $endlimit) != 6)
	$endlimit -= SECS_DAY;

$startlimit = $endlimit - SECS_WEEK;
$twobeforelimit = $startlimit - SECS_WEEK;

/** ------------------------------------------------ **/

$albums = array();
$albums_old = array();

$plays_old = array(); /** by CODE, # plays for each album **/
$ranks_old = array();

/** ------------------------------------------------ **/

echo "<h2 style='text-align:center'>Albums charting from <br><b>";
echo date("l, F j, Y", $startlimit) . "</b> until <b>";
echo date("l, F j, Y", $endlimit) . "</b></h2>";

echo "<p><a href='?date=".date('Y-m-d', $startlimit)."'>Last week</a>";
if( SECS_WEEK < (time()-$endlimit) )
	echo " | <a href='?date=".date('Y-m-d', ($endlimit+SECS_WEEK) )."'>Next Week</a>";
echo "</p>";
//"Y-m-d H:i:s"

/*
$query = "SELECT * FROM lbplaylist WHERE pDTS < '".date("Y-m-d H:i:s", $endlimit).
			"' AND pDTS > '".date("Y-m-d H:i:s", $startlimit)."' AND lb_album_code != '' ORDER BY pID DESC";
						
$query2 = "SELECT * FROM lbplaylist WHERE pDTS < '".date("Y-m-d H:i:s", $startlimit).
			"' AND pDTS > '".date("Y-m-d H:i:s", $twobeforelimit)."' AND lb_album_code != '' ORDER BY pID DESC";
**********************

$query = sprintf("SELECT logbookID, logbook.lb_album_code, logbook.lb_album, logbook.lb_label, logbook.lb_artist FROM `logbook`, `show` WHERE show.end_time < '%s' AND show.start_time > '%s' AND logbook.lb_album_code != '' ORDER BY logbook.logbookID DESC", date("Y-m-d H:i:s", $endlimit), date("Y-m-d H:i:s", $startlimit));
echo $query;
$query2 = sprintf("SELECT logbookID, logbook.lb_album_code, logbook.lb_album, logbook.lb_label, logbook.lb_artist FROM `logbook`, `show` WHERE show.end_time < '%s' AND show.start_time > '%s' AND logbook.lb_album_code != '' ORDER BY logbook.logbookID DESC", date("Y-m-d H:i:s", $startlimit), date("Y-m-d H:i:s", $twobeforelimit));

**********************
*/
$query = sprintf("SELECT logbook.logbookID, logbook.lb_album_code, logbook.lb_album, logbook.lb_artist, logbook.lb_label FROM `logbook`, `show` 
	WHERE logbook.showID = show.showID AND show.end_time < '%s' AND show.start_time > '%s' 
	AND lb_album_code != ''
	ORDER BY logbookID DESC", date("Y-m-d H:i:s", $endlimit), date("Y-m-d H:i:s", $startlimit));

$query2 = sprintf("SELECT logbook.logbookID, logbook.lb_album_code, logbook.lb_album, logbook.lb_artist, logbook.lb_label FROM `logbook`, `show` 
	WHERE logbook.showID = show.showID AND show.end_time < '%s' AND show.start_time > '%s' 
	AND lb_album_code != ''
	ORDER BY logbookID DESC", date("Y-m-d H:i:s", $startlimit), date("Y-m-d H:i:s", $twobeforelimit));
	

if( !( $rsc = mysql_query($query) ) ) die(mysql_error()); //CURRENT DATA
if( !( $rsc2 = mysql_query($query2) ) ) die(mysql_error()); //OLD DATA



/** Fill out plays_old for prior week **/
while($row = mysql_fetch_array($rsc2, MYSQL_ASSOC)) {
	if(validAlbumNo($row['lb_album_code'])) {
		if($plays_old[$row['lb_album_code']] === NULL)
			$plays_old[$row['lb_album_code']] = 1;
		else
			$plays_old[$row['lb_album_code']]++;
	}
}
arsort($plays_old);

/**
$index = 0;
$ = 1;
$level = 9999;
foreach($plays_old as $aid => $plays) {
	if($plays)
	
	$ranks_old[$aid] = $rank;
}
**/

/** Fill out albums for THIS week, they are objects **/
while($row = mysql_fetch_array($rsc, MYSQL_ASSOC)) {
	if(validAlbumNo($row['lb_album_code'])) {
		if($albums[$row['lb_album_code']] === NULL)
			$albums[$row['lb_album_code']] = new Album($row['lb_album'], $row['lb_artist'], $row['lb_label']);
		else
			$albums[$row['lb_album_code']]->again();	
			
		//if($row['lb_album_code'] == 'C815') echo "annuals\n";
	}
}

uasort($albums, 'compare');

//echo "<pre>"; print_r($plays_old); die();

?><table border='1'><tr><th>Code</th><th>Change</th><th>Rank</th><th>No.</th>

<?php if(!isset($noplays))
		echo "<th>Plays</th>";
?>
		<th>Artist</th><th>Album</th><th>Label</th></tr>
<?php

$dammit_peter = 0;
$i = 1;
$rank_temp = 1;
foreach($albums as $aid => $album) {
	$dammit_peter++;
	if(isset($limit))
		if($i == $limit)
			break;
		
	if($rank_temp == $album->getCount() && $i != 1)
		$rank = "-";
	else
		$rank = $i;
	
	
	if($plays_old[$aid] === NULL)
		$change = "NEW!";
	else 
		$change = $album->getCount() - $plays_old[$aid];
		
	
	
	$stuff = $album->printF(); //rank count artist album label
	
	echo "<tr>";
	echo "<td>$aid</td>";
	
	echo "<td>"; //change
	if($change == 0) 
		echo "-";
	else if($change > 0)
		echo "<img src='http://wsbf.net/i/arrow_up.png' />&nbsp;$change";
	else 
		echo "<img src='http://wsbf.net/i/arrow_down.png' />&nbsp;".($change*-1);
	echo "</td>"; 
	
	echo "<td>$rank</td>"; //rank
	
	echo "<td>$dammit_peter</td>";
	
	if(!isset($noplays))
		echo "<td>$stuff[1]</td>";
	echo "<td>$stuff[2]</td><td>$stuff[3]</td><td>$stuff[4]</td>";
	echo "</tr>";
	
	$i++;
	$rank_temp = $album->getCount();
}

?>