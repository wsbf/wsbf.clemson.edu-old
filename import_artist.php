<?php

$time = microtime(true);
require_once("conn.php");
require_once("utils_ccl.php");
require_once("review_lib.php");
require_once("dbupdate/getid3/getid3/getid3.php");


$id3 = new getID3;
define('BASE_DIR', "E:\\DRSAUDIO");
define('SCRIPT_PREFIX', "http://wsbf.net/wizbif/");

sanitizeInput();

$dirCurrent = urldecode($_GET['path']); //security is above...
$artistGet = urldecode($_GET['artist']);

if(chdir($dirCurrent) === FALSE)
	die("Error: Could not change to ".$dirCurrent."\n");
$dirCurrent = getcwd();
if(strpos($dirCurrent, BASE_DIR) === FALSE)
	header("Location: ".$_SERVER['HTTP_REFERER']);


echo "<h1>PRELIMINARY IMPORT SYSTEM</h1>\n";
echo "<h3>Import a CD</h3>\n";
echo "<p>This page imports <b>one album at a time.</b> Or go <a href='".$_SERVER['HTTP_REFERER']."'>back</a>...</p>\n";
echo "<div id='contents'>";


$pattern = "$artistGet*";
$files = glob($pattern);
//echo "<pre>$pattern\n".print_r($files,TRUE)."</pre>";

$trackA = array();

$index = 1; //only relevant if track# not set
foreach($files as $file) {
	$arr = $id3->analyze($file);
	getid3_lib::CopyTagsToComments($arr);
	// print_r($arr['comments']); die();
	
	$track = $index;
	if(isset($arr['comments']['track_number'][0])) 
		$track = $arr['comments']['track_number'][0];
	
	/** account for 4/14 and similar standards **/
	if(strpos($track, "/") !== FALSE){
		$pcs = explode("/", $track);
		$track = $pcs[0];
	}
	
	$trackA[$track]['filename'] = $dirCurrent."\\".$file;
	
	if(isset($arr['comments']['album'][0])) 
		$trackA[$track]['album'] = $arr['comments']['album'][0];
	if(isset($arr['comments']['artist'][0])) 
		$trackA[$track]['artist'] = $arr['comments']['artist'][0];
	if(isset($arr['comments']['title'][0])) 
		$trackA[$track]['title'] = $arr['comments']['title'][0];
	
	if(isset($arr['comments']['genre'][0])) 
		$trackA[$track]['genre'] = $arr['comments']['genre'][0];
	else $trackA[$track]['genre'] = "";
	
	if(isset($arr['comments']['year'][0])) 
		$trackA[$track]['year'] = $arr['comments']['year'][0];
	else $trackA[$track]['year'] = "";
	
	$index++;
}

/** TODO: resolve any conflicting tags (artist, album, genre, year) **/

/** TODO: WHAT HAPPENS IF YOU HAVE A " IN A FIELD? **/


$firstT = $trackA[1];
//echo "<pre>".print_r($firstT, TRUE)."</pre>";


echo "<form method='POST' action='import_submit.php'>";
echo "<table><tr><th></th><th></th>";
echo "<tr><td>Artist</td><td><input type='text' name='artist' value=\"".$firstT['artist']."\" /></td></tr>";
echo "<tr><td>Album</td><td><input type='text' name='album' value=\"".$firstT['album']."\" /></td></tr>";
echo "<tr><td>Label</td><td><input type='text' name='label' value=\"\" /></td></tr>";

echo "<tr><td>Genre</td><td><input type='text' name='genre' value=\"".$firstT['genre']."\" /></td></tr>";
echo "<tr><td>Year</td><td><input type='text' name='year' value=\"".$firstT['year']."\" /></td></tr>";
echo "</table>";
echo "<input type='hidden' name='redirect' value=\"".urlencode($_SERVER['HTTP_REFERER'])."\" />";

/** echo "<pre>".print_r($trackA, TRUE)."</pre>"; **/



echo "<table>\n";
echo "<tr><th>Track #</th><th>Song Title</th></tr>";

/** for each track, we pass title/track#/filename ONLY! **/
foreach($trackA as $trNum => $track) {
	echo "<tr><td><input type='text' size='4' name='".$trNum."_trnum' value='".$trNum."' /></td>
			<td><input type='text' size='75' name='".$trNum."_trname' value=\"".$track['title']."\" />
			</td></tr>";
	
	echo "<tr><td colspan='2' style='font-style:italic; font-size:.75em;'>".$track['filename'].
	"<input type='hidden' name='".$trNum."_trfile' value=\"".urlencode($track['filename'])."\" />".
	"</td></tr>";		
	
}
echo "</table><p><input type='submit' name='submit' value='Submit' /></form>";



$netTime = microtime(true)-$time;
echo "<p>Current working directory: $dirCurrent</p>\n";
echo "<p>Time needed to execute: ".round($netTime,5)." seconds\n</p>";
echo "\n</div>\n";
?>