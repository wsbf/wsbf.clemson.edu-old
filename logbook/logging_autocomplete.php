<?php

//Accessed via AJAX call from logging software
//Delivers XML-formatted data
//Auto-completion of manual input fields!
//Zach Musgrave, WSBF-FM Clemson, Jan 2010

//Modified 22 Aug 2010, relies on cBin in libcd now.

header("Content-type: application/xml");
include("../connect.php");
//include("drs.php");

$albno = null;
$trkno = null;
	$label = "";
	$artist = "";
	$album = "";
	$track = "";
	$clean = "";
	$recc = "";
$cdid = "";

if(!isset($_GET['albno']))
	die();
else {
	$albno = mysql_real_escape_string($_GET['albno']);
	
	$rsc1 = mysql_query("SELECT libcd.cID, liblabel.lPrettyLabelName, libartist.aPrettyArtistName, libcd.cAlbumName, libcd.cBin 
	FROM libcd, liblabel, libartist WHERE libcd.c_lID=liblabel.lID AND libcd.c_aID=libartist.aID AND 
	libcd.cAlbumNo='$albno' ORDER BY libcd.cID DESC LIMIT 1") or die(mysql_error());
	
	$show_record = mysql_fetch_array($rsc1);
	
	$cdid = $show_record['cID'];
	$label = htmlspecialchars($show_record['lPrettyLabelName']);
	$artist = htmlspecialchars($show_record['aPrettyArtistName']);
	$album = htmlspecialchars($show_record['cAlbumName']);
	
	$bin = htmlspecialchars($show_record['cBin']);
	if($bin == "")
		$bin = "O";
	
}

if(isset($_GET['trkno'])) {
	$trkno = mysql_real_escape_string($_GET['trkno']);
	
	$rsc2 = mysql_query("SELECT tTrackName, tClean, tRecc FROM libtrack WHERE t_cID='$cdid' 
		AND tTrackNo='$trkno' ORDER BY tID DESC LIMIT 1");
	$show_record = mysql_fetch_array($rsc2);
	$track = htmlspecialchars($show_record['tTrackName']);
	$clean = $show_record['tClean'];
	$recc = $show_record['tRecc'];
	
	
}


echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
echo "<autoinfo>\n";

	//echo "<id>" . $pID . "</id>\n";

	echo "<track>" . htmlspecialchars($track) . "</track>\n";
	echo "<album>" . htmlspecialchars($album) . "</album>\n";
	echo "<artist>" . htmlspecialchars($artist) . "</artist>\n";
	echo "<label>" . htmlspecialchars($label) . "</label>\n";
	echo "<bin>" . htmlspecialchars($bin) . "</bin>\n";
	echo "<clean>" . $clean . "</clean>\n";
	echo "<recc>" . $recc . "</recc>\n";

echo "</autoinfo>";
?>
