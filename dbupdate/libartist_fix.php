<?php

/**		libartist_fix.php - by ztm, 1 October 2010

	Find-and-replaced from liblabel_fix.php

	This is a maintenance script; it should not be run often (or even needed anymore).
	Basically, the field libartist.aCmpArtistName is supposed to be a unique key, but in 
	our DB it often is not. This script makes libartist compatible with at least that IDEA, 
	even if MySQL does not have that field set in such a manner.
**/
require_once('../conn.php');
echo "<pre>";

function fixArtist($cmpArtist) {
	$qu = "SELECT * FROM libartist WHERE aCmpArtistName='$cmpArtist' ORDER BY aID ASC";
	$rs = mysql_query($qu) or die(mysql_error());
	
	echo "Fixing $cmpArtist, ".mysql_num_rows($rs)." total entries\n";
	
	
	$aID = -1;
	
	while($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
		if($aID == -1) {
			$aID = $row['aID'];
			echo "\tCorrect aID: $aID\n";
		}
		else {
			$toReplace = $row['aID'];
			echo "\tDestroying aID: $toReplace\n";
			$qv = "SELECT * FROM libcd WHERE c_aID='$toReplace'";
			$rt = mysql_query($qv) or die(mysql_error());
			while($row = mysql_fetch_array($rt, MYSQL_ASSOC)) {
				$cID = $row['cID'];
				echo "\t\tFixing cID: $cID\n";
				$qw = "UPDATE libcd SET c_aID='$aID' WHERE cID='$cID'";
				if(!mysql_query($qw))
					die(mysql_error());
			}
			$qx = "DELETE FROM libartist WHERE aID='$toReplace'";
			if(!mysql_query($qx))
					die(mysql_error());
		}
	}
	
}




$qu = "SELECT * FROM libartist ORDER BY aID DESC";
$rs = mysql_query($qu) or die(mysql_error());

$artistCmps = array();
$counter = 0;
while($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
	//echo $row['lPrettyArtistName'] . "\n";
	if(!in_array($row['aCmpArtistName'], $artistCmps))
		$artistCmps[] = $row['aCmpArtistName'];
	else {
		++$counter;
		fixArtist($row['aCmpArtistName']);
	}
}

echo "$counter duplicate artists.\n";
echo "</pre>";
?>