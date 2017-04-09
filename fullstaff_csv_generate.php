<?php
/* generates a CSV file called fullstaff_phonebook.csv in order to import into gmail, etc. w
	david cohen - 6/27/2011
*/
require_once('conn.php');
$file_name = "fullstaff_phonebook.csv";
$fp = fopen($file_name, "w");
$query = "SELECT * FROM djs WHERE still_here = 1";
$res = mysql_query($query) or die(mysql_error());

fputcsv($fp, array("Name", "E-mail address", "Mobile Phone"));
$i = 0;
while($row = mysql_fetch_assoc($res)){
	fputcsv($fp, array($row['name'], $row['email'], $row['phone']));	
	$i++;
}

echo "Done! $i contacts imported into $file_name";

?>