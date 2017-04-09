<?php

require_once("../../conn.php");
echo "<pre>";
echo "bmi_logs.php - NO DADDY NO!!!\n\n\n";

$micro = microtime(true);
$ignore = array("SID NEW", "PSA", "Promotion", "Underwriting");
$timestart = "2010-06-14 00:00:00";
$timeend = "2010-06-17 00:00:00";


function time_elapsed($prefix) {
	global $micro;
	$elapsed = microtime(true) - $micro;
	echo "\n$prefix: $elapsed\n";
}

//SELECT * FROM `lbplaylist` WHERE `pDTS` > '2010-06-14 00:00:00' AND `pDTS` < '2010-06-17 00:00:00'
//E:\cdbflite>cdbflite.exe bmi_temp\2010-06-16.dbf /select:MONTH,DAY,YEAR,ATIME,ARTIST,TITLE,TIME,CATEGORY,RECORDLABEL > bmi_temp\16.txt

function makeHeader() {
	global $timestart, $timeend;
	$ret = ", BMI COLLEGE RADIO LOG MUSIC REPORT TEMPLATE\n\n";
	$ret .= ", Call Letters: WSBF-FM,, School Name: Clemson University\n";
	$ret .= ", From: $timestart,, To: $timeend\n\n";
	
	$ret .= "Timestamp, Date Played, Time Played, Song Title, Artist Name, Duration\n";
	return $ret;
}

function lbplaylist_process(&$arr) {
	global $timestart, $timeend;
	$qu = "SELECT pDTS, pArtistName, pSongTitle FROM lbplaylist 
		WHERE pDTS > '$timestart' AND pDTS < '$timeend' ORDER BY pDTS ASC";
	$rsc = mysql_query($qu) or die(mysql_error());

	while($row = mysql_fetch_array($rsc, MYSQL_ASSOC)){
		$ti = strtotime($row['pDTS']);
		$toP = array();
	
		$toP[] = $ti;
		$toP[] = date('n-j-y', $ti);
		$toP[] = date("g:i a", $ti);
		$toP[] = str_replace(",","", $row['pSongTitle']);
		$toP[] = str_replace(",","", $row['pArtistName']);
		$toP[] = ""; //duration
	
		$arr[] = $toP;	
	}
}

function dbfdump_process($filename, &$arr) {
	global $ignore;
	$rsc = fopen($filename, "r");
	$contents = fread($rsc, filesize($filename));
	$lines = explode("\n", $contents);
	
	foreach($lines as $line) {
		$pcs = explode("|", $line);
		foreach($pcs as &$entry) $entry = trim($entry);
		
		if(count($pcs) < 7) continue;
		if(in_array($pcs[7], $ignore)) continue;

			$month = $pcs[0];
			$day = $pcs[1];
			$year = $pcs[2];
			$toformat = $pcs[3];
				$foo = explode(' ', $toformat);
				$bar = explode(':', $foo[0]);
				$hour = $bar[0];
				$minute = $bar[1];
				$second = $bar[2];
				if (count($foo) == 2) {
					if(strpos($foo[1], "PM") === FALSE && $hour == 12)
						$hour = 0;
					if(strpos($foo[1], "PM") !== FALSE)
						$hour += 12;
				}
			$time = mktime($hour,$minute,$second,$month,$day,$year);
		$toP = array();
		$toP[] = $time;
		$toP[] = date('n-j-y', $time);
		$toP[] = date('g:i a', $time);
		$toP[] = str_replace(",","", $pcs[5]);
		$toP[] = str_replace(",","", $pcs[4]);
		$toP[] = $pcs[6]; //duration
		
		$arr[] = $toP;
	}
}

function gen_csvfile($filename, &$arr) {
	
	$file = fopen($filename, "w");
	echo "\nwriting output to $filename\n";
	
	function callback($foo, $bar) {
		//print "\n".$foo[0]." | ".$bar[0];
		if($foo[0] < $bar[0]) return -1;
		else if($foo[0] == $bar[0]) return 0;
		else return 1;
	}
	
	time_elapsed("about to sort");
	uasort($arr, 'callback');
	time_elapsed("done sorting!");
	
	/**
	foreach($arr as $ndx => &$ele) {
		if($ele[5] == "" && $ndx > 0 ) {
			//duration = next_start - this_start
			print_r($ele);
			$duration = (int)($arr[($ndx+1)][0] - $ele[0]);
			
			$hours = $duration / 3600;
			$duration -= 3600 * $hours;
			$mins = $duration / 60;
			$duration -= 60 * $mins;
			$secs = $duration % 60;
			
			
			echo "\n\n\nDURATION: $duration | $hours:$mins:$secs | ".date("g:i:s", $duration);
			die();
		}
	} **/
	
	fwrite($file, makeHeader());
	time_elapsed("iterating output");
	
	foreach($arr as $ndx => $ele) {
		$str = implode(", ", $ele)."\n";
		fwrite($file, $str);
	}
	fclose($file);
	time_elapsed("output complete!");
}

$records = array();

	time_elapsed("processing lbplaylist");
lbplaylist_process($records);
	time_elapsed("processing dbf1, ".count($records)." so far");
dbfdump_process("14.txt", $records);
	time_elapsed("processing dbf2, ".count($records)." so far");
dbfdump_process("15.txt", $records);
	time_elapsed("processing dbf3, ".count($records)." so far");
dbfdump_process("16.txt", $records);
	time_elapsed("done processing, ".count($records)." total");

gen_csvfile("output.csv", $records);


?>