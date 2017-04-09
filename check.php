<?php

require("connect.php");

class Track {
   var $title;
   var $num;
   var $tID;
   var $recc;
   var $noAir;
}

class Album {
   var $cID;
   var $cNo;
   var $title;

   var $artist;
   var $aID;

   var $label;
   var $lID;

   var $genre;

   var $review;
   var $reviewer;

   var $tracks = array();

   public function info() {
      $info = "";
      $info = $info . $this->title . "<br/>";
      $info = $info . $this->artist . "<br/>";
      $info = $info . $this->label . "<br/>";
      $info = $info . $this->genre . "<br/>";

      foreach($this->tracks as $track) {
         $info = $info . $track->title . "<br/>";
      }

      return $info;
   }

   public function update() {
      $aID = artistCheck($this->artist);
      if(!$aID) $aID = insertArtist($this->artist);

//echo "<pre>" . $this->info() . "\n\n\n";
//echo "artist ID $aID\n";
//die();
	  // modified, ztm, 1oct09
      $lID = labelCheck($this->label);
      if(!$lID) $lID = insertLabel($this->label);
//echo "label ID $lID\n";
//die();
	  //modified, ztm, 1oct09
	  //returns primary key, NOT A000-type key
      $cID = albumCheck($this->title, $aID);
//echo "cd ID $cID\n";
//die();	
      if(!$cID) {
         $this->cNo = getNewAlbumNo();
         $cID = insertAlbum($this->title, $this->cNo, $aID, $lID, $this->genre, $this->review, $this->reviewer);
      }

      foreach($this->tracks as $track) {
         insertTrack($track->title, $cID, $track->num, !$track->noAir, $track->recc);
      }
   }
}

//*Sets to lower case and strips all non alphanumeric characters
function cleanName($name) {
   $clean = strtolower($name);
   $clean = str_replace (" ", "", $clean);
   $clean = ereg_replace("[^a-z0-9]", "", $clean);
   //ztm added the addslashes on 21 sept 09, to fix bug in addTrack
   $clean = addslashes($clean);
   return $clean;
}

//*Checks to see if an artist is already in the database
function artistCheck($name) {
   $query = "SELECT * FROM `libartist` WHERE `aPrettyArtistName` REGEXP '$name' LIMIT 1";
   $result = mysql_query($query) or die("Query failed : " . mysql_error());
   $artist = mysql_fetch_array($result);
   if ($artist) return $artist['aID'];
}

//*Inserts artist into database, and returns an ID
function insertArtist($name) {
   $clean = cleanName($name);
   $query = "INSERT INTO `libartist` (aPrettyArtistName,aCmpArtistName) VALUES('$name','$clean')";
   mysql_query($query);
   return mysql_insert_id();
}

//*Creates a new readable album number. Ex: "J027"
function getNewAlbumNo() {
   $query = "SELECT * FROM `libcd` ORDER BY `libcd`.`cAlbumNo` DESC LIMIT 1";
   $result = mysql_query($query) or die("Query failed : " . mysql_error());
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

/** 10 Jan 2010 - this function's prototype is updated **/
/** checking artist ID is necessary, as album name is NOT a primary key **/

//*Checks to see if an album is already in the database
function albumCheck($name, $artistID) {
   //this (used to) break on album "Rules" because "Yesterday Rules" and "Rules are Predictable" already existed
   //$query = "SELECT * FROM `libcd` WHERE `cAlbumName` REGEXP '$name' LIMIT 1";
   //$query = "SELECT * FROM libcd WHERE cAlbumName='$name' LIMIT 1";
   $query = "SELECT * FROM libcd WHERE cAlbumName='$name' AND c_aID='$artistID' LIMIT 1";
	//echo $query;
   $result = mysql_query($query) or die("Query failed : " . mysql_error());
   $album = mysql_fetch_array($result);
   if ($album) return $album['cID'];
   else return false;
}

//*Inserts album into database, and returns an ID
function insertAlbum($name, $albumNo, $aID, $lID, $genre, $review, $reviewer) {
   $clean = cleanName($name);
   $query = "INSERT INTO `libcd` (cAlbumName,c_aID,c_lID,cGenre,cAlbumNo,cReview,cReviewer) VALUES('$name','$aID','$lID','$genre','$albumNo','$review','$reviewer')";
   mysql_query($query);
   return mysql_insert_id();
}

//*Checks to see if a label is already in the database
function labelCheck($name) {
	if(strlen($name) < 1)
		return -1;

   $query = "SELECT * FROM `liblabel` WHERE `lPrettyLabelName` REGEXP '$name' LIMIT 1";
   $result = mysql_query($query) or die("BBBQuery failed : " . mysql_error());
   $label = mysql_fetch_array($result);
   // prior to 1 oct 2009 lID was only t or f. now it returns the ACTUAL lID no matter what
   if ($label) return $label["lID"]; //used to return true
   else return false;
}

//*Inserts label into database, and returns an ID
function insertLabel($name) {
   $clean = cleanName($name);
   $query = "INSERT INTO `liblabel` (lPrettyLabelName,lCmpLabelName) VALUES('$name','$clean')";
   mysql_query($query);
   return mysql_insert_id();
}

// Inserts track into database, and returns an ID
function insertTrack($name, $cID, $no, $clean, $recc) {
   
   if ($clean) $clean = 1;
   else $clean = 0;
   if ($recc) $recc = 1;
   else $recc = 0;

   $query = "INSERT INTO `libtrack` (tTrackName,t_cID,tTrackNo,tClean,tRecc) VALUES('$name','$cID','$no','$clean','$recc')";
   mysql_query($query);
   return mysql_insert_id();
}



?>
