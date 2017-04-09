<?php
require_once("connect.php");
$xmlfile = '<?xml version="1.0" encoding="UTF-8" ?><playlist version="1" xmlns="http://xspf.org/ns/0/"><title>Archive Playlist</title><info>http://www.jeroenwijering.com/?item=Flash_MP3_Player</info><trackList>';

if (!empty($dj_name)) {
	$query = "SELECT * FROM lbshow WHERE sDJName LIKE '%$dj_name%' AND sID > 9440 ORDER BY sID DESC";
	$result = mysql_query($query) or die("Query failed: " . mysql_error());
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		$path = "E:/WAMP/www/new/bc_china/archpk/".$row['sID'].".mp3";
		$uri = "http://wsbf.net/bc_china/archpk/".$row['sID'].".mp3";
		//echo $path;
		if(file_exists($path)) { //" . $address . $item . " location
			$xmlfile .= "<track><annotation>".$row['sID']." ".$row['sShowName'].
				" - ".date("M j, Y", strtotime($row['sStartTime']))."</annotation><location>$uri</location></track>\n";
		}
	}
}
if(!empty($id)) {

	$query = "SELECT * FROM lbshow WHERE show_id='$id' ORDER BY sID DESC";
	$result = mysql_query($query) or die("Query failed : " . mysql_error());
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$path = "E:/WAMP/www/new/bc_china/archpk/".$row['sID'].".mp3";
		$uri = "http://wsbf.net/bc_china/archpk/".$row['sID'].".mp3";
		//echo $path;
		if(file_exists($path)) { //" . $address . $item . " location
			
			$xmlfile .= "<track><annotation>".$row['sID']." ".$row['sDJName'].
				" - ".date("M j, Y", strtotime($row['sStartTime']))."</annotation><location>$uri</location></track>\n";
		}
	}
	
}

//echo $xmlfile;
//die();

   if (empty($id)) {
      $query = "select * from `djs` where `name`='$dj_name'";
      $result = mysql_query($query) or die("Query failed : " . mysql_error());
      $stuff_arch = mysql_fetch_array($result);
      $label = $stuff_arch['name'];
      echo "<!-- EEE $label -->";
      $podcastURL = "http://wsbf.net/wizbif/podcast.php?name=$dj_name";
   } else {
      $query = "select * from `$showtable` where `show_id`='$id'";
      $result = mysql_query($query) or die("Query failed : " . mysql_error());
      $stuff_arch = mysql_fetch_array($result);
      $label = $stuff_arch['show_name'];
      $podcastURL = "http://wsbf.net/wizbif/podcast.php?name=$dj_name";
      if (empty($label))
         $label = $stuff_arch['dj_name'];
$podcastURL = "http://wsbf.net/wizbif/podcast.php?showname=$label";
   }
   if (!empty($label)) {
	$current_dir = getcwd();
	$address = "http://wsbf.net/";
	chdir('E:\WAMP\www\new\\');
      //echo "<b>Archives for $label:</b><br>";
	  
	  $oldest = getOldest();
      $count = 0;
	  //$xmlfile used to be started here
      /**
	  $search = "archive/incomplete/*" . $label . "*.mp3";
      $folders = glob($search);
      if (is_array($folders)) {
         foreach($folders as $item) {
            //if (filemtime($item) < $oldest) {
               $text = substr($item, strrpos($item, "-")+2);
               $xmlfile .= "<track><annotation>$text</annotation><location>" . $address . $item . "</location></track>\n";

               $count++;
            //}
         }
      } 
		**/
      $search = "archive/*" . $label . "*.mp3";
      $folders = glob($search);
      if (is_array($folders)) {
         foreach($folders as $item) {
//	echo $item . "<br />";
            //if (filemtime($item) < $oldest) {
               $text = substr($item, strrpos($item, "-")+2);
               $xmlfile .= "<track><annotation>$text</annotation><location>" . $address . $item . "</location></track>\n";

               $count++;
            //}
         }
      } 
	  
	  $xmlfile .= "</trackList></playlist>";
	//echo "XML: " . $xmlfile;
	$filename = "E:\WAMP\www\\new\mp3player\\".$label.".pl.xml";
	//echo $filename;
	  $handle = fopen($filename , 'wb');
	  fwrite($handle,$xmlfile);
	  fclose($handle);

	  $label = str_replace(" ", "%20", $label);
	  
	?>
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="360" height="160" id="mp3player" 
	    codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" >
	  <param name="movie" value="http://wsbf.net/mp3player/mp3player.swf" />
	  <param name="flashvars" value="config=http://wsbf.net/mp3player/config.xml&file=http://wsbf.net/mp3player/<?PHP echo $label; ?>.pl.xml" />
	  <embed src="http://wsbf.net/mp3player/mp3player.swf" width="360" height="160" name="mp3player"
	    flashvars="config=http://wsbf.net/mp3player/config.xml&file=http://wsbf.net/mp3player/<?PHP echo $label; ?>.pl.xml" 
	    type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
	<?php
	chdir($current_dir);
   }
   $podcastURL = str_replace(" ", "%20",$podcastURL);
//   echo "<br>Podcast Feed: (<a href='http://wsbf.net'>What's this?</a>)";

//	echo "<br><a href=\"$podcastURL\" target=\"_blank\">$podcastURL</a>";
?>
