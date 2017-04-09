<?PHP
	/** 99.9999999% sure this is archaic **/
   if (empty($id)) {
      $query = "select * from `djs` where `name`='$dj_name'";
      $result = mysql_query($query) or die("Query failed : " . mysql_error());
      $stuff_arch = mysql_fetch_array($result);
      $label = $stuff_arch['name'];
      echo "<!-- EEE $label -->";
      $podcastURL = "http://wsbf.net/podcast.php?name=$dj_name";
   } else {
      $query = "select * from `$showtable` where `show_id`='$id'";
      $result = mysql_query($query) or die("Query failed : " . mysql_error());
      $stuff_arch = mysql_fetch_array($result);
      $label = $stuff_arch['show_name'];
      $podcastURL = "http://wsbf.net/podcast.php?id=$id";
      if (empty($label))
         $label = $stuff_arch['dj_name'];
   }
   if (!empty($label)) {
      echo "<b>Archives for $label:</b><br>";

      $oldest = getOldest();
      $count = 0;


      $search = "archive/incomplete/*" . $label . "*.mp3";
      $folders = glob($search);
      if (is_array($folders)) {
         foreach($folders as $item) {
            if (filemtime($item) > $oldest) {
               $text = substr($item, strrpos($item, "-")+2);
               echo "<a href='$item'>$text</a><br>\n";

               $count++;
            }
         }
      } 

      $search = "archive/*" . $label . "*.mp3";
      $folders = glob($search);
      if (is_array($folders)) {
         foreach($folders as $item) {
            if (filemtime($item) > $oldest) {
               $text = substr($item, strrpos($item, "-")+2);
               echo "<a href='$item'>$text</a><br>\n";

               $count++;
            }
         }
      } 


      if ($count == 0) {
         echo 'No archives yet for this show.';
      }
     
   }
   $podcastURL = str_replace(" ", "%20",$podcastURL);
   echo "<br><b>New!</b> Podcast feed URL: $podcastURL [ <a href='index.php?page=podcastinfo'>What's this?</a> ]";
?>