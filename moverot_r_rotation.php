<?php
require_once('conn.php');

echo "<h3>Albums currently tagged as 'R' that are in active rotation</h3>";

$qu = "SELECT DISTINCT lbplaylist.pAlbumNo, lbplaylist.pArtistName, lbplaylist.pAlbumTitle FROM lbplaylist, libcd 
WHERE lbplaylist.pRotation='R' AND libcd.cAlbumNo=lbplaylist.pAlbumNo AND libcd.cBin=lbplaylist.pRotation ORDER BY pAlbumNo DESC LIMIT 0,1000";
$rs = mysql_query($qu) or die(mysql_error());

echo "<p>".mysql_num_rows($rs)." entries total.</p>";

?>
<table><tr><th>CD Code</th><th>Artist</th><th>Album</th></tr>
<?php
while($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {

	echo "<tr><td>".$row['pAlbumNo']."</td><td>".$row['pArtistName']."</td><td>".$row['pAlbumTitle']."</td></tr>";
	
}
?>
</table>