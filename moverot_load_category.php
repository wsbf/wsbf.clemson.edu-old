<?php

/**
	moverot_load_category.php by ztm, 8july10
	Takes a cBin code, and returns the contents in HTML format
	Called by moverot_main.php via JQuery/AJAX.

**/

require_once('conn.php');
require_once('review_lib.php');
require_once('utils_ccl.php');
sanitizeInput();


//"T"=>"To Be Reviewed"
$categories = array("R"=>"Recently Reviewed", 
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
echo "<tr><th>#</th><th>Artist</th><th>Album</th><th>Move to...</th></tr>\n";

$q = "SELECT libcd.cAlbumNo, libartist.aPrettyArtistName, libcd.cAlbumName, liblabel.lPrettyLabelName FROM ".
	"libcd, libartist, liblabel WHERE libcd.c_aID=libartist.aID AND libcd.c_lID=liblabel.lID AND libcd.cAlbumNo != '' ".
	"AND libcd.cBin='$bin' ORDER BY libartist.aPrettyArtistName DESC, libcd.cAlbumName DESC";
$rs = mysql_query($q) or die(mysql_error());

while($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
	$cdcode = $row['cAlbumNo'];
	echo "<tr class='moverot' id='$cdcode'><td style='width: auto'>".$cdcode."</td>";
	echo "<td style='font-face: bold'>".$row['aPrettyArtistName']."</td>";
	echo "<td style='font-face: italic'>".$row['cAlbumName']."</td>\n";
	echo "<td class='rotui'>";
	echo
		"<a onclick=\"movealbum('$cdcode', 'N')\">-N-</a> | ".
		"<a onclick=\"movealbum('$cdcode', 'H')\">-H-</a> | ".
		"<a onclick=\"movealbum('$cdcode', 'M')\">-M-</a> | ".
		"<a onclick=\"movealbum('$cdcode', 'L')\">-L-</a> | ".
		"<a onclick=\"movealbum('$cdcode', 'O')\">-O-</a>";
	
	echo "</td></tr>\n";
}

echo "</table>\n\n\n";


?>