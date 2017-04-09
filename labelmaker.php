<?php
//labelmaker.php
//frontend for printlabel.php
//zach musgrave, sept 2009
require_once('conn.php');
?>

<!--<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>WSBF Label Maker</title>
    <link rel="stylesheet" type="text/css" href="http://wsbf.net/wp-content/themes/rounded-grey-blog-10/style.css" />
    <link rel="stylesheet" type="text/css" href="wsbf.css" />
</head>
<html><head>
	<title>Frontend for Label Maker</title>-->
	<script type='text/javascript'>

		var albumids = new Array();
		function append(app) {

			document.getElementById('output').innerHTML += " " + app + " ";
			albumids.push(app);

			if(albumids.length == 4) {
				var get = 'wizbif/printlabel.php?';

				for(i=1; i<=4; i++)
					get += 'a' + i + '=' + albumids[i-1] + '&';
				window.location.href = get;
			}
		}
		function clearA() {
			albumids.length = 0;
			document.getElementById('output').innerHTML = "&nbsp;";
		}
	</script>
<!--</head><body><div style='width: 80%; margin: 50px auto 50px auto;'>-->
<!--<h1>It's a Label Maker</h1><h2><i>(Not a Baby Maker)</i></h2>-->
	<h3>2009, Zach Musgrave, for WSBF-FM Clemson</h3>
	<p>After you click on exactly FOUR albums below, you will be redirected to the printable template page.</p>
	<br><a href='#' onclick='clearA()'>Clear clicked albums</a><br>
	<a href='?showall=1'>Show all albums (ever... there are a lot!)</a><br>
	<a href='?showall=2'>Show ONLY those marked as "Recently Reviewed"</a><br><br>
	<span id='output'>&nbsp;</span>
	<table border='1'>
<?php

$biglist = "SELECT libcd.cAlbumNo, libartist.aPrettyArtistName, libcd.cAlbumName FROM " .
			"libcd, libartist WHERE libcd.c_aID=libartist.aID AND libcd.cReview != '' ";

if(!isset($_GET['showall']))
	$_GET['showall'] = 0;

if($_GET['showall'] == 2){
	$biglist .= "AND cBin >= 'R'";
}
elseif($_GET['showall'] != 1)
	$biglist .= "AND libcd.cAlbumNo > 'G' ";



$biglist .= "ORDER BY libcd.cAlbumNo DESC";
$rsc = mysql_query($biglist);

while($row = mysql_fetch_array($rsc, MYSQL_NUM) )
	echo "<tr><td><a href='#' onclick=\"append('{$row[0]}')\">{$row[0]}</a></td><td>{$row[2]} by {$row[1]}</td></tr>\r\n";

?>
</table><!--</div></body></html>-->
