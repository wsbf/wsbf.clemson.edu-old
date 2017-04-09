<?php
require_once('conn.php');
require_once('utils_ccl.php');
sanitizeInput();

if(!isset($_POST['listserv'])) {
	?>
	<form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='POST'>
		<p>Paste the contents of a REVIEW WSBF_FULLSTAFF-L below.</p>
		<textarea name='listserv' rows='25' cols='50'></textarea>
		<p><input type='submit' name='submit' /></p>
	</form>
	<?php
}
else { //closing brace at end
	

echo "<pre>";

//dated 4nov10
$review_out = $_POST['listserv'];

$listserv_contents = explode('\r\n', $review_out);
foreach($listserv_contents as &$line)
	$line = strtolower(substr($line, 0, strpos($line, " ")));

$qu = "SELECT * FROM djs WHERE still_here=1";
$rs = mysql_query($qu) or die(mysql_error());

echo "\n\nSUMMARY OF COMPARISON\n";
echo "\tEntries in Listserv: ".count($listserv_contents)."\n";
echo "\tEntries in wsbf.djs: ".mysql_num_rows($rs)."\n";

echo "\nAddresses in wsbf.djs but not in Listserv\n\n";
$ctr = 0;

// why does jarrett lucero come up??
//foreach($listserv_contents as $addr)
//	if($addr[0] == 'j') echo $addr."\n";

$prefix = "QUIET ADD WSBF_FULLSTAFF-L";

while($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
	//$djs_contents[] = strtolower($row['email']);
	
	if(!in_array($row['email'], $listserv_contents)) {
		echo $prefix . " ".$row['email'] ." ". $row['name'] ."\n";
		++$ctr;
	}
	
}
echo "\n\tTotal: $ctr\n\n";



$rs = mysql_query($qu) or die(mysql_error());

echo "\nAddresses in Listserv but not in (current) wsbf.djs\n\n";
$ctr = 0;

$djs_arr = array();
while($row = mysql_fetch_array($rs, MYSQL_ASSOC))
	$djs_arr[] = $row['email'];

$nonclemson = array();
foreach($listserv_contents as $ldj) {
	if(!in_array($ldj, $djs_arr)) {
		if(strpos($ldj, "clemson") !== FALSE)
			echo $ldj ."\n";
		else $nonclemson[] = $ldj;
		++$ctr;
	}
}
echo "\n\n";
foreach($nonclemson as $dj) echo $dj."\n";

echo "\n\tTotal: $ctr\n\n";

} //closing brace

?>
