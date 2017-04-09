<?PHP
require_once("connect.php");

$maxListeners = 100;

// --For shoutcast--
// Get number of listeners
//$file="f:\\inetpub\\wwwroot\\shoutcast.txt";
//$fd=fopen($file,"r");
//$listeners=fread($fd,filesize($file));
//fclose($fd);

// For IceCast
//$low_lost = $hi_list = 0;
// Get number of listners on low-fi stream

// Get number of listners on low-fi stream
$statarr=file("http://stream.wsbf.net:8000/status.xsl");



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
		//echo "CONTENT IS $content\n\n";
		$nextLine = FALSE;
	}
	
}

/**
if (!empty($statarr)) {
	$statusfile = $statarr[11];
	$start = strpos($statusfile, "Listeners:") + 38;
	@$end = strpos($statusfile, "<", $start);
	$low_list = substr($statusfile, $start, $end - $start);
}

// Get number of listners on high-fi stream
$statarr=file("http://wsbf.net:8000/status.xsl");
if (!empty($statarr)) {
	$statusfile = $statarr[11];
	$start = strpos($statusfile, "Listeners:") + 38;
	@$end = strpos($statusfile, "<", $start);
	$hi_list = substr($statusfile, $start, $end - $start);
}

$listeners = $low_list + $hi_list;
**/
?>
<?php echo($listeners); // out of <?php echo($maxListeners);
?>
