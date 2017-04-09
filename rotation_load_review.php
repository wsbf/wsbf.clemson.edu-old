<?php

require_once('conn.php');
require_once('review_lib.php');
require_once('utils_ccl.php');
sanitizeInput();

if(isset($_GET['cdcode'])) {
	$query = "SELECT * FROM libartist, libcd WHERE libcd.c_aID = libartist.aID AND 
		libcd.cAlbumNo='".$_GET['cdcode']."' ORDER BY libcd.cID DESC";
	$result = mysql_query($query) or die(mysql_error());
	
	
    while($row = mysql_fetch_array($result)){
	
        $query = "SELECT DISTINCT libtrack.tTrackNo, libtrack.tTrackName, libtrack.tClean, libtrack.tRecc 
			FROM libtrack, libcd WHERE libtrack.t_cID = libcd.cID AND libcd.cID='".$row["cID"]."'";
        $tracks = mysql_query($query) or die(mysql_error());
       	//$row['cAlbumNo']." ".
		echo "<h2 style='text-align: center'>".$row["aPrettyArtistName"]."</h2>\n
			<h3 style='text-align: center; font-style:italic'>" . $row["cAlbumName"] . "</h3>\n";
        
		//nl2br() adds a <br/> before every newline
		echo "<p>".nl2br(nl2br($row['cReview']))."</p>
			<p style='font-style: italic; text-align: right'>by ".$row["cReviewer"]."</p>";
		
		
		echo "<table>";
        while($track = mysql_fetch_array($tracks)) {
            if ($track["tRecc"]) $color = "green";
            if (!$track["tClean"]) $color = "red";
            if(isset($color))
                echo "<tr style='color: $color'><td>".$track["tTrackNo"]."</td>
						<td>".$track["tTrackName"]."</td></tr>\n";
            else
                echo "<tr><td>".$track["tTrackNo"]."</td><td>".$track["tTrackName"]."</td></tr>\n";
            
            $color = NULL;
        }
		echo "</table>";
    }
	
}
else echo "ERROR";


?>