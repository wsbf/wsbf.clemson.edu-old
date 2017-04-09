<?php

require_once('conn.php');

function addAction($user, $tableName, $priKey, $type, $data) {
	$qu = "INSERT INTO libaction (actUser, act_tableName, act_priKey, actType, actData) 
		VALUES ('$user', '$tableName', '$priKey', '$type', '$data')";
	mysql_query($qu) or die("\nlibaction update failed: ".mysql_error());

}



// mostly reused from check.php

//DSC: Label already exists? ID or FALSE
//BUG: Should this be LIMIT 1? Or should multiples prompt the user?
function labelCheck($name) {
	$name = cleanName($name);
   //$query = "SELECT * FROM `liblabel` WHERE `lPrettyLabelName` REGEXP '$name' LIMIT 1";
	$query = "SELECT * FROM liblabel WHERE lCmpLabelName='$name'";
//echo $query;
   $result = mysql_query($query);// or die("labelCheck failed: " . mysql_error());
	if(mysql_num_rows($result) == 0) 
		return FALSE;
	else if(mysql_num_rows($result) > 1)
		die("labelCheck failed: not 1 entry!");
	else {
		$label = mysql_fetch_array($result);
		if ($label) 
			return $label["lID"];
		else 
			return FALSE;
	}
   
}

//DSC: Add a new label? ID
function insertLabel($name) {
   $clean = cleanName($name);
   $query = "INSERT INTO `liblabel` (lPrettyLabelName,lCmpLabelName) VALUES('$name','$clean')";
   mysql_query($query);
   return mysql_insert_id();
}


//DSC: Artist exists? ID or FALSE
function artistCheck($name) {
	$name = cleanName($name);
	//$query = "SELECT * FROM `libartist` WHERE `aPrettyArtistName` REGEXP '$name' LIMIT 1";
	$query = "SELECT * FROM libartist WHERE aCmpArtistName='$name'";	
	$result = mysql_query($query); //or die("artistCheck failed : " . mysql_error());
	if(mysql_num_rows($result) > 1) die("artistCheck failed: >1 entry!");
	if(mysql_num_rows($result) == 0) 
		return FALSE;
	$artist = mysql_fetch_array($result);
	if($artist) return $artist['aID'];
	else return FALSE;
}

//DSC: Add a new artist? ID
function insertArtist($name) {
   $clean = cleanName($name);
   $query = "INSERT INTO `libartist` (aPrettyArtistName,aCmpArtistName) VALUES('$name','$clean')";
   mysql_query($query);
   return mysql_insert_id();
}




//DSC: Album by artistID already exists? ID or FALSE
//BUG: May be unnecessary / LIMIT 1? / 
function albumCheck($name, $artistID) {
	if(strlen($name) < 1 || !is_numeric($artistID)) return FALSE;
//cAlbumName REGEXP '$name'
/** "Hurricane"/"Hurricane Dub" bug. Changed "LIKE '%$name%'" to "LIKE '$name'". ZTM, 26 July 2011 **/
	$query = "SELECT * FROM libcd WHERE cAlbumName LIKE '$name' AND c_aID='$artistID'";
//echo $query."<br>";
	$result = mysql_query($query);
	if(mysql_num_rows($result) > 1) die("albumCheck failed: >1 entry!");
	if(mysql_num_rows($result) == 0) return FALSE;
	$album = mysql_fetch_array($result, MYSQL_ASSOC);
	if($album) return $album['cID'];
	else return FALSE;
}


//DSC: Creates a new readable album number. Ex: "J027"
function getNewAlbumNo() {
   //$query = "SELECT * FROM `libcd` ORDER BY `libcd`.`cAlbumNo` DESC LIMIT 1";
$query = "SELECT * FROM `libcd` WHERE cAlbumNo !='' ORDER BY `libcd`.`cAlbumNo` DESC LIMIT 1";
   $result = mysql_query($query) or die("getNewAlbumNo failed : " . mysql_error());
   $album = mysql_fetch_array($result);
   $latestAlbumNo = $album['cAlbumNo'];

   $charlength = strlen($latestAlbumNo) - 3;
   $alpha = substr($latestAlbumNo,0,$charlength);

   $num = intval(substr($latestAlbumNo, -3));

   if ($num == 999) {
      $num = 0;
      $alpha++;
   } else {
      $num++;
   }
   $num = str_pad($num, 3, "0", STR_PAD_LEFT);

   $newAlbumNo = $alpha . $num;
   return $newAlbumNo;
}

