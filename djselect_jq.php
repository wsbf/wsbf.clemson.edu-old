<?php
// http://www.jensbits.com/2010/03/29/jquery-ui-autocomplete-widget-with-php-and-mysql/
header("Content-Type: application/json");
require_once('conn.php');
$return_arr = array();

	$term = mysql_real_escape_string($_GET['term']);
//echo $term;
	$q = "SELECT * FROM djs WHERE name LIKE '%$term%' ORDER BY name ASC";

	$result = mysql_query($q);


	/* Retrieve and store in array the results of the query.*/

	while ($row = mysql_fetch_assoc($result)) {
		foreach($row as $k=>$v)
			$row_array[$k] = $v;
		$row_array['id'] = $row['dj_id'];
		$row_array['value'] = $row['name'];
        array_push($return_arr,$row_array);
    }

/* Toss back results as json encoded array. */
echo json_encode($return_arr);
?>