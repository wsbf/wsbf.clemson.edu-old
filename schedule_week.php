<?php

require_once('conn.php');
require_once('showtable.php');

if(function_exists('drupal_add_css')) {
	drupal_add_css('wizbif/schedule.css');
}
else {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>WSBF Schedule</title>
		<link rel="stylesheet" type="text/css" href="http://wsbf.net/wizbif/schedule.css" />
</head><body>
<?php
}

/** **/
define('SMALLEST_INC', 30);
/** number of table rows, in this case based on half hours **/
define('NUM_TROWS', 48); 

/** create zeroed 2D array **/
$cells = NULL;
for($row = 0; $row < NUM_TROWS; $row++) 
	for($col = -1; $col < 7; $col++) 
			$cells[$row][$col] = 0;

/** this relies on a constant set in showtable.php **/
switch(SCHEDULE_MODE) {
	case 'SUMMER': {
		$cells[0][-1] = '1 - 4 a.m.';
		$cells[6][-1] = '4 - 7 a.m.';
		$cells[12][-1] = '7 - 10 a.m.';
		$cells[18][-1] = '10 a.m. - 1 p.m.';
		$cells[24][-1] = '1 - 4 p.m.';
		$cells[30][-1] = '4 - 7 p.m.';
		$cells[36][-1] = '7 - 10 p.m.';
		$cells[42][-1] = '10 p.m. - 1 a.m.';
	} break;
	case 'FREEFORM': {
		echo "WSBF is in freeform mode.";
		exit();
	} break;
	case 'SEMESTER': {
		$cells[0][-1] = '1 - 3 a.m.';
		$cells[4][-1] = '3 - 5 a.m.';
		$cells[8][-1] = '5 - 7 a.m.';
		$cells[12][-1] = '7 - 9 a.m.';
		$cells[16][-1] = '9 - 11 a.m.';
		$cells[20][-1] = '11 a.m. - 12:30 p.m.';
		$cells[23][-1] = '12:30 - 2 p.m.';
		$cells[26][-1] = '2 - 3:30 p.m.';
		$cells[29][-1] = '3:30 - 5 p.m.';
		$cells[32][-1] = '5 - 7 p.m.';
		$cells[36][-1] = '7 - 9 p.m.';
		$cells[40][-1] = '9 - 11 p.m.';
		$cells[44][-1] = '11 p.m. - 1 a.m.';
	} break;
	default: { echo "WTF?!?"; }
}
/** following are markings for leftmost columns (index -1) **/


$classes = NULL;
$classes[0] = 'rotation';
$classes[1] = 'specialty';
$classes[2] = 'sportstalk';
$classes[3] = 'jazz';


/** create $djs_lookup - map dj_id key to values in array. **/
/** this could be done with nested inner joins in SQL, but for clarity we are avoiding that **/
$djs_lookup = NULL;
	$q = "SELECT dj_id, name, alias, drupal FROM djs WHERE still_here=1";
	$rs = mysql_query($q) or die(mysql_error());
	while ($row = mysql_fetch_array($rs, MYSQL_ASSOC))
		$djs_lookup[$row['dj_id']] = $row;

/** create a 2D array of shows: by table row (start time), then table column (day of week) **/
$shows = NULL;
	/** sauce: http://en.wikipedia.org/wiki/Inner_join#Equi-join **/
	$qu = "SELECT * FROM shows INNER JOIN show_dj USING ( show_id )";
	$rs = mysql_query($qu) or die(mysql_error());
	while($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
		/** page is Mon to Sun; SQL is Sun to Sat **/
		$tcol = $row['day'] - 1;
		if($tcol == -1) $tcol = 6;
		
		/** first row is 1am, so transition to row number 0 **/
		/** half hour multiplier, as well **/
		$trow = ($row['start_hour'] - 1) * 2;
		/** move to a different row if not starting on the hour **/
		if($row['start_min'] != 0)
			$trow += $row['start_min'] / SMALLEST_INC;
		
		/** if the show hasn't been added to the array yet, add it. **/
		if(!isset($shows[$trow][$tcol]))
			$shows[$trow][$tcol] = $row;
		/** it's been added, and there are already 2 or more djs in it, so add another. **/
		else if(is_array($shows[$trow][$tcol]['dj_id']))
			$shows[$trow][$tcol]['dj_id'][] = $row['dj_id'];
		/** it's been added and it has 1 dj so far. make the dj_id element an array with 2 elements. **/
		else
			$shows[$trow][$tcol]['dj_id'] = array($shows[$trow][$tcol]['dj_id'], $row['dj_id']);
		
	}


