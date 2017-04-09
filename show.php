<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>WSBF Music Director Tools</title>
    <link rel="stylesheet" type="text/css" href="http://wsbf.net/wizbif/schedule_style.css" />
    <link rel="stylesheet" type="text/css" href="wsbf.css" />
</head>
<?PHP

    require_once("connect.php");
	$id = $_GET['id'];
?>
<center>
<?php
    $query = "SELECT * FROM `$showtable` WHERE `show_id`='$id'";
    $result = mysql_query($query) or die("Query failed : " . mysql_error());
    $show = mysql_fetch_array($result);
    $show_name = $show['show_name'];
    $image = $show['image'];
    if (empty($show['start_hour']))
       echo "That show doesn't seem to exist. Try again in a parallel universe or something.";
    else {


    echo "<h1>$show_name</h1>";
    echo "<img src='$image'>";
    echo '<br /><b>Show description:</b><br>';
    echo $show['show_desc'];
    //echo "<br><br>[ <a href='index.php?page=archives&id=$id'> Archives for $show_name </a> ]";
    echo '<br><br>';
    
    $dj_name = $show_name;
    include('archives_flash.php');
    echo '';
    $dj_name = $show['dj_name'];
    if (strpos($dj_name, ',')) {
       while ($cutoff = strpos($dj_name, ',')) {
          $name = trim(substr($dj_name,0,$cutoff));

          // Get DJ info
          $query = "SELECT * from `djs` WHERE `name`='$name'";
          $result = mysql_query($query) or die("Query failed : " . mysql_error());
          $dj_info = mysql_fetch_array($result);
          $alias = $dj_info['alias'];
          if (empty($alias)) 
             $alias = $name;


          $links = $links . "<a class='dj' href='dj.php?name=$name'>$alias</a>";

          $links .= ' / ';
          $dj_name = substr($dj_name,$cutoff + 1);
       }
       $name = trim($dj_name);

       // Get DJ info
       $query = "SELECT * from `djs` WHERE `name`='$name'";
       $result = mysql_query($query) or die("Query failed : " . mysql_error());
       $dj_info = mysql_fetch_array($result);
       $alias = $dj_info['alias'];
       if (empty($alias)) 
          $alias = $name;


       $links = $links . "<a class='dj' href='dj.php?name=$name'>$alias</a>";

       $links .= ' / ';
       echo "<h2>DJ's: $links";
    } else {
// Get DJ info
       $query = "SELECT * from `djs` WHERE `name`='$dj_name'";
       $result = mysql_query($query) or die("Query failed : " . mysql_error());
       $dj_info = mysql_fetch_array($result);
       $alias = $dj_info['alias'];
       if (empty($alias)) 
          $alias = $dj_name;


       echo "<h2>"; //DJ: <a class='dj' href='dj.php?name=$dj_name'>$alias</a>";

       //echo ' / ';
    }
?>
Back to <a class='dj' href="scheduleztm.php">Full Schedule</a>
</h2>
</center>
<?PHP
}
?>
