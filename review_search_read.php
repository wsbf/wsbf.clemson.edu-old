<?php
header("Content-Type: text/html; charset=ISO-8859-1");

if(function_exists("drupal_add_js"))
	drupal_add_js('misc/reviews.js');
else echo "<script type='text/javascript' src='http://wsbf.net/misc/reviews.js'></script>";

require_once("connect.php");
require_once("utils_ccl.php");
require_once("class.artist.php");
sanitizeInput();

$query_base = "SELECT * FROM libartist, libcd WHERE libcd.c_aID = libartist.aID AND libcd.cAlbumNo != '' AND ";
$query_tail = " ORDER BY libcd.cID DESC";


if ( isset($_POST["a"]) || (isset($_GET['artist'],$_GET['album'])) || isset($_GET['cdcode'])) {
    if(isset($_POST['a'])) {
		$search = mysql_real_escape_string($_POST["a"]);
		echo "<h2 style='text-align: center'>Search for <b>$search</b></h2><br/>\n";
		if(isset($_POST['artist']))
			$query = $query_base."libartist.aPrettyArtistName REGEXP \"$search\"".$query_tail;
		elseif(isset($_POST['album']))
			$query = $query_base."libcd.cAlbumName REGEXP '$search'".$query_tail;
		elseif(isset($_POST['reviewer']))
			$query = $query_base."libcd.cReviewer REGEXP '$search'".$query_tail;
		else die("wut.");
	}
	else {

		if(isset($_GET['artist'], $_GET['album'])) {

			$query = $query_base."libartist.aPrettyArtistName REGEXP '".$_GET['artist']."' AND libcd.cAlbumName REGEXP '".$_GET['album']."'".$query_tail;
		}
		elseif(isset($_GET['cdcode'])) {
			$query = $query_base."libcd.cAlbumNo='".$_GET['cdcode']."'".$query_tail;
		}

	}


	//$query = "SELECT * FROM `pendcddb` WHERE pArtist REGEXP '$search' AND pReview !=''";
	$result = mysql_query($query);

	echo "<h3><a href='?'>Search Again!</a></h3>";
    echo "<table>\n<tr><td width=\"20%\"></td><td></td></tr>";

	$chk = new Artist();
    while($row = mysql_fetch_array($result)){

		$currArtist = $row["aPrettyArtistName"];
/*
		if($chk->getArtistName != $currArtist){
		// skip if this artist was just checked
			$chk->setArtistName($currArtist);
			$policyCheck = $chk->policyCheck();
			if($policyCheck == 0)
				$playable = "Playable on all WSBF shows";
			elseif($policyCheck == 1)
				$playable = "Playable on specialty shows";
			elseif($policyCheck == 2)
				$playable = "Not playable on any WSBF shows";
		}
*/

        $query = "SELECT DISTINCT libtrack.tTrackNo, libtrack.tTrackName, libtrack.tClean, libtrack.tRecc FROM libtrack, libcd WHERE libtrack.t_cID = libcd.cID AND libcd.cID= \"" . $row["cID"] . "\"";
        $tracks = mysql_query($query);
        echo "<tr><td style='vertical-align:top; text-align: center'>
			<h2> ".$row["aPrettyArtistName"]."</h2>
			<h3 style='font-style: italic'>" . $row["cAlbumName"] . "<br/></h3>
			<h3 style='font-weight: bold'>".$row['cAlbumNo']."</h3>
<!--			<h3 style='font-weight: bold'>".$row['cBin']."</h3> -->
			<h3>".$playable."</h3>
			</td>";
        echo "<td><p>Genre: <b>".$row['cGenre']."</b></p>
		<p>".$row['cReview']."</p><p style='text-align: right; font-style: italic'>" . $row['cID'] . " by " . $row["cReviewer"] . "</p>";
        while($track = mysql_fetch_array($tracks)) {
            if ($track["tRecc"]) $color = "green";
            if (!$track["tClean"]) $color = "red";
            if(isset($color)) {
                echo "<span style=\"color: $color\">" . $track["tTrackNo"] . " - " . $track["tTrackName"] . "</span><br/>\n";
            } else {
                echo $track["tTrackNo"] . " - " . $track["tTrackName"] . "<br/>\n";
            }
            $color = NULL;
        }
        echo "</td></tr>\n";
    }
    echo "</table>";
} else {
?>
<form method="POST">
	Search String:
	<input type="text" name="a" id="Artist" value="" style="width: 200px;" /><br>
	<input type="submit" name='artist' value="Search By Artist" /><br>
	<input type="submit" name='album' value="Search By Album" /><br>
	<input type="submit" name='reviewer' value="Search By Reviewer" /><br>

</form>
<?php
}
?>
