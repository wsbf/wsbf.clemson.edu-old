<?php
//Accessed via AJAX call from logging software
//Logs out the given id and sets relevant current time
//Zach Musgrave, WSBF-FM Clemson, Dec 2009

include("../connect.php");
if (isset($_GET['id'])) {
	$showID = $_GET['id'];
	$time = date("Y-m-d G:i:s");
	$query = "UPDATE lbshow SET sEndTime='$time' WHERE sID='$showID'";
	mysql_query($query) or die("Query failed : " . mysql_error());
	echo $showID; // on success
}
?>