//DSC: Generates comparable names from pretty ones
function cleanName($name) {
   $clean = str_replace (" ", "", strtolower($name));
   $clean = ereg_replace("[^a-z0-9]", "", $clean);
   //ztm added the addslashes on 21 sept 09, to fix bug in addTrack
   //$clean = addslashes($clean);
	//ztm removed above line in summer 2010
   return $clean;
}

//DSC: Based on cleanName, but follows alphabetizing conventions.
//	at the moment, merely remove "the" from beginnings of artist names
//	newer features may be added later, so DO NOT rely on this (or cleanName)
//	to stat files for automation/etc - there is a reason the WHOLE filename is 
//	saved in libtrack!
//		function added by ztm on 1 Nov 2010
function directoryName($name) {
	$name = cleanName($name);
	if(strpos($name, 'the') === 0)
		$name = substr($name, 3); //start with 4th character
	return $name;
}


//DSC: Inserts album into database, and returns an ID
function insertAlbum($name, $albumNo, $aID, $lID, $genre, $review, $reviewer) {
   $clean = cleanName($name);
   $query = "INSERT INTO `libcd` (cAlbumName,c_aID,c_lID,cGenre,cAlbumNo,cReview,cReviewer,cBin) 
	VALUES('$name','$aID','$lID','$genre','$albumNo','$review','$reviewer','TBR')";
   mysql_query($query) or die("insertAlbum failed : ".mysql_error());
   return mysql_insert_id();
}

//DSC: Inserts track into database, and returns an ID
function insertTrack($name, $cID, $no, $clean, $recc) {
   
   if ($clean) $clean = 1;
   else $clean = 0;
   if ($recc) $recc = 1;
   else $recc = 0;

   $query = "INSERT INTO `libtrack` (tTrackName,t_cID,tTrackNo,tClean,tRecc) 
	VALUES('$name','$cID','$no','$clean','$recc')";
   mysql_query($query);
   return mysql_insert_id();
}


//DSC: Takes filename/path, returns boolean if is an MP3
//Used by: import_*.php
function isMP3($in) {
	if(strpos($in, ".mp3") !== FALSE)
		return TRUE;
	if(strpos($in, ".MP3") !== FALSE)
		return TRUE;
	return FALSE;
}

/** FOR USE BY import_submit.php **/

//DSC: Inserts album into database, and returns an ID
function insertNewAlbum($name, $aID, $lID, $genre, $year) {
   $clean = cleanName($name);
   $query = "INSERT INTO `libcd` (c_lID,c_aID,cAlbumName,cYear,cGenre,cBin) 
	VALUES('$lID','$aID','$name','$year','$genre','T')";
   mysql_query($query) or die("insertNewAlbum failed : ".mysql_error());
   return mysql_insert_id();
}

//DSC: Inserts track into database, and returns an ID
function insertNewTrack($name, $cID, $no, $filename) {
	
   $query = "INSERT INTO `libtrack` (tTrackName,t_cID,tTrackNo,tFileName) 
	VALUES('$name','$cID','$no','$filename')";
   mysql_query($query) or die("insertNewTrack failed : ".mysql_error());;
   return mysql_insert_id();
}

/** FOR USE BY review_backend.php (mode INSERT) **/

function reviewAlbum($cID, $aID, $lID, $albumName, $cdcode, $review, $reviewer, $genre) {
	if(!is_numeric($cID)) return FALSE;
	if(!is_numeric($aID)) return FALSE;
	if(!is_numeric($lID)) return FALSE;
	
	$bin = "R";
	$query = "UPDATE libcd SET c_aID='$aID', c_lID='$lID', cAlbumName='$albumName', 
		cAlbumNo='$cdcode', cReview='$review', cReviewer='$reviewer', cGenre='$genre', cBin='$bin' 
		WHERE cID='$cID'";
	mysql_query($query) or die("reviewNewAlbum failed : ".mysql_error());
	return TRUE;
}

function reviewTrack($cID, $num, $name, $recc, $noair) {
	if(!is_numeric($cID)) return FALSE;
	if(!is_numeric($num)) return FALSE;
	$inClean = 0;
	$inRecc = 0;
	
	if($recc && $noair) return FALSE;
	if($recc && !$noair) {
		$inClean = 1;
		$inRecc = 1;
	}
	elseif(!$recc && !$noair) {
		$inClean = 1;
		$inRecc = 0;
	}
	elseif($noair) {
		$inClean = 0;
		$inRecc = 0;
	}
	
	$query = "UPDATE libtrack SET tTrackName='$name', tClean='$inClean', tRecc='$inRecc' WHERE t_cID='$cID' AND tTrackNo='$num'";
	mysql_query($query) or die("reviewNewTrack failed : ".mysql_error());
	return TRUE;
	
}

?>