<?php

/**
	rotation_load_category.php - A RIPOFF OF THE BELOW
	called by rotation.php
	
	moverot_load_category.php by ztm, 8july10
	Takes a cBin code, and returns the contents in HTML format
	Called by moverot_main.php via JQuery/AJAX.

**/

require_once('conn.php');
require_once('review_lib.php');
require_once('utils_ccl.php');
sanitizeInput();


$categories = array("T"=>"To Be Reviewed",
					"R"=>"Recently Reviewed", 
					"N"=>"New Rotation", 
					"H"=>"Heavy Rotation", 
					"M"=>"Medium Rotation", 
					"L"=>"Light Rotation", 
					"O"=>"Optional (Out of Rotation)");

$bin = $_GET['bin'];
if(strlen($categories[$bin]) < 1)
	die("Invalid bin code.");


echo "<h2>".$categories[$bin]."</h2>\n";
echo "<table id='$bin'>\n";
echo "<tr><th>#</th><th>Artist</th><th>Album</th><th>Action</th></tr>\n";


// AND libcd.cAlbumNo != '' 
$q = "SELECT libcd.cID, libcd.cAlbumNo, libartist.aPrettyArtistName, libcd.cAlbumName, liblabel.lPrettyLabelName FROM ".
	"libcd, libartist, liblabel WHERE libcd.c_aID=libartist.aID AND libcd.c_lID=liblabel.lID ".
	"AND libcd.cBin='$bin' ORDER BY libartist.aPrettyArtistName ASC, libcd.cAlbumName ASC";
$rs = mysql_query($q) or die(mysql_error());

while($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
	$cdid = $row['cID'];
	$cdcode = $row['cAlbumNo'];
	
	if($bin == "T")
		echo "<tr class='moverot'><td style='width: auto'>".$cdid."</td>";
	else echo "<tr class='moverot' id='$cdcode'><td style='width: auto'>".$cdcode."</td>";
	
	echo "<td style='font-face: bold'>".$row['aPrettyArtistName']."</td>";
	echo "<td style='font-face: italic'>".$row['cAlbumName']."</td>\n";
	echo "<td class='rotui'>";
	
	if($bin == "T")
		echo "<button type='button' onclick=\"writeReview('$cdid')\">Review this!</button>";
	else echo "<button type='button' onclick=\"openDialog('$cdcode')\">Read review</button>";
	
	echo "</td></tr>\n";
}

echo "</table>\n\n\n";


?>