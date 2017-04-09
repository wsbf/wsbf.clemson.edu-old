<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>WSBF Music Director Tools</title>
    <link rel="stylesheet" type="text/css" href="http://wsbf.net/wp-content/themes/rounded-grey-blog-10/style.css" />
    <link rel="stylesheet" type="text/css" href="wsbf.css" />
</head>
<?PHP

    require_once("connect.php");
?>
<table class="chart">
<tr><td class="show"><p class="show">Name / Position</p></td><td class="show"><p class="show">Office Hours</p></td><td class="show"><p class="show">Phone</p></td></tr>
<?php 
    $alternator = 0;
    if (empty($start)) {
       $start = 0;
    }
    $query = "SELECT * FROM `djs` WHERE `position`!='' and `still_here`=1 ORDER BY `sort_by`,`name` ASC";    


    $result = mysql_query($query) or die("Query failed : " . mysql_error());

    while ($dj = mysql_fetch_array($result)) {
        $position = $dj['position'];  
        $name = $dj['name'];
        $email = $dj['email'];
        $phone = $dj['phone'];
        $alternator = 1 - $alternator;
        if ($alternator == 1) {
           echo "<tr class='chartA'>";
        } else {
           echo "<tr class='chartB'>";
        }
        $alias = dj_alias($name);
        if (!empty($alias))
           $alias = ' ( ' . $alias . ' )';

           echo "<td><b>$name</b><br>$position</td>\n";

        $officehours = $dj['profile'];
        $officehours = substr($officehours,24 ,strpos($officehours, "--eoh--")-28);
        echo "<td width='200'>$officehours</td>\n";
        $email = fullyConfuse($email);
        echo "<td>$phone</td>";
        echo '</td></tr>';
    }
    echo '</table><br>';
?>
