<?php
require_once('conn.php');
$start_date = '2011-01-01 00:00:00';
$end_date = '2011-08-24 16:00:00';

$deejays = array();

echo "<pre>\n";
echo "===== Number of CD reviews per DJ =====\n";
echo "    Report generated:\t".date('Y-m-d H:i:s')."\n";
echo "    Interval start:\t$start_date\n";
echo "    Interval end:\t$end_date\n\n";


$q = "SELECT djs.name, libaction.actUser, libaction.act_priKey FROM djs, libaction WHERE djs.active=1 AND 
 djs.drupal=libaction.actUser AND libaction.actType='WRITE' AND libaction.actWhen > '$start_date' AND libaction.actWhen < '$end_date'";


$rs = mysql_query($q) or die(mysql_error());
while($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
	$dru = strtolower($row['actUser']);
	
	if(array_key_exists($dru, $deejays)) {
		$deejays[$dru]['keys'][] = $row['act_priKey'];
	}
	else {
		$tmp = array();
		$tmp['name'] = $row['name'];
		$tmp['keys'] = array($row['act_priKey']);
		$deejays[$dru] = $tmp;
	}
	
}


/** anyone with 0 reviews won't have showed up in the above query **/
$q = "SELECT * FROM djs WHERE active=1 AND still_here=1";
$r = mysql_query($q) or die(mysql_error());
while($row = mysql_fetch_array($r, MYSQL_ASSOC)) {
	$dru = strtolower($row['drupal']);
	if(!array_key_exists($dru, $deejays)) {
		$tmp = array();
		$tmp['name'] = $row['name'];
		$tmp['keys'] = array();
		$deejays[$dru] = $tmp;
	}
} 

/** sort and then output **/
function cmp($a, $b) {
	if(count($a['keys']) == count($b['keys'])) {
		//return 0;
		return ($a['name'][strpos($a['name'], ' ')+1] <
		$b['name'][strpos($b['name'], ' ')+1]) ? -1 : 1;
	}
	return (count($a['keys']) < count($b['keys'])) ? 1 : -1;
}

uasort($deejays, 'cmp');
$out = '';
$reviews = 0;

foreach($deejays as $dj) {
	$reviews += count($dj['keys']);
	$out .= count($dj['keys']) . "\t" . $dj['name'] . "\n";
}
echo "    DJ count:\t\t".count($deejays)."\n";
echo "    Review count:\t$reviews\n";

echo "\n";
echo $out;
echo "</pre>";
?>