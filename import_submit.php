<?php
$delete = FALSE; // default state - don't fuck up drs by deleting tracks


$time = microtime(true);
require_once("conn.php");
require_once("utils_ccl.php");
require_once("review_lib.php");
//require_once("dbupdate/getid3/getid3/getid3.php");

define('BASE_DIR', "E:\\DRSAUDIO");
define('BASE_ZAUTO', "E:\\ZAutoLib");
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
echo "<p>Go <a href='".urldecode($_POST['redirect'])."'>back</a>...</p>\n";
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
if($cID) echo "CD already exists: $cID<br>";
else {
	$cID = insertNewAlbum($album, $aID, $lID, $genre, $year);
	$delete = TRUE;
	echo "New CD ID: $cID<br>";
}
$actionData['cID'] = $cID;

if($delete)
	echo "Delete mode is turned on.<br>";
else
	echo "Delete mode is turned off - old files will be preserved.<br>";
echo "Moving files and updating track table...<br>";



$ctr = 1; /** $ctr is an iterator, not the ACTUAL track number, which is a POSTed *_trNum **/
while(isset($_POST[$ctr.'_trnum'])) {
	$trNum = $_POST[$ctr.'_trnum'];
	$trName = urldecode($_POST[$ctr.'_trname']);
	$trFile = urldecode($_POST[$ctr.'_trfile']);
	
	$pcs = explode("\\", $trFile);
	$filename = $pcs[count($pcs)-1];
	
	
	/** file moving **/
		//this change made by ztm 1nov10. see review_lib.php documentation
		//$dirMake = cleanName($artist);
		$dirMake = directoryName($artist);
	$newPath = BASE_ZAUTO."\\";
	if(!file_exists( $newPath.$dirMake[0] ))
		mkdir( $newPath.$dirMake[0] );
	$newPath .= $dirMake[0] . "\\";
	if(!file_exists( $newPath.$dirMake[1] ))
		mkdir( $newPath.$dirMake[1] );
	$newPath .= $dirMake[1] . "\\";
	$newPath .= $filename;
	
	/** FOR WRITING TO DATABASE ONLY **/
	$dirDB = $dirMake[0].$dirMake[1].$filename;
	
	if(!copy($trFile, $newPath))
		die("Could not copy: $trFile to $newPath");
	//echo "Moved from: $trFile <br>Moved to: $newPath<br>";
	//echo "Copying to ZAutomate... ";
	
	if($delete) {
		if(!unlink($trFile))
			die("Could not delete: $trFile");
	}
	else {
		$rs = fopen("import_todelete.txt", 'a');
		fwrite($rs, $trFile."\n");
		fclose($rs);
	}
	//echo "Deleted: $trFile<br>";
	//echo "Deleting original... ";
	//echo "Success!<br>";
	
	/** libtrack modifying **/
	$qu = "SELECT * FROM libtrack WHERE t_cID='$cID' AND tTrackNo='$trNum'";
	$rs = mysql_query($qu);
	$tID = -1;
	if(mysql_num_rows($rs) == 1) {
		//have a valid track to update
		$row = mysql_fetch_array($rs, MYSQL_ASSOC);
		$tID = $row['tID'];
		$qu2 = "UPDATE libtrack SET tTrackName='$trName', tFileName='".urlencode($dirDB)."' WHERE tID='$tID'"; //urlencode($newPath)
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