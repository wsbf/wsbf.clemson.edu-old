<?php
require_once('conn.php');
require_once('utils_ccl.php');
sanitizeInput();
//showtable.php selects the calendar during the school year/summer
global $user;
$username = $user->name;
session_start();

echo "<br />";

//DJ Name from Drupal Username
$djquery = "SELECT * FROM djs WHERE drupal = '$username' LIMIT 1";
$qdj = mysql_query($djquery) or die(mysql_error());
$dj = mysql_fetch_array($qdj);
	$name = $dj['name'];
if($dj['alias'] != ''){
	$alias = $dj['alias'];
}
	$sms = $dj['sms'];
	$dj_id = $dj['dj_id'];

	//splits phone into 3 parts
	$phone = $dj['phone'];
	$split = str_split($phone, 3);
	$p0 = $split[0];
	$p1 = $split[1];
	$p2 = $split[2] .$split[3];

//Find shows with this
$query = "SELECT * FROM shows, show_dj WHERE shows.show_id = show_dj.show_id AND show_dj.dj_id = '$dj_id'";
$sql = mysql_query($query) or die("Query failed : " . mysql_error());

//This makes the changes.
if(isset($_POST['new_alias']) && isset($_POST['new_name'])){
	htmlSanitize($_POST);
	foreach($_POST as $k=>$v)
		$$k = $v;
	if($new_alias == $name) $new_alias = '';

	if($_POST['new_sms'] == 'yes') $new_sms = '1';
	elseif($_POST['new_sms'] == 'no') $new_sms = '0';

	$new_phone = $_POST['p0'] .$_POST['p1'] .$_POST['p2'];
	$query = "UPDATE djs SET alias='$new_alias', phone='$new_phone', sms='$new_sms' WHERE drupal='$username'";
	$sql = mysql_query($query) or die("Query Failed: " . mysql_error());
	echo "Name: $name <br />
	Alias: $new_alias <br />
	Phone: $new_phone<br />";
	if($new_sms == '1') echo "Receive Text Messages: Yes<br />";
	else echo "Receive Text Messages: No<br />";

	$id = $_POST['show_id'];
	$new_desc = mysql_real_escape_string($_POST['new_desc']);
	$query = "UPDATE shows SET show_name='$new_name', show_desc='$new_desc' WHERE show_id='$id'";
	$sql = mysql_query($query) or die("Query failed : " . mysql_error());
	echo "Show Name: $new_name<br />";
	echo "Show Description: $new_desc<br />";
}
else{
echo "<form method='POST'>";

//if user has only one show, it automatically picks. otherwise, it puts a drop-down box.

$num_rows = mysql_num_rows($sql);
if($num_rows < 1){
	echo "No shows found for your account.<br />";
	$show_num = 1;
}

elseif($num_rows > 1){

echo "<br /><select name='show_id'><option value='%'>Select a Show</option>";

	while($s = mysql_fetch_array($sql, MYSQL_ASSOC)){
		$id = $s['show_id'];
		$showname = $s['show_name'];
	//echo $id ."<br />" .$showname;
	echo "<option value='$id'>$id - $showname</option>";

	}
	echo "</select><br />";
}

else{
	$s = mysql_fetch_array($sql, MYSQL_ASSOC);
	$id = $s['show_id'];
	$showname = $s['show_name'];
	$show_desc = $s['show_desc'];
	if(!$showname) $showname = "";
	//POSTs the only show id if there's no need for input
		echo "<input type='hidden' name='show_id' value='$id' />";
}



	echo "DJ Alias (If it's just your name, leave blank): <input type='text' name='new_alias' value='$alias'><br />";
	if($num_rows == 1) echo "Show Name: <input type='text' name='new_name' value='$showname'><br />";
	elseif($num_rows > 1) echo "Show Name: (Make sure this is correct!): <input type='text' name='new_name'><br />";

	if($sms = '0' || !isset($sms) || !$sms) echo "Do you want to receive occasional text messages? <input type='radio' name='new_sms' value='yes' />Yes <input type='radio' name='new_sms' value='no' checked='yes' />No<br />";
	elseif($sms = '1') echo "Do you want to receive occasional text messages? <input type='radio' name='new_sms' value='yes' checked='yes' />Yes <input type='radio' name='new_sms' value='no' />No<br />";


	echo "Phone Number: ( <input type='text' name='p0' maxlength='3' size='4' value='$p0' /> ) <input type='text' name='p1' maxlength='3' size='4' value='$p1' /> - <input type='text' name='p2' maxlength='4' size='5' value='$p2' /><br />";

	echo "Show Description<br /><textarea name='new_desc' rows='10' cols='60'>$show_desc</textarea><br />";
	echo "<br /><input type='submit' name='submit' value='Submit'></form><br />";
}
