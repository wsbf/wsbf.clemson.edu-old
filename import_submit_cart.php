<?php
$delete = FALSE; // default state - don't fuck up drs by deleting tracks


$time = microtime(true);
require_once("conn.php");
require_once("utils_ccl.php");
require_once("review_lib.php");
sanitizeInput();
//require_once("dbupdate/getid3/getid3/getid3.php");

define('BASE_DIR', "E:\\DRSAUDIO");
define('BASE_NEW', "E:\\ZAutoLib\\carts\\");
define('SCRIPT_PREFIX', "http://wsbf.net/wizbif/");
define('LABEL_DUMMY', 1899);

//sanitizeInput();
if(session_id() == "") session_start();

global $user;
if(!isset($user)) $user = "zachm";
else $user = $user->name;


echo "<h1>PRELIMINARY IMPORT SYSTEM</h1>\n";
echo "<h3>Import Submission/Confirmation</h3>\n";
echo "<p>Go <a href='".urldecode($_POST['redirect'])."'>back</a>...</p>\n";
echo "<div id='contents'>";

	$cartName = trim($_POST['cartName']);
	$cartType = $_POST['cartType'];
	if(!isset($cartName) || $cartName == "" || $cartType == "")
		die("Please fill out a cart name and cart type!");
	$cartIssuer = trim($_POST['cartIssuer']);
	$oldPath = $_POST['cartPath'];
	$filename = $_POST['filename'];

//this is to say 01/03/1991 instead of 1/3/1991. 	
	if($_POST['startDateMonth'] < 10)
		$startDateMonth = "0".$_POST['startDateMonth'];
	else
		$startDateMonth = $_POST['startDateMonth'];
	
	if($_POST['startDateDay'] < 10)
		$startDateDay = "0".$_POST['startDateDay'];
	else
		$startDateDay = $_POST['startDateDay'];

	if($_POST['endDateMonth'] < 10)
		$endDateMonth = "0".$_POST['endDateMonth'];
	else
		$endDateMonth = $_POST['endDateMonth'];
	
	if($_POST['endDateDay'] < 10)
		$endDateDay = "0".$_POST['endDateDay'];
	else
		$endDateDay = $_POST['endDateDay'];

	$startDate = $_POST['startDateYear'] ."-" .$startDateMonth ."-" .$startDateDay ." 00:00:00";
	
	if(isset($_POST['noEndDate']))
		$endDate = "0000-00-00 00:00:00";
	else
		$endDate = $_POST['endDateYear'] ."-" .$endDateMonth ."-" .$endDateDay ." 11:59:59";

	if($_POST['startTime'] < 10)
	 	$startTime = "0" . $_POST['startTime'] .":00:00";
	else
		$startTime = $_POST['startTime'] .":00:00";

	if($_POST['endTime'] < 10)
	 	$endTime = "0" .$_POST['endTime'] .":00:00";
	else
		$endTime = $_POST['endTime'] .":00:00";


	
	if($delete)
		echo "Delete mode is turned on.<br>";
	else
		echo "Delete mode is turned off - old files will be preserved.<br>";
	echo "Moving files and updating track table...<br>";

$newPath = BASE_NEW . $filename;

if(!copy($oldPath, $newPath))
	die("Could not copy: $oldPath to $newPath");
	
	
	
if($delete) {
	if(!unlink($oldPath))
		die("Could not delete: $oldPath");
}
else {
	$rs = fopen(BASE_DIR ."import_todelete.txt", 'a');
	fwrite($rs, $oldPath."\n");
	fclose($rs);
}

$query = "INSERT INTO libcart (cartDateValid, cartDateInvalid, cartTitle, cartIssuer, cartType, cartFilename) VALUES('$startDate', '$endDate', '$cartName', '$cartIssuer', '$cartType', '$filename')";
$insert = mysql_query($query) or die(mysql_error());

$q = "SELECT * FROM libcart WHERE cartTitle = '$cartName'";
$thingy = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($thingy, MYSQL_ASSOC);
	$cartID = $row['cartID'];

addAction($user, 'libcart', $cartID, 'IMPORT', '');
echo "Complete: All operations finished! Successfully moved $oldPath to $newPath. <br>";
echo "</div>";
$netTime = microtime(true)-$time;
echo "<p>Time needed to execute: ".round($netTime,5)." seconds\n</p>";

// $cartFileName = $newpath . "\\" . $cartName . "." .$_POST['cartExt'];
?>