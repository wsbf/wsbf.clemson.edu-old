<?php
require_once('stream_conn.php');
require_once('utils_ccl.php');
sanitizeInput();
if($_POST){
	$email = $_POST['email'];
	//checks for correct email address
	if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
		
		echo "Please input a proper email address. <br />";
		
		echo "<form name='list' action='mailing-list-submit' method='post'><input type='email' name='email' />&nbsp;&nbsp;<input type='submit' name='submit' value='Join!' /></form>";
	}
	else{
	$addr = mysql_real_escape_string($email);
		
		$Cquery = sprintf("SELECT * FROM mailing_list WHERE email LIKE '%s'", $addr);
		$result = mysql_query($Cquery) or die(mysql_error());
		if(mysql_num_rows($result) > 0){
			echo "You're already in the system!";
		}
		else{		
		$query = sprintf("INSERT INTO mailing_list (email) VALUES('%s')", $addr);
		mysql_query($query) or die(mysql_error());
		echo "Success! You're now in the mailing list!";
	}
}
	
}
else{
	echo "Join the WSBF Mailing List! Just enter your email: <br />";
	echo "<form name='list' action='mailing-list-submit' method='post'><input type='email' name='email' />&nbsp;&nbsp;<input type='submit' name='submit' value='Join!' /></form>";
}
?>