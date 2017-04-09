<?php
//part of the chat system - dac
session_start();
require_once('conn.php');
require_once('utils_ccl.php');
sanitizeInput();
/*
if(isset($_SESSION['name'])){
	$text = $_POST['text'];
	
	$fp = fopen("log.html", 'a');
	fwrite($fp, "<div class='msgln'>(".date("g:i A").") <b>".$_SESSION['name']."</b>: ".stripslashes(htmlspecialchars($text))."<br></div>");
	fclose($fp);
}
*/
if(isset($_SESSION['name'])){
$name = $_SESSION['name'];
if(isset($_SESSION['ip']))
	$ip = $_SESSION['ip'];
else
	$ip=$_SERVER['REMOTE_ADDR'];
//$time = date("H:i:s");
$text = htmlspecialchars($_POST['text']);

//$query = "";
mysql_query("INSERT INTO chatlog (name,entry,ip) VALUES('$name', '$text', '$ip')") or die(mysql_error());
}
?>