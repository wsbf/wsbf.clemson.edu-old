<?php

$time = microtime(true);
require_once("conn.php");
require_once("utils_ccl.php");
require_once("review_lib.php");
require_once("dbupdate/getid3/getid3/getid3.php");
sanitizeInput();
function timeSelect($d, $c=0){
	echo "<select name = '$d'>";
	for($d = 0; $d <=23; $d++){
		//$c = 0;
		if($d == 0)
			$c = "12 AM";
		elseif($d > 0 && $d < 12)
			$c = $d . " AM";
		elseif($d == 12)
			$c = $d . " PM";
		elseif($d > 12 && $d <= 23)
			$c = ($d-12) . " PM";

		echo "<option value = '$d'>$c</option>";
	}
	echo "</select>";
}

function DateSelector($inName, $useDate=0) 
{ 
/* create array so we can name months */ 
$monthName = array(1=> "January", "February", "March", 
"April", "May", "June", "July", "August", 
"September", "October", "November", "December"); 

/* if date invalid or not supplied, use current time */ 
if($useDate == 0) 
{ 
$useDate = Time(); 
} 



/* make month selector */ 
echo "<SELECT NAME=" . $inName . "Month>\n"; 
for($currentMonth = 1; $currentMonth <= 12; $currentMonth++) 
{ 
echo "<OPTION VALUE=\""; 
echo intval($currentMonth); 
echo "\""; 
if(intval(date( "m", $useDate))==$currentMonth) 
{ 
echo " SELECTED"; 
} 
echo ">" . $monthName[$currentMonth] . "\n"; 
} 
echo "</SELECT>"; 

/* make day selector */ 
echo "<SELECT NAME=" . $inName . "Day>\n"; 
for($currentDay=1; $currentDay <= 31; $currentDay++) 
{ 
echo "<OPTION VALUE=\"$currentDay\""; 
if(intval(date( "d", $useDate))==$currentDay) 
{ 
echo " SELECTED"; 
} 
echo ">$currentDay\n"; 
} 
echo "</SELECT>"; 

/* make year selector */ 
echo "<SELECT NAME=" . $inName . "Year>\n"; 
$startYear = date( "Y", $useDate); 
for($currentYear = $startYear - 5; $currentYear <= $startYear+5;$currentYear++) 
{ 
echo "<OPTION VALUE=\"$currentYear\""; 
if(date( "Y", $useDate)==$currentYear) 
{ 
echo " SELECTED"; 
} 
echo ">$currentYear\n"; 
} 
echo "</SELECT>"; 

} 

//end date selector



$id3 = new getID3;
define('BASE_DIR', "E:\\DRSAUDIO");
define('SCRIPT_PREFIX', "http://wsbf.net/wizbif/");

sanitizeInput();

$dirCurrent = urldecode($_GET['path']); //security is above...
$cartGet = urldecode($_GET['cart']);
$cartName = explode(".", $cartGet);

if(chdir($dirCurrent) === FALSE)
	die("Error: Could not change to ".$dirCurrent."\n");
$dirCurrent = getcwd();
if(strpos($dirCurrent, BASE_DIR) === FALSE)
	header("Location: ".$_SERVER['HTTP_REFERER']);


echo "<h1>PRELIMINARY IMPORT SYSTEM</h1>\n";
echo "<h3>Import a Cart</h3>\n";
echo "<p>This page imports <b>carts.</b> K? Or go <a href='".$_SERVER['HTTP_REFERER']."'>back</a>...</p>\n";
echo "<div id='contents'>";

$cartPath = "$dirCurrent\\$cartGet";
echo "$dirCurrent\\$cartGet <br /><br />";

echo "<form method='POST' action='import_submit_cart.php'>";
echo "<table><tr><th></th><th></th>";
//the hidden input should take care of the file extension (in case it's not mp3)
echo "<tr><td>Title</td><td><input type='text' name='cartName' value='$cartName[0]' /></td></tr> <input type='hidden' name='cartExt' value='$cartName[1]' />";

echo "<tr><td>Issuer</td><td><input type='text' name='cartIssuer' value='' /></td></tr>";
echo "<tr><td>Type</td><td><input type='radio' name='cartType' value='PSA' />PSA<br /><input type='radio' name='cartType' value='UNDERWRITING' />Underwriting<br /><input type='radio' name='cartType' value='PROMOTION' />Promotion<br /><input type='radio' name='cartType' value='STATION' />Station<br /><input type='radio' name='cartType' value='Other' />Other<br /></td></tr>";

echo "<tr><td>Start Date</td>";
	echo "<td>";
	DateSelector('startDate');
	echo "</td></tr>";
echo "<tr><td>End Date</td>";
	echo "<td>";
	DateSelector('endDate');
	echo "<input type='checkbox' name='noEndDate' />No End Date</td></tr>";
echo "<tr><td>Start Time: </td><td>";
	timeSelect('startTime');	
	echo "(Leave both the same if all day)</td></tr>";
echo "<tr><td>End Time: </td><td>";
	timeSelect('endTime');
	echo "</td></tr>";

echo "</table>";

echo "<input type='hidden' name='redirect' value=\"".urlencode($_SERVER['HTTP_REFERER'])."\" /><input type='hidden' name='cartPath' value='$cartPath' /><input type='hidden' name='filename' value='$cartGet' />";
echo "<p><input type='submit' name='submit' value='Submit'></form>";
$netTime = microtime(true)-$time;
echo "<p>Current working directory: $dirCurrent</p>\n";
echo "<p>Time needed to execute: ".round($netTime,5)." seconds\n</p>";
echo "\n</div>\n";
?>
