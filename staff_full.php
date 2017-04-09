<?php
//Staff-only phone book
//Includes all WSBF members (in table 'djs')

//Adapted from old-school staff.php
//Authentication removed - should be access-controlled by Drupal
//Zach Musgrave, 8 April 2010

require_once("conn.php"); //uses no old functions!

/** this function is the whole page. it should NOT be called outside this page. **/
function phoneBookList($query) {
	$teamNames = array('P'=>'Purple Pirates', 'R'=>'Red Jaguars', 'G'=>'Green Monkeys', 'B'=>'Blue Barracudas', 'N'=>'---');
	$teamColors = array('P'=>'#601860', 'R'=>'#BF3030', 'G'=>'#308030', 'B'=>'#5566FF', 'N'=>'#505050');
	?>
	<table class="chart">
	<tr>
	<th class="show"><p class="show">Name</p></th>
	<th class="show">Team Affiliation</th>
	<th class="show"><p class="show">E-mail</p></th>
	<th class="show"><p class="show">Phone</p></th>
	</tr>
	
<?php 
		//$query = "SELECT * FROM `djs` WHERE `still_here`=1 ORDER BY `sort_by`,`name` ASC";
		$result = mysql_query($query) or die("Query failed : " . mysql_error());
	
	    while ($dj = mysql_fetch_array($result)) {
			$team = $dj['team'];
			if(empty($team)) $team = 'N';
			$alias = $dj['alias'];
			$phone = $dj['phone'];
			if($phone != ''){$split = str_split($phone, 3);
			$phone = "(" .$split[0] . ") " . $split[1] . "-" . $split[2] .$split[3];
			}
		
			
			$profile = genProfileURL($dj['drupal']);
		
			echo "<tr style=' background-color:".$teamColors[$team]."'>";
			echo "<td><a href='$profile'>".$dj['name']."</a>";
			if ($alias != "") 
				echo "<br><i>$alias</i>";
			if(strlen($dj['position']) != 0)
				echo "<br><b>".$dj['position']."</b>";
			echo "</td>";
			echo "<td>".$teamNames[$team]."</td> <td>".$dj['email']."</td> <td>".$phone."</td> </tr>";
	    }
	    echo '</table>';
}

echo "<h2 style='margin: 20px 10px'>Active DJs</h2>";
phoneBookList("SELECT * FROM `djs` WHERE `active`=1 AND `still_here`=1 ORDER BY `sort_by`,`name` ASC");
echo "<h2 style='margin: 20px 10px'>Inactive DJs Still Here</h2>";
phoneBookList("SELECT * FROM `djs` WHERE `active`=0 AND `still_here`=1 ORDER BY `sort_by`,`name` ASC");


?>