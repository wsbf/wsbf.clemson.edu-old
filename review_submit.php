<?php
/**
THIS FILE IS USED FOR WRITING REVIEWS. FOR MODIFYING REVIEWS, SEE REVIEW_BACKEND.PHP
**/
require_once("conn.php");
require_once("utils_ccl.php");
require_once("review_lib.php");
sanitizeInput();


$error = "";
$uris = genUriStruct($_SERVER['HTTP_REFERER']);

global $user; if(isset($user)) $uname = $user->name; 
else $error .= "<br>No user name specified!";

if(isset($_POST['cdid'])) {
	$cdid = $_POST['cdid'];
	$uris = updateUriStruct($uris, "cdid", $cdid);
}
else $error .= "<br>No CD ID!";


if($error == "") {
/** beginning of old case INSERT statement **/
	
	//drupal mode insert stuff
	$msg = array();
	
	if(strlen($_POST['artist']) < 1) $error .= "<br>Enter an artist name.";
	if(strlen($_POST['album']) < 1) $error .= "<br>Enter an album name.";
	if(strlen($_POST['label']) < 1 && $_POST["Self"] != "Self")
		$error .= "<br>Enter a label name, or click 'self-released'.";
	if(strlen($_POST['label']) < 1 && $_POST["Self"] == "Self")
		$_POST["label"] = $_POST["Self"];
	if(strlen($_POST['genre']) < 1) $error .= "<br>Enter a genre, or multiple genres.";
	if(strlen($_POST['reviewer']) < 1) $error .= "<br>Enter your name as the reviewer.";

	$m = $_POST['maxtrack'];
	$ctr = 0;
	for($i = 1; $i <= $m; $i++){
		$recc = $_POST["recc$i"];
		$ctr = $ctr + $recc;
	}
	if($ctr == 0)
		$error .= "<br />You haven't selected any recommended tracks. Please select the most outstanding tracks.";
	
	if(strlen($_POST['review']) < 1) $error .= "<br>Enter a review. Asshat.";
	
	
	if($error != "") {
		$_SESSION['errorMessage'] = "Error(s):".$error;
		header("Location: ".useUriStruct($uris));
	}

	$artist = $_POST['artist'];
	$album = $_POST['album'];
	$label = $_POST['label'];
	$genre = $_POST['genre'];
	$review = $_POST['review'];
	$reviewer = $_POST['reviewer'];
	
	$cID = $_POST['cdid'];
	
	/**
	echo "<pre>";
	echo $uname."\n";
	print_r($uri);
	print_r($_POST);
	echo "</pre>";
	die();
	**/
	
	$lID = labelCheck($label);
	if($lID) $msg[] = "Label already exists: $lID";
	else {
		$lID = insertLabel($label);
		$msg[] = "New label ID: $lID";
	}
	
	$aID = artistCheck($artist);
	if($aID) $msg[] = "Artist already exists: $aID";
	else {
		$aID = insertArtist($artist);
		$msg[] = "New artist ID: $aID";
	}
	
	$cdcode = getNewAlbumNo();
	if(!reviewAlbum($cID,$aID,$lID, $album, $cdcode, $review, $reviewer, $genre))
		die("ERROR: reviewAlbum failed!");
	
	$ctr = 1;
	while(isset($_POST['track'.$ctr])) {
		
		$name = $_POST['track'.$ctr];
		$recc = $_POST['recc'.$ctr];
		$noair = $_POST['noair'.$ctr];
		if(!reviewTrack($cID, $ctr, $name, $recc, $noair)) 
			die("ERROR: reviewTrack failed!");
		
		$msg[] = "Track updated: $ctr";
		$ctr++;
	}
	
	$rs = mysql_query("SELECT * FROM libcd WHERE cID='$cID'") or die(mysql_error());
//TODO: make sure addslashes() call is documented
	$data = addslashes(serialize(mysql_fetch_array($rs, MYSQL_ASSOC)));
	addAction($uname, 'libcd', $cID, "WRITE", $data);
	
	//get here? SUCCESS
	echo "<h2>Review Confirmation</h2>";
	echo "<p>$album by $artist ($cdcode) was reviewed successfully by user $uname.</p>";
	echo "<p>".implode("<br>", $msg)."</p>";
	
/** end of old case INSERT statement **/
} print "<p>$error</p>";

?>