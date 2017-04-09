<?php
//Accessed via AJAX call from logging software
//Logs in the given show
//Zach Musgrave, WSBF-FM Clemson, Dec 2009

//on success, returns lbshow prikey
//-1 on failboat

//GET parms: djname, showsid (series), showname (custom!)
//RETurns: show id (instance)

include("../connect.php");
include('../utils_ccl.php');
if (isset($_GET['djname']) && isset($_GET['showsid'])) {
	sanitizeInput();
	$sDJName = urldecode($_GET['djname']);
	$sDJName = substr($sDJName, 0, strlen($sDJName) - 2); //accounts for trailing ', '
	/** WE DO NOT SUPPORT multi-djs with a SELF-TITLED show.
		wsbf doesn't really ever do this.
	**/
	$djNames = explode(", ", $sDJName);
	$sDJName = "";
	foreach($djNames as $name){
		if($name != "-1") {
			$sDJName .= $name . ", ";
		}	
	}
	$sDJName = substr($sDJName, 0, strlen($sDJName) - 2);
	
	
	//only set this to !null if show_id is MIA
	if(isset($_GET['showname']))
		$sShowName = urldecode($_GET['showname']); 
	else $sShowName = '';
	
	$row0['specialty'] = "";
	$shows_id = urldecode($_GET['showsid']);
	
	if(trim($sShowName) == '' && $shows_id > -1) {
		$r0 = mysql_query("SELECT * FROM shows WHERE show_id='$shows_id'");
		$row0 = mysql_fetch_array($r0, MYSQL_ASSOC);
		
		$sShowName = $row0['show_name'];
		
		
	}
	else {
/**		if($shows_id == -1) { //sShowType, show_id
			// sDJName can be csv!!!
			$r0 = mysql_query("SELECT * FROM shows WHERE dj_name='$sDJName'");
			$row0 = mysql_fetch_array($r0, MYSQL_ASSOC);
			$shows_id = $row0['show_id'];
		}**/
	}
	
	
	switch($row0['specialty']) {
		case 0: $sShowType = "Rotation";
		break;
		case 1: $sShowType = "Specialty";
		break;
		case 2: $sShowType = "Talk";
		break;
		case 3: $sShowType = "Jazz";
		break;
		default: $sShowType = "Unknown";
		
	}
	
	
	
	
	$time = date("Y-m-d G:i:s"); //this is probably redundant, schema has default CURRENT_TIMESTAMP
	$query = "INSERT INTO lbshow (sStartTime,sDJName,sShowType,sType, sShowName, show_id) VALUES " . 
								"('$time','$sDJName','$sShowType',2,'$sShowName','$shows_id')";

	$result = mysql_query($query) or //die("Query failed : " . mysql_error());
		die(-1);
	$showID = mysql_insert_id();
	echo $showID;
}

mysql_close();
?>