<?php
/** SIMPLE redo of connect.php **/
/** Should, over time, move MOST scripts to this version. **/
/** In the interest of simplicity and efficiency. **/

$link = mysql_connect('new.wsbf.net') or die("Could not connect: ".mysql_error());
   mysql_select_db('wsbf', $link) or die("Could not select database");

if(!function_exists('genProfileURL')){
function genProfileURL($username) {
	if($username == '') return "#";
	$url = "#";

	$username = strtolower($username);
	$username = str_replace(' to ', '-', $username);
	$username = str_replace(' ', '-', $username);
	$username = str_replace('...', '', $username); //nolan whitman

	$url = "http://" . $_SERVER['HTTP_HOST'] . "/users/" . $username;
	return $url;
}
}
if(!function_exists('getNumConnections')){
function getNumConnections ($url) {
	$statarr=file($url);
	$nextLine = FALSE;
	$lookFor = "<td>Current Listeners";
	$prefix = "<td class=\"streamdata\">";
	$suffix = "</td>";
	$listeners = 0;
	foreach ($statarr as $line) {
		$where = strpos($line, $lookFor);
		if($where !== FALSE) {
			$nextLine = TRUE;
			continue;
		}
		if($nextLine === TRUE) {
			$content = str_replace($prefix, '', $line);
			$content = str_replace($suffix, '', $content);
			$listeners += (int)$content;
			$nextLine = FALSE;
		}
	}
	return $listeners;
}
}
?>
