<?php
// WSBF Playlist
// David Bowman - created 03-25-2004
// Heavily modified by DJ Stathgar
// this php file shows the show archives

// Heavily modified, and integrated into Drupal, Zach Musgrave, November 2009

require_once("connect.php");

?><!--<h2 style='text-align:center'>Show Archive Links</h2>-->
	<form action="archive" method="post">
		<p>Filter by name/show (case-sensitive): <input type="text" name="djname"> <input type='submit' value='Filter' name='submit'></p>
	</form>

	<table class="chart" width="100%"  border="0">
	<tr class='show'><th>DJ/Show</th><th>Date</th></tr>
	
<?php
	if(isset($_POST['djname']))
		$djname = $_POST['djname'];
	else $djname = "";

	//when this script resided in www/mdtools the correct search prefix was ../archive/
	//now it is www/new/wizbif/ so the prefix is ../../archive/
      $search = "../archive/incomplete/*" . $djname . "*.mp3";
      $files1 = glob($search);

// 19jan2010 USED TO BE ../archive/*	  
      $search = "./archive/*". $djname  . "*.mp3"; 
      $files2 = glob($search);
	  
      $all_files = array_merge($files1, $files2);

      // Function courtesy of joseph.morphy@gmail.com
	  //he's dead, jim. i'm a doctor, not a magician
      function sort_by_mtime($file1,$file2) {
         $time1 = filemtime($file1);
         $time2 = filemtime($file2);
         if ($time1 == $time2) {
            return 0;
         }
         return ($time1 < $time2) ? 1 : -1;
      }

      usort($all_files,"sort_by_mtime");

      if (is_array($all_files)) {
         foreach($all_files as $item) {
            $index1 = max(strpos($item, "archive")+8, strpos($item, "incomplete")+11);
            $index2 = strpos($item, "w Archive")+10;
            if ($index2 > 0 && $index2 > 0) {
               $showdate = substr($item,$index2, strlen($item)-$index2 - 4);
// 19jan2010. was +0,-16. now -1, -15
               $djname = substr($item, $index1-1, $index2 - $index1 - 15); 
               echo "<tr>
					<td>
						<a href='http://wsbf.net/$item'>$djname</a>
					</td>
					<td>
						<a href='http://wsbf.net/$item'>$showdate</a>
					</td>
				</tr>";
            }
         }
      } 

  ?>
</table>
