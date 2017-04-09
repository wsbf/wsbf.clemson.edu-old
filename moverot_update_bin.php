<?php

/**
	moverot_ajax.php by ztm, 8july10
	Takes a drupal username, a cBin code, and a cAlbumNo code and updates cBin accordingly.
	Called by moverot_main.php via JQuery/AJAX. Also updates libaction (as it should).

**/

require_once('conn.php');
require_once('review_lib.php');
require_once('utils_ccl.php');
sanitizeInput();

$categories = array("R", "N", "H", "M", "L", "O");

$user = $_GET['user'];
$bin = $_GET['bin'];
$cdcode = $_GET['cdcode'];

if(!in_array($bin, $categories))
	die("Invalid bin code.");

$getQ = "SELECT cID FROM libcd WHERE cAlbumNo='$cdcode'";
$rsc = mysql_query($getQ) or die(mysql_error());
if(mysql_num_rows($rsc) != 1)
	die("Invalid cd code.");
$cID = mysql_fetch_array($rsc, MYSQL_ASSOC);
$cID = $cID['cID'];

$query = "UPDATE libcd SET cBin='$bin' WHERE cAlbumNo='$cdcode'";
mysql_query($query) or die(mysql_error());


$updateArr = serialize(array('cBin' => $bin, 'cAlbumNo' => $cdcode));
addAction($user, 'libcd', $cID, 'MOVE', $updateArr);
//function addAction($user, $tableName, $priKey, $type, $data)

echo "success";
?>