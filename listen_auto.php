<?php
session_start();
//require_once("conn.php");

require_once("stream_conn.php");

$q = sprintf("SELECT show_typeID FROM `show` ORDER BY show.showID DESC LIMIT 1");
$rsc = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($rsc, MYSQL_ASSOC);
if($row['show_typeID'] == 6){
	include("listen_ctv2.php");
}
else
	include("listen.php");
?>