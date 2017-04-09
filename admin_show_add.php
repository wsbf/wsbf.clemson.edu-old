<?php
/*
David Cohen - 1/18/2011
show_add.php
Used to 
*/
require_once('conn.php');
require_once('utils_ccl.php');
sanitizeInput();
function printForm($inArr){

	
	echo "<table><form method='POST'>";
	echo "<tr><td>DJ Name (hold Ctrl or Command to select multiple): </td><td><select name='names[]' size='8' multiple>";
		$q = "SELECT * FROM djs WHERE still_here = '1' ORDER BY sort_by";
		$rs = mysql_query($q) or die(mysql_error());
		while($row = mysql_fetch_assoc($rs)){
			$name = $row['name'];
			$sort_by = $row['sort_by'];
			$alias = $row['alias'];
			$dj_id = $row['dj_id'];

			echo "<option value='$dj_id'>$sort_by --- $name";
			if($row['alias'])
				echo " ($alias)</option>";
			else
				echo "</option>";
		}
	echo "</select></td></tr>";
	$days = array(	"0"=>"Sunday",
					"1"=>"Monday", 
					"2"=>"Tuesday", 
					"3"=>"Wednesday", 
					"4"=>"Thursday", 
					"5"=>"Friday",
					"6"=>"Saturday"
					);
	echo "<tr><td>Day: </td><td><select name='day'>";		
	$ctr = 0;
	foreach($days as $day){
		//this may be a bad way to do it, but i can't figure out how to use the indices.
		if(isset($_POST['day']) && $_POST['day'] == $ctr)
			echo "<option value='$ctr' selected>$day - $ctr</option>";
		else 
			echo "<option value='$ctr'>$day - $ctr</option>";
		$ctr++;
	}
	
	//convert values from inArray (for validation)
if(!empty($inArr)){
	foreach($inArr as $k=>$v) $$k=$v;
	echo "</select></tr>
	<tr><td>Start Hour: </td><td><input type='number' name='start_hour' maxlength='2' size='5' value='$start_hour' />:<input type='number' name='start_min' value='$start_min' maxlength='2' size='5' />";

if($ampm == 'am')
	echo "<input type='radio' name='ampm' value='am' checked /> am
	<input type='radio' name='ampm' value='pm' /> pm";
elseif($ampm == 'pm')
	echo "<input type='radio' name='ampm' value='am' /> am
	<input type='radio' name='ampm' value='pm' checked /> pm";
else
	echo "<input type='radio' name='ampm' value='am' /> am
	<input type='radio' name='ampm' value='pm' checked /> pm";
	
	echo"</td></tr>
	<tr><td>Length: </td><td>";
	if($show_length == 180)
		echo "<input type='radio' name='show_length' value='120' /> 2 hour<br />
		<input type='radio' name='show_length' value='90' /> 1.5 hour<br />
		<input type='radio' name='show_length' value='180' checked /> 3 hour<br />";
	elseif($show_length == 90)
		echo "<input type='radio' name='show_length' value='120' /> 2 hour<br />
		<input type='radio' name='show_length' value='90' /> 1.5 hour<br />
		<input type='radio' name='show_length' value='180' checked /> 3 hour<br />";
	else
		echo "<input type='radio' name='show_length' value='120' checked /> 2 hour<br />
		<input type='radio' name='show_length' value='90' /> 1.5 hour<br />
		<input type='radio' name='show_length' value='180' /> 3 hour<br />";
	
	echo "</td></tr>
	<tr><td>Type</td><td>";
if($specialty == '1')
	echo "<input type='radio' name='specialty' value='0' /> Rotation<br />
	<input type='radio' name='specialty' value='1' checked /> Specialty<br />
	<input type='radio' name='specialty' value='2' /> Talk<br />
	<input type='radio' name='specialty' value='3' /> Jazz<br />";

elseif($specialty == '2')
	echo "<input type='radio' name='specialty' value='0' /> Rotation<br />
	<input type='radio' name='specialty' value='1' /> Specialty<br />
	<input type='radio' name='specialty' value='2' checked /> Talk<br />
	<input type='radio' name='specialty' value='3' /> Jazz<br />";

elseif($specialty == '3')
	echo "<input type='radio' name='specialty' value='0' /> Rotation<br />
	<input type='radio' name='specialty' value='1' /> Specialty<br />
	<input type='radio' name='specialty' value='2' /> Talk<br />
	<input type='radio' name='specialty' value='3' checked /> Jazz<br />";

else 
	echo"<input type='radio' name='specialty' value='0' checked /> Rotation<br />
	<input type='radio' name='specialty' value='1' /> Specialty<br />
	<input type='radio' name='specialty' value='2' /> Talk<br />
	<input type='radio' name='specialty' value='3' /> Jazz<br />";

echo "<tr><td>Show Name</td><td><input type='text' name='show_name' value='$show_name' /></td></tr>
	<tr><td>Show Description: </td><td><textarea name='show_desc' rows='5' cols='60'>$show_desc</textarea></td></tr>
	<tr><td></td><td><input type='submit' name='submit' value='Submit' /></td></tr></form></table>";
}

else{
		echo "</select></tr>
		<tr><td>Start Hour: </td><td><input type='number' name='start_hour' maxlength='2' size='5' value='00' />:<input type='number' name='start_min' value='00' maxlength='2' size='5' />
		<input type='radio' name='ampm' value='am' /> am
		<input type='radio' name='ampm' value='pm' /> pm
		</td></tr>
		<tr><td>Length: </td><td>
			<input type='radio' name='show_length' value='120' checked /> 2 hour<br />
			<input type='radio' name='show_length' value='90' /> 1.5 hour<br />
			<input type='radio' name='show_length' value='180' /> 3 hour<br />";
	//		<input type='radio' name='show_length' value='-1' /> Other: <input type='number' value='0' name='length_other' maxlength='3' size='8' />
		echo "</td></tr>
		<tr><td>Type</td><td>
		<input type='radio' name='specialty' value='0' checked /> Rotation<br />
		<input type='radio' name='specialty' value='1' /> Specialty<br />
		<input type='radio' name='specialty' value='2' /> Talk<br />
		<input type='radio' name='specialty' value='3' /> Jazz<br />
		<tr><td>Show Name</td><td><input type='text' name='show_name' /></td></tr>
		<tr><td>Show Description: </td><td><textarea name='show_desc' rows='5' cols='60'></textarea></td></tr>
		<tr><td></td><td><input type='submit' name='submit' value='Submit' /></td></tr></form></table>";
}
}
		