/** build left-hand header cells **/ 
for($i = 0; $i < NUM_TROWS; $i++) {
	if( $cells[$i][-1] !== 0 ) {
		$rowspan = 1;
		while( isset($cells[($i+$rowspan)][-1]) && $cells[($i+$rowspan)][-1] == 0) {
			++$rowspan;
		}
		$cells[$i][-1] = "<th class='side' rowspan='$rowspan'>".$cells[$i][-1]."</th>\n";
	} else {
		$cells[$i][-1] = '';
	}
}

/** build the actual cells **/
for($col = 0; $col < 7; $col++) {
	for($row = 0; $row < NUM_TROWS; $row++) {
		if(isset($shows[$row][$col]) && $cells[$row][$col] == 0) {
			$show = $shows[$row][$col];
			
			
			$djs = '';
			//print_r($show); echo "<br><br>";
			
			if(!is_array($show['dj_id']))
				$show['dj_id'] = array($show['dj_id']);
			foreach($show['dj_id'] as &$dj) {
				
				//print_r($djs_lookup);
				$data = $djs_lookup[$dj];
				//echo $dj."\n";
				
				if($data['alias'] !== '') 
					$data['name'] = $data['alias'];
				
				if($data['drupal'] != '')
					$dj = "<a href='".genProfileURL($data['drupal']).
						"' target='_parent'>".$data['name']."</a>";
				else $dj = $data['name'];
			}
			$djs = implode('<br/>', $show['dj_id']);
			
			
			if($show['show_name'] != '')
				//$djs = "<i><a href='#' onclick=\"openDialog('".$show['show_name']."','".$show['dj_id']."','".$show['show_desc']."')\">".$show['show_name'].'</a></i><br/>'.$djs;
				$djs = '<i>'.$show['show_name'].'</i><br/>'.$djs;
			$rowspan = $show['show_length'] / SMALLEST_INC;
			$class = $classes[$show['specialty']]; // poor man's ENUM type
			$cells[$row][$col] = "\t<td class='$class' rowspan='$rowspan'>$djs</td>\n";
			
			$filler = $rowspan;
			while(--$filler > 0) {
				$cells[$row+$filler][$col] = -1;
			}
		}
	}
}

/** this pass has to be done AFTER building the cells above **/
/** add in all the filler cells and adjust their rowspans accordingly **/
for($col = -1; $col < 7; $col++) {
	for($row = 0; $row < NUM_TROWS; $row++) {
		//echo $row . "x" . $col . " | ";
		if($cells[$row][$col] === 0) {
			
			$counter = 1;
			while( isset($cells[$row+$counter][$col]) && 
					$cells[$row+$counter][$col] === 0) {
				$cells[$row+$counter][$col] = -1;
				++$counter;
				//echo "incrementing - $counter<br>";
			}
			$cells[$row][$col] = "\t<td rowspan='$counter'>&nbsp;</td>\n";
		}	
	}
	//echo "\n";
}

//exit();
?>
<div style='margin: 15px auto; width: 100%'>
<h3>For more info about a show, check out the tabs for each day.</h3>
<br />
Color key: <div class="rotation" style="display: inline;">Rotation Shows</div> 
	<div class="specialty" style="display: inline;">Specialty Shows</div> 
	<div class="sportstalk" style="display: inline;">Sports/Talk Shows</div> 
	<div class="jazz" style="display: inline;">Jazz Shows</div>
</div>
<table id='schedule'>
<tr class='side'>
	<th>&nbsp;</th>
	<th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th>
	<th>Saturday</th><th>Sunday</th>
</tr>
<?php

//print out everything. allow for filler rows.
for($i = 0; $i < NUM_TROWS; $i++) {
	echo "\n<tr>";
	for($j = -1; $j < 7; $j++) {
		if( isset($cells[$i][$j]) ) {
			if($cells[$i][$j] !== -1)
				echo $cells[$i][$j];
			//else echo "NULLITY ";
		}
	}
	echo "</tr>\n";
}

?></table>

<?php
if(!function_exists('drupal_add_css')) {
	echo "</body></html>";
}

?>
