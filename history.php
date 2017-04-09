<?php
require_once("wsbf.php");

if($_GET["page"] == "") {
	$query = "SELECT * FROM pages WHERE page REGEXP 'hist_*'";
    $result = mysql_query($query) or die("Query failed : " . mysql_error());
    echo "<ul>";
    while ($item = mysql_fetch_array($result)) {
        if($item['title'] != "History") {
            echo "<li><a href=\"?page=" . $item['longtitle'] . "\">" . $item['title'] . "</a></li>\n";
        }
    }
    echo "</ul>";
} else {
    $query = "SELECT * FROM pages WHERE longtitle='" . $_GET["page"] . "'";
    $result = mysql_query($query) or die("Query failed : " . mysql_error());
    $item = mysql_fetch_array($result);
    echo "<center><h2>" . $item['title'] . "</h2></center>";
    echo $item['content'];
}
?>