<?php
$time = microtime(true);
require_once("conn.php");
require_once("utils_ccl.php");
require_once("review_lib.php");

define('SCRIPT_PREFIX', "http://wsbf.net/wizbif/");
define('BIN_CODE', 'T'); //To Be Reviewed
define('LABEL_DUMMY', 1899);

sanitizeInput();
if(session_id() == "") session_start();

global $user;
if(!isset($user)) $user = "zachm";
else $user = $user->name;

//dac
echo "<h1>PRELIMINARY IMPORT SYSTEM</h1>\n";
echo "<h3>Import Submission/Confirmation</h3>\n";
echo "<p>Import another <a href='import_record.php'>record</a>...</p>\n";
echo "<p>Go back to <a href='import_main.php'>main</a>...</p>\n";
echo "<div id='contents'>";


$artist = trim($_POST['artist']);
$album = trim($_POST['album']);
$label = trim($_POST['label']);
$genre = trim($_POST['genre']);
$year = trim($_POST['year']);

if($artist == "" || $album == "")
	die("Please fill in artist and album!");

//echo "<pre>".print_r($_POST, TRUE)."</pre>";
$actionData = array();

if($label != ""){
	$lID = labelCheck($label);
	if($lID) echo "Label already exists: $lID<br>";
	else {
		$lID = insertLabel($label);
		echo "New label ID: $lID<br>";
	}	
} else $lID = LABEL_DUMMY;
$actionData['lID'] = $lID;

$aID = artistCheck($artist);
if($aID) echo "Artist already exists: $aID<br>";
else {
	$aID = insertArtist($artist);
	echo "New artist ID: $aID<br>";
}
$actionData['aID'] = $aID;

$cID = albumCheck($album, $aID);
if($cID) echo "Album already exists: $cID<br>";
else {
	$cID = insertNewAlbum($album, $aID, $lID, $genre, $year);
	echo "New Album ID: $cID<br>";
}
$actionData['cID'] = $cID;

echo "Updating track table...<br>";

$ctr = 1; /** $ctr is an iterator, not the ACTUAL track number, which is a POSTed *_trNum **/
while(isset($_POST[$ctr.'_trnum'])) {
	$trNum = $_POST[$ctr.'_trnum'];
	$trName = urldecode($_POST[$ctr.'_trname']);
	
		/** libtrack modifying **/
	$qu = "SELECT * FROM libtrack WHERE t_cID='$cID' AND tTrackNo='$trNum'";
	$rs = mysql_query($qu);
	$tID = -1;
	if(mysql_num_rows($rs) == 1) {
		//have a valid track to update
		$row = mysql_fetch_array($rs, MYSQL_ASSOC);
		$tID = $row['tID'];
		$qu2 = "UPDATE libtrack SET tTrackName='$trName'WHERE tID='$tID'"; //urlencode($newPath)
		mysql_query($qu2);
		//if(mysql_affected_rows() != 1)
		//	die("dead on qu2: ".mysql_error());
		echo "Track updated, already exists: $tID<br>";
	}
	elseif(mysql_num_rows($rs) == 0) {
		$tID = insertNewTrack($trName, $cID, $trNum, urlencode($dirDB)); //urlencode($newPath)
		echo "New track ID: $tID<br>";
	}
	else { die("libtrack rows not one or zero!"); }
	$actionData['tID'][] = $tID;
	
	//echo "<br>";
	$ctr++;
}

addAction($user, 'libcd', $cID, 'IMPORT', serialize($actionData));


echo "Complete: All operations finished!<br>";
//echo "<pre>".print_r($_POST, TRUE)."</pre>";

echo "</div>";
$netTime = microtime(true)-$time;
echo "<p>Time needed to execute: ".round($netTime,5)." seconds\n</p>";


?>