<?php

//David Cohen did this, Spring Break 2010

require_once("conn.php");
//$name = "Zach Musgrave";
//	echo $path;
$xmlfile = '<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">';
if(isset($_GET['name']))
	$name = mysql_real_escape_string($_GET['name']);
if(isset($_GET['showname']))
	$showname = mysql_real_escape_string($_GET['showname']);
			
		$orig = array(" ", "&");
		$replace   = array("%20", "%26");
// EVERYTHING FOR PODCASTS OF A PARTICULAR PERSON
	if (!empty($name)) {
		$dj_name = $name;
		$query = "SELECT * FROM lbshow WHERE sDJName LIKE '%$dj_name%' AND sID > 9440 ORDER BY sID DESC";
		$result = mysql_query($query) or die("Query failed : " . mysql_error());
	
			 $djquery = "SELECT * from djs where name LIKE '%$name%'";
		     $djresult = mysql_query($djquery) or die("Query failed : " . mysql_error());
		     $stuff_arch = mysql_fetch_array($djresult);  
			$label = $stuff_arch['name'];

			$podlink = "http://wsbf.net/wizbif/podcast.php?name=" . str_replace($orig, $replace, $name);
		     $summ = substr($stuff_arch['profile'], strpos($stuff_arch['profile'], "<!--/hours-->",0)+10);
//ztm ed: only use generated $summ if the other one is empty, right?
			 if (empty($summ)) //was: !empty
		        $summ = "$name's rotation show on WSBF.";

		     $pic = $stuff_arch['image'];

			   if (!empty($label)) {
			      if ($pic == "q.jpg")
			         $pic = "http://wsbf.net/lady.jpg";
			      else
			         $pic = "http://wsbf.net/images/$pic";    
			   }		
	
$xmlfile .= "<channel>
 <title>$label</title>
 <link>$podlink</link>
 <language>en-us</language>
 <copyright>WSBF-FM Clemson</copyright>
 <itunes:subtitle>See more shows! http://wsbf.net</itunes:subtitle>
 <itunes:author>WSBF-FM Clemson</itunes:author>
 <itunes:keywords>WSBF,Clemson,88.1, $dj_name</itunes:keywords>
 <itunes:summary>$summ</itunes:summary>
 <description>$summ</description>
 <itunes:owner>
  <itunes:name>WSBF-FM Clemson</itunes:name>
  <itunes:email>computer@wsbf.net</itunes:email>
 </itunes:owner>
 <itunes:image href=\"$pic\" /> 
 <itunes:category text=\"Music\"/>
 <generator>WSBF.net</generator>
 <itunes:explicit>No</itunes:explicit> 
	 <image>
	  <url>$pic</url>
	  <title>$label</title>
	  <link>$podlink</link>
	 </image>";
	
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			
			$path = "E:\WAMP\www\\new\archpk\\".$row['sID'].".mp3";
			$url = "http://wsbf.net/archpk/".$row['sID'].".mp3";
			$date = date("M j, Y", strtotime($row['sStartTime']));
$xmlfile .= "<item><title>Show Archive $date</title><link>$podlink</link><description>$summ</description><pubDate>$date</pubDate><enclosure url='$url' length='7200' type='audio/mpeg' /><guid>$url</guid></item>\n";
	}
}

//EVERYTHING HERE IS FOR A PODCAST OF A PARTICULAR SHOW
if(!empty($showname)) {

	$query = "SELECT * FROM lbshow WHERE sShowName LIKE '%$showname%' AND sID > 9440 ORDER BY sID DESC";
	$result = mysql_query($query) or die("Query failed : " . mysql_error());

		 $showquery = "SELECT * from shows where show_name LIKE '%$showname%'";
	     $showresult = mysql_query($showquery) or die("Query failed : " . mysql_error());
	     $stuff_arch = mysql_fetch_array($showresult); 
	 	$name = $stuff_arch['dj_name'];
		$label = $showname;

		$podlink = "http://wsbf.net/wizbif/newpodcast/newpodcast.php?showname=" . str_replace($orig, $replace, $showname);
	       	 if (empty($summ)) 
		        $summ = "$showname on WSBF.";
		$desc = $stuff_arch['show_desc'];

	     $pic = $stuff_arch['image'];

		   if (!empty($label)) {
		      if ($pic == "q.jpg")
		         $pic = "http://wsbf.net/lady.jpg";
		      else
		         $pic = "http://wsbf.net/images/$pic";    
		   }
	$xmlfile .= "<channel>
	 <title>$showname</title>
	 <link>$podlink</link>
	 <language>en-us</language>
	 <copyright>WSBF-FM Clemson</copyright>
	 <itunes:subtitle>See more shows http://wsbf.net</itunes:subtitle>
	 <itunes:author>WSBF-FM Clemson</itunes:author>
	 <itunes:keywords>WSBF,Clemson,88.1, $showname</itunes:keywords>
	 <itunes:summary>$summ</itunes:summary>
	 <description>$summ</description>
	 <itunes:owner>
	  <itunes:name>WSBF-FM Clemson</itunes:name>
	  <itunes:email>computer@wsbf.net</itunes:email>
	 </itunes:owner>
	 <itunes:image href=\"$pic\" /> 
	 <itunes:category text=\"Music\"/>
	 <generator>WSBF.net</generator>
	 <itunes:explicit>No</itunes:explicit> 
		 <image>
		  <url>$pic</url>
		  <title>$label</title>
		  <link>$podlink</link>
		 </image>";

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

				$path = "E:\WAMP\www\\new\archpk\\".$row['sID'].".mp3";
				$url = "http://wsbf.net/archpk/".$row['sID'].".mp3";
				$date = date("M j, Y", strtotime($row['sStartTime']));
	$xmlfile .= "<item><title>Show Archive $date</title><link>$podlink</link><description>$summ</description><pubDate>$date</pubDate><enclosure url='$url' length='7200' type='audio/mpeg' /><guid>$url</guid></item>\n";
		}
	}



	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$path = "E:\WAMP\www\\new\archpk\\".$row['sID'].".mp3";
		$uri = "http://wsbf.net/archpk/".$row['sID'].".mp3";
		echo $path;
		if(file_exists($path)) { //" . $address . $item . " location
			
		//	$xmlfile .= "<track><annotation>".$row['sID']." ".$row['sDJName'].
		//		" - ".date("M j, Y", strtotime($row['sStartTime']))."</annotation><location>$uri</location></track>\n";
		}
	}
	

$xmlfile .= "</channel>
</rss>";
echo $xmlfile;
mysql_close($link);
?>