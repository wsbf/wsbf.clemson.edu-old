<center><h3>Current DJs with Shows</h3></center>
<?php
include("connect.php");
$query = "SELECT djs.name, djs.email FROM djs, shows WHERE shows.dj_name REGEXP djs.name";
$result = mysql_query($query);
while($dj = mysql_fetch_array($result)){
    echo "<b>" . $dj["name"] . "</b>: " . $dj["email"] . "<br/><br/>\n";
}
?>
