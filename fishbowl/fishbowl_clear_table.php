<?php
require_once('conn.php');
if(!$_POST){
	echo "Click the button below to move everything from fishbowl to fishbowl_log.";
	echo "<form method='post'><input type='submit' value='Move!' name='submit' /></form>";
}

if(isset($_POST['submit'])) {
$q = "SELECT * FROM fishbowl ORDER BY average DESC";
$rs = mysql_query($q) or die(mysql_error());
$rows = mysql_num_rows($rs);

$Ctr = 0;	
while($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
	$username = mysql_real_escape_string($row['username']);
	$timestamp = $row['timestamp'];
	$semesters = mysql_real_escape_string($row['semesters']);
	$missedShows = mysql_real_escape_string($row['missedShows']);
	$liveShows = mysql_real_escape_string($row['liveShows']);
	$springFest = mysql_real_escape_string($row['springFest']);
	$dead_hours = mysql_real_escape_string($row['dead_hours']);
	$specialty = mysql_real_escape_string($row['specialty']);
	$review = mysql_real_escape_string($row['review']);
	$other = mysql_real_escape_string($row['other']);
	$weight = mysql_real_escape_string($row['weight']);


	$sql="INSERT INTO fishbowl_log (timestamp, username, semesters, missedShows, liveShows, springFest, specialty, dead_hours, review, other, weight)
	VALUES
	('$timestamp', '$username','$semesters', '$missedShows', '$liveShows', '$springFest', '$specialty', '$dead_hours', '$review', '$other', '$weight')";

	if (!mysql_query($sql,$link))
	  {
	  die('Error: ' . mysql_error());
	  }
$Ctr++;
}
echo "$Ctr records added to fishbowl_log <br />";

$truncate = 'TRUNCATE TABLE `fishbowl`';
$query = mysql_query($truncate) or die(mysql_error());
echo "fishbowl table emptied.";

}
?>