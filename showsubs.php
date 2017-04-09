<?php
/** This page is to replace the forums when djs apply for show subs.
Started 4 Oct 2010.
David Cohen
**/

require_once('conn.php');
include('showtable.php');
	global $user;
	$username = $user->name;
//session_start();
echo "<br />";

	//echo table of all the stuff

$currTime = date("Y-m-d H:i:s", time());
$query = "SELECT * FROM showsubs WHERE show_time > '$currTime'";
$result = mysql_query($query) or die(mysql_error());
if(mysql_num_rows($result) < 1){
	echo "No show subs needed!";
}
else{
	echo "<form method='POST'><table><tr><td>DJ</td><td>Show Time</td><td>Description</td><td>Fill</td></tr>";
	
while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	$dju = $row['dj_name'];
	$showtime = $row['show_time'];
	$desc = $row['description'];
	$filler = $row['filler'];
	
	$djquery = "SELECT * FROM djs WHERE drupal = '$dju' LIMIT 1";
	$qdj = mysql_query($djquery) or die(mysql_error());
	$dj = mysql_fetch_array($qdj, MYSQL_ASSOC);
	$djname = $dj['name'];
	
	
	echo "<tr><td>$djname</td><td>$showtime</td><td>$desc</td><td><input type='submit' name='subfor' value='Sub for this Show!' /></td>";
}
	echo "<input type='hidden' name='filler' value ='$username' /></form></table>";
}

if(isset($_GET['addsub'])){
//DJ Name from Drupal Username
$djquery = "SELECT * FROM djs WHERE drupal = '$username' LIMIT 1";
$qdj = mysql_query($djquery) or die(mysql_error());
$dj = mysql_fetch_array($qdj, MYSQL_ASSOC);
	$name = $dj['name'];
//if($dj['alias'] != ''){
//	$alias = $dj['alias'];
//}
//and find the shows
$query = "SELECT * FROM $showtable WHERE dj_name LIKE '%$name%'";
$sql = mysql_query($query) or die("Query failed : " . mysql_error());

//if there's only one show that the person does, it's automatically selected. otherwise, user picks.
$num_rows = mysql_num_rows($sql);
if($num_rows < 1){
	echo "You don't have a show!";
}
elseif($num_rows > 1){
echo "<br /><select name='show'><option value='%'>Select a Show</option>";

	while($s = mysql_fetch_array($sql, MYSQL_ASSOC)){
		$id = $s['show_id'];
		$showname = $s['show_name'];
		$djname = $s['dj_name'];
	//echo $id ."<br />" .$showname;
if(!$showname) $showname = "(No Showname)";
	echo "<option value='$id'>$id - $djname - $showname</option>";
	}
	echo "</select>";
}

else{
	$s = mysql_fetch_array($sql, MYSQL_ASSOC);
	$id = $s['show_id'];
	$showname = $s['show_name'];
	$djname = $s['dj_name'];
	if(!$showname) $showname = "(No Showname)";	
}
}
echo "<a href='?addsub'>thingy</a>";
?>

