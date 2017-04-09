<br />
<?php 
//David Cohen
//July-August 2010
require_once('conn.php');
require_once('utils_ccl.php');
sanitizeInput();
global $user;
$username=$user->name;

if(empty($username) && $_GET['id']){
//if($_GET['id']){
	$djid = $_GET['id'];
	$djquery = "SELECT * FROM djs WHERE dj_id='$djid' ORDER BY dj_id ASC LIMIT 1";
	$qdj = mysql_query($djquery) or die(mysql_error());
	$dj = mysql_fetch_array($qdj, MYSQL_ASSOC);
	
}

else{
	$djquery = "SELECT * FROM djs WHERE drupal='$username' ORDER BY drupal ASC LIMIT 1";
	$qdj = mysql_query($djquery) or die(mysql_error());
	$dj = mysql_fetch_array($qdj, MYSQL_ASSOC);
}
	$name = $dj['name'];

//The following script will kill it after the deadline. 
//MUST STAY IN THE FORMAT '4:00pm August 1, 2010'
//Changing the deadline variable is all you need to change
//type 'spring' or 'fall' for upcoming semester so it knows whether to put whether they helped for springfest or not!
$semester = 'fall';
$deadline = '6:00pm August 24, 2011';
$end_date = strtotime($deadline);
 $now = time();

//Make sure that the username hasn't submitted anything
$query = "SELECT * FROM fishbowl WHERE username='$username'";
$result = mysql_query($query);
	if(mysql_num_rows($result)) echo "$name, You've already submitted your fishbowl points!";
	
