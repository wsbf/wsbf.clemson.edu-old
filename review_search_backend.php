<?php
// http://www.jensbits.com/2010/03/29/jquery-ui-autocomplete-widget-with-php-and-mysql/
header("Content-Type: application/json");
require_once('conn.php');
$return_arr = array();
//	$q = "SELECT * FROM libartist, libcd WHERE libcd.c_aID = libartist.aID AND libcd.cAlbumNo != '' AND libartist.aPrettyArtistName REGEXP \"$search\" ORDER BY libcd.cID DESC"

	$term = mysql_real_escape_string($_GET['term']);
//echo $term;
	$q = "SELECT * FROM libartist WHERE aPrettyArtistName LIKE '%$term%' ORDER BY aID DESC LIMIT 10";
	
	// ordering by newest first becuase those will (hopefully) be the most relevant.
	// not a very good way of doing it but alphabetical is just bullshit.
	$result = mysql_query($q);


	/* Retrieve and store in array the results of the query.*/

	while ($row = mysql_fetch_assoc($result)) {
		$row_array['id'] = $row['aID'];
		$row_array['value'] = $row['aPrettyArtistName'];
        array_push($return_arr,$row_array);
    }

/* Toss back results as json encoded array. */
echo json_encode($return_arr);
?>