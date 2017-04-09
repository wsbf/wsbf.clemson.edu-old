<?php
/* DAC 6/14/2011 - this is to update the teams */


function staffList($query) {
	$teamNames = array('P'=>'Purple Pirates', 'R'=>'Red Jaguars', 'G'=>'Green Monkeys', 'B'=>'Blue Barracudas', 'N'=>'---');
	$teamColors = array('P'=>'#601860', 'R'=>'#BF3030', 'G'=>'#308030', 'B'=>'#5566FF', 'N'=>'#505050');
	?>
	<table class="chart">
	<tr>
	<th class="show"><p class="show">Name</p></th>
	<th class="show">Team Affiliation</th>
	</tr>
	
<?php 
		//$query = "SELECT * FROM `djs` WHERE `still_here`=1 ORDER BY `sort_by`,`name` ASC";
		$result = mysql_query($query) or die("Query failed : " . mysql_error());
	
	    while ($dj = mysql_fetch_array($result)) {
			$team = $dj['team'];
			if(empty($team)) $team = 'N';
			$alias = $dj['alias'];
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
			echo "<td>";
			// team select
			echo "<select name='team'>";
			foreach($teamNames as $t=>$tname){
				if($t == $teamNames[$team])
					echo "<option value='$t' selected>$tname</option>";
				else 
					echo "<option value='$t'>$tname</option>";
			}
			echo "</select>";
			
			
			echo "</td>";
	    }
	    echo '</table>';
}





?>