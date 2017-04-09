<br />
<?php
//David Cohen
//July-August 2010
session_start();
require_once('conn.php');
global $user;
$username=$user->name;
if (!$_POST['submit']){
$query="SELECT id FROM fishbowl ORDER BY id ASC";
$sql = mysql_query($query) or die(mysql_error());
	while($q = mysql_fetch_array($sql, MYSQL_ASSOC)){
$ids[] = $q['id']; //all primary keys go into array
	}

//Randomize array ids, serialize and use $_SESSION to handle refresh
shuffle($ids); 
$_SESSION['keys'] = serialize($ids); 
?>
<h2>This is the fishbowl review page. Click "Start" to begin reviewing applications.</h2><br /><br />
<form action='fishbowl-review' method='post'>
	&nbsp;&nbsp;&nbsp;&nbsp;<input name='submit' type='submit' value='Start!' />
</form>
<?php	
}
else{
	$ids = unserialize($_SESSION['keys']);

//Posting the previous rating into the mysql database	
	if($_POST['rating']){
		$rating = $_POST['rating'];
		$just_rated = $_SESSION['just_rated'];

//		echo "Record number $just_rated was just rated $rating stars.<br />";	
		$query="SELECT * FROM fishbowl WHERE id='$just_rated'";
		$sql = mysql_query($query) or die(mysql_error()); 
			while($q = mysql_fetch_array($sql, MYSQL_ASSOC)){
				$average = $q['average'];
	//Note: Weight is the number of reviewers who have reviewed this entry. 
				$weight = $q['weight'];
			}
//		echo "<p>Previous average was $average and previous weight was $weight. </p>";
		$average = ($average*$weight + $rating) / ($weight + 1);
		$weight++;
		mysql_query("UPDATE fishbowl SET average = $average, weight = $weight WHERE id = $just_rated");
//		echo "<p>New average is $average and new weight is $weight. </p>";
	}
	
//Confirming that all entries have been successfully reviewed.
		if(empty($ids)){
			echo "<h2>You're Done!</h2>";
			echo "<table>
				<tr>
					<td>id</td>
					<td>User</td>
					<td>Average</td>
					<td>Weight</td>
				</tr>";
			$query="SELECT * FROM fishbowl ORDER BY average DESC";
			$sql = mysql_query($query) or die(mysql_error()); 
				while($q = mysql_fetch_array($sql, MYSQL_ASSOC)){
					$id = $q['id'];
					$username = $q['username'];
					$average = $q['average'];
					$weight = $q['weight'];
					//Get names of all DJs
					$djquery = "SELECT * FROM djs WHERE drupal='$username'";
					$qdj = mysql_query($djquery) or die(mysql_error());
					while($dj = mysql_fetch_array($qdj, MYSQL_ASSOC)) {
						$name = $dj['name'];
					}
				echo"<tr>
				<td>$id</td>
				<td>$name</td>
				<td>$average</td>
				<td>$weight</td>
				</tr>";
				}
		}
	else{
//The Current Rating page		
	$current = array_pop($ids);
// 	echo "You are currently rating record number $current.";
	$query="SELECT * FROM fishbowl WHERE id='$current'";
	$sql = mysql_query($query) or die(mysql_error()); 
		while($q = mysql_fetch_array($sql, MYSQL_ASSOC)){
		/*	$semesters = $q['semesters'];
			$missedShows = $q['missedShows'];
			$liveShows = $q['liveShows'];
			$springFest = $q['springFest'];
			$specialty = $q['specialty'];
			$review = $q['review'];
			$other = $q['other'];
		*/
		foreach($q as $k=>$v) $$k = stripslashes($v);
		
			if($specialty =="1") $specialtyDisp = "Yes";
				else $specialtyDisp = "No";
				echo $id . "<p>Number of semesters at the station: <br /><i>$semesters</i></p>
				<p>Number of missed shows: <br /><i>$missedShows</i></p>
				<p>Did you help set up for live shows or events?<br /><i>$liveShows</i></p>
				<p>Did you help with the Spring Festival?<br /><i>$springFest</i></p>
				<p>Applying for a specialty show?<br /><i>$specialtyDisp</i></p>
				<p>Number of CDs reviewed:<br /><i>$review</i></p>
				<p>Is there anything else that you've done for the station?<br /><i>$other</i></p>";
		}
	$_SESSION['just_rated'] = $current;
	
//Javascript Validation of the radio buttons
drupal_add_js('function validateForm() {
with (document.review) {
var alertMsg = "Pick a rating.";
radioOption = -1;
for (counter=0; counter<rating.length; counter++) {
if (rating[counter].checked) radioOption = counter;
}
if (radioOption == -1) alertMsg = "Pick a Rating!";
if (alertMsg != "Pick a rating.") {
alert(alertMsg);
return false;
} else {
return true;
} } }', 'inline');
//The rating form with the pretty stars.
?>
<form name='review' action='fishbowl-review' method='post' onSubmit='return validateForm()'>
	<p>
	<input type='radio' name='rating' value='1' />&#9733;<br />
	<input type='radio' name='rating' value='2' />&#9733;&#9733;<br />
	<input type='radio' name='rating' value='3' />&#9733;&#9733;&#9733;<br />
	<input type='radio' name='rating' value='4' />&#9733;&#9733;&#9733;&#9733;<br />
	<input type='radio' name='rating' value='5' />&#9733;&#9733;&#9733;&#9733;&#9733;<br />
	</p>
	<input name='submit' type='submit' value='Submit Rating' />
	</form>
<?php
//Re-serializing the keys that now have one less variable.
$_SESSION['keys'] = serialize($ids);	
}

}
?>