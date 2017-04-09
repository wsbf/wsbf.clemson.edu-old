<?php

//Accessed via AJAX call from logging software
//
//Adds typed-in 'optional' songs to DB
//Zach Musgrave, WSBF-FM Clemson, Jan 2010

/** TO DO **/
	// AT PRESENT: cBin codes are IGNORED. incoming GET rotation is ALWAYS used.
	//auto-currently playing is turned off
	//use recc & noair codes for something

include("../connect.php");

//$genre = mysql_real_escape_string($_GET['genre']);
	$genre = "";
$track = mysql_real_escape_string($_GET['track']);
$album = mysql_real_escape_string($_GET['album']);
$label = mysql_real_escape_string($_GET['label']);
$artist = mysql_real_escape_string($_GET['artist']);


$showid = mysql_real_escape_string($_GET['showid']);

/**$rotation = mysql_real_escape_string($_GET['rotation']);
if($rotation == "-1")
	$rotation = "O";
**/

$albumno = mysql_real_escape_string($_GET['albumno']);
$trackno = mysql_real_escape_string($_GET['trackno']);


//calculate pNumInShow. initialization only here.
$numinshow = 0;

//field names for lbplaylist
//	pID 	p_sID		pDTS		pNumInShow	pAlbumNo	pTrackNo	
//	pGenre	pRotation	pArtistName	pSongTitle	pAlbumTitle	pRecordLabel	pCurrentlyPlaying

if(isset($_GET['showid'])) {	
	
	
	//calculate $numinshow
	$rsc0 = mysql_query("SELECT * FROM lbplaylist WHERE p_sID='$showid' ORDER BY pNumInShow DESC LIMIT 1");
	
	if(mysql_num_rows($rsc0) == 1) {
		$row0 = mysql_fetch_array($rsc0, MYSQL_ASSOC);
		$numinshow = $row0['pNumInShow'];
		$numinshow++;
	}
	else {
		$numinshow = 1;
	}
	
	
	
	
	//try to get data based on albumno
	//if there is NOT 1 record, then treat as optional.
	$rsc = mysql_query("SELECT * FROM libcd WHERE cAlbumNo='$albumno'");
	if(mysql_num_rows($rsc) == 1) {
		$row = mysql_fetch_array($rsc, MYSQL_ASSOC);
		//field names for libcd
		//	cID	c_lID	c_aID	cAlbumName	cAlbumNo	cYear	cPromoter	cReview	cReviewer	cGenre	cBin
		$cID = $row['cID'];
		$lID = $row['c_lID'];
		$aID = $row['c_aID'];
		
		$genre = mysql_real_escape_string($row['cGenre']);
		$album = mysql_real_escape_string($row['cAlbumName']);
		
		$rotation = $row['cBin'];
		if($row['cBin'] == "")
			$rotation = "O";
		
		
		//try based on artist
		$rsc2 = mysql_query("SELECT * FROM libartist WHERE aID='$aID'");
		if(mysql_num_rows($rsc2) == 1) {
			$row2 = mysql_fetch_array($rsc2, MYSQL_ASSOC);
			$artist = mysql_real_escape_string($row2['aPrettyArtistName']);
		}
		//field names for libartist
		//	aID	aPrettyArtistName	aCmpArtistName
		
		
		//now try based on trackno too
		$rsc3 = mysql_query("SELECT * FROM libtrack WHERE t_cID='$cID' AND tTrackNo='$trackno'");
		if(mysql_num_rows($rsc3) == 1) {
			$row3 = mysql_fetch_array($rsc3, MYSQL_ASSOC);
			$track = mysql_real_escape_string($row3['tTrackName']);
		}
		//field names for libtrack
		//	tID	t_cID	tDisc	tTrackNo	tTrackName	tClean	tRecc
		
		//finally, try based on label
		$rsc4 = mysql_query("SELECT * FROM liblabel WHERE lID='$lID'");
		if(mysql_num_rows($rsc4) == 1) {
			$row4 = mysql_fetch_array($rsc4, MYSQL_ASSOC);
			$label = mysql_real_escape_string($row4['lPrettyLabelName']);
		}
		
		
		
	}
	
	//field names for lbplaylist
	//	pID 	p_sID		pDTS		pNumInShow	pAlbumNo	pTrackNo	
	//	pGenre	pRotation	pArtistName	pSongTitle	pAlbumTitle	pRecordLabel	pCurrentlyPlaying
	//do NOT write pID (auto_inc) or pDTS (curr timestamp)

	$query = "INSERT INTO lbplaylist (p_sID, pNumInShow, pAlbumNo, pTrackNo, 
		pGenre, pRotation, pArtistName,pSongTitle,pAlbumTitle,pRecordLabel,pCurrentlyPlaying)";

	$query .= " VALUES('$showid', '$numinshow', '$albumno', '$trackno',  
	'$genre', '$rotation', '$artist','$track','$album','$label',0)";
//echo $query;
	mysql_query($query) or die("Query failed : " . mysql_error());
	echo "success";
	
	
}
?>