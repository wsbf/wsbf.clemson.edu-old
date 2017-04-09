<?php
/** David Cohen - 7 Mar 2011	**/
/* This script allows senior staff members to add a DJ to the database as soon as s/he passes */
require_once('conn.php');
require_once('utils_ccl.php');
function djAddForm($_POST){
?>	<script language="Javascript">
	<!--
	function resetBox(box, defaultvalue) {
	  if (box.value == defaultvalue) {
	   box.value = "";
	   }
	}
	-->
	</script>
<?php
/** if the function is called with an error message **/
if(!empty($_POST)){
	foreach($_POST as $k=>$v) $$k=$v;

echo "<h2>Add a new DJ:</h2>
	<p>Note: DJ must have an account first. If not, have the new DJ create one at <a href='http://wsbf.net/user/register'>http://wsbf.net/user/register</a> on another computer.</p>
	<table>
		<tr><td></td><td></td></tr>
	<form name='dj_add' method='POST'>
		<tr>
			<td>Name:</td>
			<td>
			<input type='text' name='fname' size='12' value='$fname' />
			<input type='text' name='lname' size='12' value='$lname' />
			</td>
		</tr>
		<tr>
			<td>Email: </td>
			<td><input type='text' name='email' value='$email' /></td>			
		<tr>
			<td>Phone Number:</td>
			<td>
				( <input type='text' name='p0' maxlength='3' size='4' value='$p0' /> ) <input type='text' name='p1' maxlength='3' size='4' value='$p1' /> - <input type='text' name='p2' maxlength='4' size='5' value='$p2' /> ";
		if($sms == 'yes')
			echo "Receive Texts: <input type='radio' name='sms' value='yes' checked='yes' />Yes <input type='radio' name='sms' value='no' />No";
		else
			echo "Receive Texts: <input type='radio' name='sms' value='yes' />Yes <input type='radio' name='sms' value='no' checked='yes' />No";
	
		echo"</td>
		</tr>
		<tr>
			<td>Wsbf.net Username:</td>
			<td><input type='text' name='username' value='$username' /></td>
		</tr>
		<tr>
			<td></td>
			<td><br /><input type='submit' value='Submit' name='dj_add_submit' /></td>
		</tr>
	</table>";
}
/** if nothing is passed into here **/
else{
	echo "<h2>Add a new DJ:</h2>
	<p>Note: DJ must have an account first. If not, have the new DJ create one at <a href='http://wsbf.net/user/register'>http://wsbf.net/user/register</a> on another computer.</p>
	<table>
		<tr><td></td><td></td></tr>
	<form name='dj_add' method='POST'>
		<tr>
			<td>Name:</td>
			<td>
			<input type='text' name='fname' size='12' value='First' onfocus=\"resetBox(this, 'First')\" />
			<input type='text' name='lname' size='12' value='Last' onfocus=\"resetBox(this, 'Last')\" />
			</td>
		</tr>
		<tr>
			<td>Email: </td>
			<td><input type='text' name='email' /></td>			
		<tr>
			<td>Phone Number:</td>
			<td>
				( <input type='text' name='p0' maxlength='3' size='4' value='' /> ) <input type='text' name='p1' maxlength='3' size='4' value='' /> - <input type='text' name='p2' maxlength='4' size='5' value='' /> Receive Texts: <input type='radio' name='sms' value='yes' checked='yes' />Yes <input type='radio' name='sms' value='no' />No
			</td>
		</tr>
		<tr>
			<td>Wsbf.net Username:</td>
			<td><input type='text' name='username' /></td>
		</tr>
		<tr>
			<td></td>
			<td><br /><input type='submit' value='Submit' name='dj_add_submit' /></td>
		</tr>
	</table>
";
}

}


if(!empty($_POST['dj_add_submit'])){
/* the following line maps each $_POST['blah'] as $blah. nifty, eh?	*/
foreach($_POST as $k=>$v) $$k=$v;
$errormsg = '';
if(empty($fname))
	$errormsg .= 'Please input a first name<br />';
if(empty($lname))
	$errormsg .= 'Please input a last name<br />';
if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) 
	$errormsg .= "Please input a proper email address. <br />";
if(strlen($p0) != 3 || strlen($p1) != 3 || strlen($p2) != 4)
	$errormsg .= "Please input a proper phone number<br />";
if(empty($username))
	$errormsg .= "Please input a user name.<br />";

	if($errormsg != ''){
		echo $errormsg;
		djAddForm($_POST);
	}
	else{
	$name = "$fname $lname";
	$sort_by = "$lname";
	$phone = $p0 . $p1 . $p2;
	if($sms == 'yes')
		$sms = '1';
	else
		$sms = '0';
	$query = "INSERT INTO djs (name, email, phone, sort_by, drupal, sms) VALUES ('$name', '$email', '$phone', '$sort_by', '$username', '$sms')";
	$insert = mysql_query($query) or die(mysql_error());
	echo "DJ Added. You can now add a show under \"Show Admin.\"																																																										";
	}
}
else{
	djAddForm(NULL);
}
?>