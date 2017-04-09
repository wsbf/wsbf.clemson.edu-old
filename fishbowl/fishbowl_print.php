<?php
require_once('conn.php');

$q = "SELECT * FROM fishbowl ORDER BY average DESC";
$rs = mysql_query($q) or die(mysql_error());
$rows = mysql_num_rows($rs);

echo "<p>Total Number of entries: $rows</p>";

$bowlSize = ceil(($rows / 5));

$bowls = array();

$bowlCtr = 1;
$entryCtr = 1;

while($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
	$bowls[$bowlCtr][] = $row;
	
	
	if($entryCtr == $bowlSize) {
		$bowlCtr++;
		$entryCtr = 1;	
	}
	else {
		$entryCtr++;
	}
	
}

foreach($bowls as $num => $bowl) {
	echo "<p>Bowl number $num</p>";
	echo "<table>
		<tr>
			<td>id</td>
			<td>User</td>
			<td>Average</td>
			<td>Weight</td>
		</tr>";
	shuffle($bowl);	
	foreach($bowl as $row) {
			$username = $row['username'];
			$djquery = "SELECT * FROM djs WHERE drupal='$username'";
			$qdj = mysql_query($djquery) or die(mysql_error());
			while($dj = mysql_fetch_array($qdj, MYSQL_ASSOC)) {
				$name = $dj['name'];
			}
			if($name) echo "<tr><td>".$row['id']."</td><td>".$name."</td><td>".$row['average']."</td><td>".$row['weight']."</td></tr>";
	else echo "<tr><td>".$row['id']."</td><td>".$row['username']."</td><td>".$row['average']."</td><td>".$row['weight']."</td></tr>";
	}
	
	
	
	echo "</table>";
}

?>