<?php
// block_profile_archive.php
// zach musgrave, december 2010
// 
// this page is included in the block 'Show Archives'
// which should only show up on profile pages
// so, under 'page specific visibility settings'
// set it to 'show only on listed pages' - 'user/*'

require_once('conn.php');

// get the current username from the URL (and from the Pathauto alias)
// there is probably a better way to do this
$username = substr(strrchr($_SERVER['REQUEST_URI'], "/"), 1);
// Pathauto uses - to replace spaces in usernames
$username = str_replace('-', ' ', $username);

$query = "SELECT * FROM djs WHERE drupal LIKE '$username' LIMIT 1";
$rsc = mysql_query($query) or die(mysql_error());

if(mysql_num_rows($rsc) == 1) {
	$row = mysql_fetch_array($rsc, MYSQL_ASSOC);
	
	// binding this variable is important in the included file
	$dj_name = $print_name = $row['name'];
	if($row['alias'] != '')
		$print_name = $row['alias'];
	
	echo "<p style='text-align: center; '>";
	echo "Archives for $print_name:<br/>";
	require_once('wizbif/archives_flash.php');
	echo "</p>";
}
else {
	echo "<p>This user is not a DJ</p>";
}

?>