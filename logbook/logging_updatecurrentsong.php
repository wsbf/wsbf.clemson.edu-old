<?php

//Accessed via AJAX call from logging software
//Sets currently playing song using primary key in lbplaylist
//Zach Musgrave, WSBF-FM Clemson, Oct 2009

require_once("../connect.php");
if(!isset($_GET['pid']))
	die();
if(!get_magic_quotes_gpc())
	$_GET['pid'] = addslashes($_GET['pid']);

$q = "UPDATE lbplaylist SET pCurrentlyPlaying=0 WHERE pCurrentlyPlaying=1";
mysql_query($q);

/**
$q = "SELECT pID FROM lbplaylist WHERE pCurrentlyPlaying='1'";
$rs = mysql_query($q);
if(mysql_num_rows($rs) > 0) {
	while($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
		$q = "UPDATE lbplaylist SET pCurrentlyPlaying='0' WHERE pID='".$row['pID']."'";
	}
}
**/

$q = "UPDATE lbplaylist SET pCurrentlyPlaying='1' WHERE pID='".$_GET['pid']."'";
mysql_query($q);

//write artist and songname to textfiles so they can be accessed by Kevin's RBDS program
$query = "SELECT * FROM lbplaylist WHERE pCurrentlyPlaying='1' LIMIT 1";
$result = mysql_query($query);


$row = mysql_fetch_array($result, MYSQL_ASSOC);
$artist = $row['pArtistName'];
$song = $row['pSongTitle'];

/*
$segment_id= 881;
// You have to attach to the shared memory segment first
$shm = shm_attach($segment_id,PHP_INT_SIZE,0600);
$date = time();
shm_put_var($shm,1,$date);
include("../rds_sender.php");
rdssend($song,$artist,$date);
*/

$fa = fopen('current_artist.txt', 'w');
fwrite($fa, $artist);
fclose($fa);

$fs = fopen('current_song.txt', 'w');
fwrite($fs, $song);
fclose($fs);

include("../rds_sender_test.php");

rdssend($song,$artist);

mysql_close();
?>