if(isset($_POST['names'])){
	foreach($_POST as $k=>$v) $$k=$v;
$show_name = mysql_real_escape_string(htmlspecialchars($show_name));
$show_desc = mysql_real_escape_string(htmlspecialchars($show_desc));

$errormsg = '';
	if($start_hour == '00')
		$errormsg .= 'Time is incorrect. Enter 12 AM for midnight.<br />';
	if(empty($ampm))
		$errormsg .= 'Please select am/pm.<br />';
if($errormsg != ''){
	echo $errormsg;
	printForm($_POST);
}
else{
	if($_POST['ampm'] == "pm" && $_POST['start_hour'] != 12)
		$start_hour = $_POST['start_hour'] += 12;
	elseif($_POST['ampm'] == "am" && $_POST['start_hour'] == 12)
		$start_hour = 0;
	else
		$start_hour = $_POST['start_hour'];

/*	the following was for the case of "other," which can easily be added to the form. 
	But make sure it's in increments of 30 to keep the schedule from breaking!

	if($_POST['show_length'] != '-1')
		$show_length = $_POST['show_length'];
	else
		$show_length = $_POST['length_other'];
*/


	
	$ins = "INSERT INTO shows (day, start_hour, start_min, show_length, show_name, show_desc, specialty) VALUES('$day', '$start_hour', '$start_min', '$show_length', '$show_name', '$show_desc', '$specialty')";
	$query = mysql_query($ins) or die(mysql_error());

	$q = "SELECT show_id FROM shows ORDER BY show_id DESC LIMIT 1";
	$res = mysql_query($q) or die(mysql_error());
	$row = mysql_fetch_array($res);
	$show_id = $row['show_id'];
	$id_disp = "";

	//NAMES ACTUALLY SIGNIFIES AN ARRAY OF DJ_ID VALUES, NOT NAMES
		foreach($names as $dj_id){
			$q = "INSERT INTO show_dj (show_id, dj_id) VALUES('$show_id', '$dj_id')";
			$insert = mysql_query($q) or die(mysql_error());
			$id_disp .= "$dj_id,";
		}
		echo "Successfully inserted: <br />DJ ID: $id_disp <br />Day: $day <br />Start Time: $start_hour:$start_min<br />Length: $show_length<br />Specialty: $specialty<br />Show Name: $show_name <br /><br />";
printForm(NULL);
	}
}
else{
	printForm(NULL);
}
?>