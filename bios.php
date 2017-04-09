<?php
    require_once("connect.php");
    // This PHP file is meant to be inserted in the page
    // by index.php
    // Created by Thomas Davidson

    $biographies = '';
    $alternator = 0;
    $query = 'SELECT * from `alumni` WHERE `checked`=1';
    $result = mysql_query($query) or die("Query failed : " . mysql_error());

    while ($item = mysql_fetch_array($result)) {
        $name = $item['name'];
        $years = $item['years'];
        $location = $item['location'];
        $positions = $item['positions'];
        $bio = $item['bio'];
        
        // Do top links
        echo " [<a href='#$name'>$name</a>]";
        // Build biographies text
        $biographies .= "<tr><td class='show'><h2><p class='show'><a style='color:white' name='$name'>$name:</a></h2></p></td></tr>";

        $alternator = 1 - $alternator;
        if ($alternator == 1) {
           $biographies .= "<tr class='chartA'>";
        } else {
           $biographies .= "<tr class='chartB'>";
        }
        $biographies .= "<td><b>Positions held ($years):</b> $positions<br>\n";
        $biographies .= "<b>Location:</b> $location<br>\n";
        $biographies .= "<p style='text-indent: 3em'>$bio</p><p style='text-indent: 3em'>";
        
        if ($item['publicemail'] == 1) {
           $biographies .= '<b>Email: </b>[ ' . fullyConfuse($item['email']) . ' ] ';
        } 
        if ($item['publicweb'] == 1) {
           $website = $item['website'];
           $biographies .= "<b>Website: </b>[ <a href='$website'>$website</a> ] ";
        }

        $biographies .= '</p>';
        if ($item['publicemail'] + $item['publicweb'] > 0)
           $biographies .='<br>';

        $biographies .= '</td></tr>';
    }
	echo "</center>";
    echo '<br><br>';
    echo $biographies;
?>