elseif ($end_date < $now) {
echo "<h2>Sorry, kid. Maybe next semester.</h2>
<p>The deadline for submitting fishbowl applications was $deadline. You missed it. The current date and time is " .date("l F d, Y, h:i:s A");
}
//And everything else in the page is for when the time's before the deadline. 
else{
if (!$_POST['submit']){
echo "<h1><font color='red'>These applications must be submitted before $deadline</h1><p>There are no exceptions to this rule. So get it done.</p></font>
<p>You are posting this as $name. If this is incorrect, please <a href='logout'>log out</a> and sign in as yourself.</p>";

//The Main Form
?>
<form action='fishbowl' method='POST'>
<p>How many semesters have you worked at WSBF? If you DJ'd over a summer, count it as 1.<br /><input type='text' size='6' maxlength='2' name='semesters' value='0' /></p>
<p>How many times did you miss your show last semester? Don't lie; the records will be checked.<br /><input type='text' size='6' maxlength='2' name='miss' value='0' /></p>
<p>Did you help set up for live shows or events? List and describe briefly.<br /><textarea name='liveshows' cols='50' rows='10'></textarea></p>
<?php
switch($semester){
case 'fall':
	echo "<p>Did you help with the Spring Festival? In what capacity? Tell us about it.<br /><textarea name='springfest' cols='50' rows='10'></textarea></p>";
	echo "<p>Was your previous show 3-5am or 5-7am?<br /><input type='radio' name='dead_hours' value='0' />No<br /><input type='radio' name='dead_hours' value='1' />Yes</p>";
	break;
case 'spring':
	echo "<input type='hidden' name='springfest' value=''";
	echo "<p>Is your show currently 3-5am or 5-7am?<br /><input type='radio' name='dead_hours' value='0' />No<br /><input type='radio' name='dead_hours' value='1' />Yes</p>";
	break;
}
?>
<p>Are you applying for a specialty show for this semester? <br /><input type='radio' name='specialty' value='0' />No<br /><input type='radio' name='specialty' value='1' />Yes</p>
<p>How many CDs have you reviewed? Again, don't lie, because we'll check.<br /><input type='text' size='6' maxlength='2' name='review' value='0' /></p>

<p>Is there anything else that you've done for the station?<br /><textarea name='other' cols='50' rows='10'></textarea></p>
<input name='submit' type='submit' value='Submit'/> as <?php echo $name ?>.
</form>
<br />Note: The submit button won't work, it's probably because you have characters that aren't numbers in one of the number fields.
<?php 
}
else{
	//Validating the user's input
$readytosubmit = TRUE;
$semesters = mysql_real_escape_string($_POST['semesters']);
if($semesters == "") $readytosubmit = FALSE;
$missedShows = mysql_real_escape_string($_POST['miss']);
if($missedShows == "") $readytosubmit = FALSE;
$liveShows = mysql_real_escape_string($_POST['liveshows']);
if($liveShows == "") $readytosubmit = FALSE;

if($semester == 'fall'){
$springFest = mysql_real_escape_string($_POST['springfest']);
if($springFest == "") $readytosubmit = FALSE;
}

$dead_hours = mysql_real_escape_string($_POST['dead_hours']);
if($dead_hours == "") $readytosubmit = FALSE;
$specialty = mysql_real_escape_string($_POST['specialty']);
if($specialty == "" && $semester == "fall") $readytosubmit = FALSE;
$review = mysql_real_escape_string($_POST['review']);
if($review == "") $readytosubmit = FALSE;
$other = mysql_real_escape_string($_POST['other']);
if($other == "") $readytosubmit = FALSE;

if($readytosubmit) {
	$sql="INSERT INTO fishbowl (username, semesters, missedShows, liveShows, springFest, specialty, dead_hours, review, other)
	VALUES
	('$username','$semesters', '$missedShows', '$liveShows', '$springFest', '$specialty', '$dead_hours', '$review', '$other')";

	if (!mysql_query($sql,$link))
	  {
	  die('Error: ' . mysql_error());
	  }
	
	//Repeat to user what s/he has just submitted.
	
	if($specialty =="1") $specialtyDisp = "Yes";
		else $specialtyDisp = "No";
		
	if($dead_hours == "1") $dh = "Yes";
		else $dh = "No";
	echo "<br /><p><h1>Thank you, $name!</h1>Your application has been submitted!<br /><h2>Here's what you put: </h2></p><br />";
	echo "<p>How many semesters have you worked at WSBF? If you DJ'd over a summer, count it as 1.<br /><i>$semesters</i></p>
	<p>How many times did you miss your show last semester? Don't lie; the records will be checked.<br /><i>$missedShows</i></p>
	<p>Did you help set up for live shows or events? List and describe briefly.<br /><i>$liveShows</i></p>";
	
	switch($semester){
	case 'fall':
		echo "<p>Did you help with the Spring Festival? In what capacity? Tell us about it.<br /><i>$springfest</i></p>";
		echo "<p>Was your previous show 3-5am or 5-7am?<br /><i>$dh</i></p>";
		break;
	case 'spring':
		echo "<p>Is your show currently 3-5am or 5-7am?<br /><i>$dh</i></p>";
		break;
	}
	echo "<p>Are you applying for a specialty show for this semester? <br /><i>$specialtyDisp</i></p>
	<p>How many CDs have you reviewed? Again, don't lie, because we'll check.<br /><i>$review</i></p>
	<p>Is there anything else that you've done for the station?<br /><i>$other</i></p>";
	}
else {
	
	//If there's an error, the following should redisplay the form but not delete everything the user has entered by setting the default value for each to the previously entered variables
	
	echo "<br /><h1>Whoops, it looks like you left something blank!</h1>
	<form action='fishbowl' method='POST'>
	<p>How many semesters have you worked at WSBF? If you DJ'd over a summer, count it as 1.<br /><input type='text' size='2' maxlength='2' name='semesters' value='$semesters' /></p>
	<p>How many times did you miss your show last semester? Don't lie; the records will be checked.<br /><input type='text' size='2' maxlength='2' name='miss' value='$missedShows' /></p>
	<p>Did you help set up for live shows or events? List and describe briefly.<br /><textarea name='liveshows' cols='50' rows='10'>$liveShows</textarea></p>
	<p>Did you help with the Spring Festival? In what capacity? Tell us about it.<br /><textarea name='springfest' cols='50' rows='10'>$springFest</textarea></p>
	<p>Are you applying for a specialty show for this semester? <br />";
	if (!isset($specialty)) echo "<input type='radio' name='specialty' value='1' />Yes<br /><input type='radio' name='specialty' value='0' />No</p>";
	else {
		if($specialty == 1) echo "<input type='radio' name='specialty' value='0' />No</p><br /><input type='radio' name='specialty' value='1' checked='yes' />Yes";
		elseif ($specialty == 0) echo "<input type='radio' name='specialty' value='0' checked='yes' />No<br /><input type='radio' name='specialty' value='1' />Yes</p>";
	}
	
//fall vs spring again; to leave out springfest

	switch($semester){
	case 'fall':
		echo "<p>Did you help with the Spring Festival? In what capacity? Tell us about it.<br /><textarea name='springfest' cols='50' rows='10'>$springfest</textarea></p>";
		
		if($dead_hours == 1)
		echo "<p>Was your previous show 3-5am or 5-7am?<br /><input type='radio' name='dead_hours' value='0' />No<br /><input type='radio' name='dead_hours' value='1' checked='yes' />Yes</p>";
		elseif ($dead_hours == 0) echo "<p>Was your previous show 3-5am or 5-7am?<br /><input type='radio' name='dead_hours' value='0' checked='yes' />No<br /><input type='radio' name='dead_hours' value='1' />Yes</p>";
		break;
	case 'spring':
		echo "<input type='hidden' name='springfest' value='' />";
		
		if($dead_hours == 1)
		echo "<p>Was your previous show 3-5am or 5-7am?<br /><input type='radio' name='dead_hours' value='0' />No<br /><input type='radio' name='dead_hours' value='1' checked='yes' />Yes</p>";
		elseif ($dead_hours == 0) echo "<p>Was your previous show 3-5am or 5-7am?<br /><input type='radio' name='dead_hours' value='0' checked='yes' />No<br /><input type='radio' name='dead_hours' value='1' />Yes</p>";
		break;
		
	}	
	
	echo "<p>How many CDs have you reviewed? Again, don't lie, because we'll check.<br /><input type='text' size='2' maxlength='2' name='review' value='$review' /></p>
	<p>Is there anything else that you've done for the station?<br /><textarea name='other' cols='50' rows='10'>$other</textarea></p>
	<input name='submit' type='submit' value='Submit'/> as $name.
	</form>
	<br />Note: If the submit button won't work, it's probably because you have characters that aren't numbers in one of the number fields.";
}
}
}

?>
