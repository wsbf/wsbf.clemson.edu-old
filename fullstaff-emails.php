<?php
require_once('stream_conn.php');
//echo "<pre>";
global $user;
$username = $user->name;

//sends to current user
echo "<br /><br /><br /><br />";
/*
$newlink = "<a href='mailto:";

$query = "SELECT email FROM djs WHERE drupal LIKE '$username' LIMIT 1";
$result = mysql_query($query) or die(mysql_error());
while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	$e = $row['email'];
	$newlink .= "$e?bcc="; 
}
*/
echo "<h1>FULLSTAFF EMAIL</h1><h2>Copy and paste the following into <i><b><u>BCC</u></b></i> of your email!</h2> <br /><br /><p>(This includes users that are listed as Active, SemiActive, or Intern)</p>";

$qu = "SELECT email_addr FROM users WHERE statusID IN (0,1,5) ORDER BY last_name ASC";
$rs = mysql_query($qu) or die(mysql_error());

while($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
	$staffemail = $row['email_addr'].", ";	
	$newlink .= $staffemail;
}

//$newlink .="'>Use this link to email fullstaff!</a>";

echo $newlink;

?>                                                        