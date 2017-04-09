<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>WSBF Music Director Tools</title>
    <link rel="stylesheet" type="text/css" href="http://wsbf.net/wizbif/schedule_style.css" />
    <link rel="stylesheet" type="text/css" href="wsbf.css" />
</head>

<?php

    require_once("connect.php");
?>

<?php
    // This PHP file is meant to be inserted in the page
    // by index.php
    // Created by Thomas Davidson
    $name = $_GET['name'];
    $cat = 'about';
    if (empty($name))
       $name = 'Dark Helmet';
    $query = "SELECT * FROM djs WHERE name='$name'";
    //echo $query ."</br>";
    $result = mysql_query($query) or die("Query failed : " . mysql_error());
    $dj = mysql_fetch_array($result);
    $image = $dj['image'];
?>
<center>
<?php
    // Print out name/alias, omit real name if profile is marked with an asterisk
	// This was added by request to hide someone's real name from the public
    if (substr($dj['profile'], 0, 1) == "*") {
       echo '<h1>' . $dj['alias'] . '</h1>';
       $dj['profile'] = substr($dj['profile'], 1);
    } else {
       if (!empty($dj['alias']))
          $alias = ' ( ' . $dj['alias']. ' )';
	   else $alias = "";
       
	   echo "<b>$name$alias</b>";
    }

    if (empty($dj['name'])) {
       ?>
       <br>Nonexistent person<br><br>
       <table background='BG2.gif' style='border: 1px solid white' cellpadding='12'><tr><td><img src='images/q.jpg' style='border: 1px solid white'></td><td class='description'>
       <? echo $name; ?> is your father's brother's nephew's cousin's former roommate. No, actually he's not a real WSBF person at all. What were you thinking?
       </td></tr></table>
       <?PHP
    } else {
       if (!empty($dj['position'])) {
          echo '<br>' . $dj['position'] . '<br><br>';
       }
	   
	   if (strlen($image) > 5)
		  echo "<br /><img src='http://wsbf.net/images/$image' style='border: 1px solid white'><br />";
	   
	   echo "<br /><b>About:</b><br /></center>";
	   echo "<div style='margin-left:auto; margin-right:auto; width:50%'>";
       //echo substr($dj['profile'],strpos($dj['profile'], "--eoh--")+8);
	   echo substr($dj['profile'],strpos($dj['profile'], "<!--/hours-->")+13);
	   echo "</div>";
	   echo "<center>";
       $dj_name = $dj['name'];
       echo '<br>';
                
       include('archives_flash.php');

       echo '';

       if (!empty($links)) {
          $links = 'Show(s): ' . $links . ' / ';
       }
    }
	if(isset($links))
		echo "<h2>$links";

?>
</h2>
</center>
