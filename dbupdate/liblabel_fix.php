<?php

/**		liblabel_fix.php - by ztm, 1 October 2010
	This is a maintenance script; it should not be run often (or even needed anymore).
	Basically, the field liblabel.lCmpLabelName is supposed to be a unique key, but in 
	our DB it often is not. This script makes liblabel compatible with at least that IDEA, 
	even if MySQL does not have that field set in such a manner.
**/
require_once('../conn.php');
echo "<pre>";

function fixLabel($cmpLabel) {
	$qu = "SELECT * FROM liblabel WHERE lCmpLabelName='$cmpLabel' ORDER BY lID ASC";
	$rs = mysql_query($qu) or die(mysql_error());
	
	echo "Fixing $cmpLabel, ".mysql_num_rows($rs)." total entries\n";
	
	
	$lID = -1;
	
	while($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
		if($lID == -1) {
			$lID = $row['lID'];
			echo "\tCorrect lID: $lID\n";
		}
		else {
			$toReplace = $row['lID'];
			echo "\tDestroying lID: $toReplace\n";
			$qv = "SELECT * FROM libcd WHERE c_lID='$toReplace'";
			$rt = mysql_query($qv) or die(mysql_error());
			while($row = mysql_fetch_array($rt, MYSQL_ASSOC)) {
				$cID = $row['cID'];
				echo "\t\tFixing cID: $cID\n";
				$qw = "UPDATE libcd SET c_lID='$lID' WHERE cID='$cID'";
				if(!mysql_query($qw))
					die(mysql_error());
			}
			$qx = "DELETE FROM liblabel WHERE lID='$toReplace'";
			if(!mysql_query($qx))
					die(mysql_error());
		}
	}
	
}




$qu = "SELECT * FROM liblabel ORDER BY lID DESC";
$rs = mysql_query($qu) or die(mysql_error());

$labelCmps = array();
$counter = 0;
while($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
	//echo $row['lPrettyLabelName'] . "\n";
	if(!in_array($row['lCmpLabelName'], $labelCmps))
		$labelCmps[] = $row['lCmpLabelName'];
	else {
		++$counter;
		fixLabel($row['lCmpLabelName']);
	}
}

echo "$counter duplicate labels.\n";
echo "</pre>";
?>