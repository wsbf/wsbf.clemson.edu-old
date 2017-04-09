<?php
/** phonebook.php
 * This page displays the phonebook of the full staff.
 * (It should only be available to members of fullstaff).
 * Re-written by David Cohen 
 */ 


require_once("stream_conn.php");

/** this function is the whole page. it should NOT be called outside this page. **/
function phoneBookList($query) {
	$teamNames = array('Purple'=>'Purple Pirates', 'Red'=>'Red Jaguars', 'Green'=>'Green Monkeys', 'Blue'=>'Blue Barracudas', 'None'=>'---');
	$teamColors = array('Purple'=>'#601860', 'Red'=>'#BF3030', 'Green'=>'#308030', 'Blue'=>'#5566FF', 'None'=>'#505050');
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
			if(empty($dj['team'])) $team = 'None';
			
			$preferred_name = $dj['preferred_name'];
			$phone = $dj['phone_number'];
			if($phone != ''){$split = str_split($phone, 3);
				// $split for 8646569723:
				// 0 => 864
				// 1 => 656
				// 2 => 972
				// 3 => 3
			$phone = "(" .$split[0] . ") " . $split[1] . "-" . $split[2] .$split[3];
			}
		
			
//			$profile = genProfileURL($dj['drupal']);
		
			echo "<tr style=' background-color:".$teamColors[$team]."'>";
			echo "<td>".$preferred_name."</td>"; 
			echo "<td>".$teamNames[$team]."</td> <td>".$dj['email_addr']."</td> <td>".$phone."</td> </tr>";
	    }
	    echo '</table>';
}
/*
echo "<form>";
$q = mysql_query("SELECT * FROM def_status");
while($row = mysql_fetch_assoc($q)){
	echo "<input type='checkbox' name='status' value='".$row['statusID']."'>".$row['status']."<br/>";

}
echo "<input type='submit'></form>";

*/
echo "<h2 style='margin: 20px 10px'>Active DJs</h2>";
phoneBookList("SELECT users.username, users.preferred_name, users.email_addr,
	users.first_name, users.last_name, users.phone_number, def_teams.team
	FROM `users`, `def_teams`, `schedule`, `schedule_hosts`
	WHERE 	schedule.active = 1
		AND schedule_hosts.scheduleID = schedule.scheduleID
		AND users.username = schedule_hosts.username 
		AND def_teams.teamID = users.teamID 
	 	AND users.phone_number IS NOT NULL
	ORDER BY users.last_name, users.first_name ASC");
//echo "<h2 style='margin: 20px 10px'>Inactive DJs Still Here</h2>";

// phoneBookList("SELECT * FROM `djs` WHERE `active`=0 AND `still_here`=1 ORDER BY `sort_by`,`name` ASC");


?>