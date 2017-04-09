<?php
// This is to do things like edit a person's show if they change their show time, etc.
// David Alexander Cohen II, bitch.
// Feb 2011
require_once('conn.php');
//if (session_id() == "") session_start();

/** Define Function to select which show you want to edit **/
function selectShow(){
	$q = "SELECT * FROM shows ORDER BY day, start_hour ASC";
	$rs = mysql_query($q) or die(mysql_error());
	echo "<form name='show_select' method='GET'>";
	echo "<br /><h2>Use this page to edit a show or add a new one.</h2>
	<p>Let me know if this doesn't work properly; I'm still testing it.</p><br />
	<h2>Edit an existing show here:</h2> <br /> <select name='show_id'><option value='%'>Select a Show (sorted by day)</option>";

	while($row = mysql_fetch_assoc($rs)){
	//		sanitize($row);
			/*
			$show_id = $row['show_id'];
			$show_name = htmlspecialchars($row['show_name']);
			$show_desc = htmlspecialchars($row['show_desc']);
			$start_hour = $row['start_hour'];
			$start_min = $row['start_min'];
			$show_length = $row['show_length'];
			*/
			foreach($row as $k=>$v) $$k=$v;
			$djq = "SELECT djs.name, djs.alias FROM djs, show_dj WHERE show_dj.dj_id = djs.dj_id AND show_dj.show_id = $show_id";
			$result = mysql_query($djq) or die(mysql_error());
	/*
	the following checks to see if alias exists, and if it does, it makes dj_name the alias
	in addition, it can handle multiple djs, separating them with commas
	making dj_name into an array and imploding with a comma puts commas in the middle but none at the end.
	*/
				$dj_name = array();
				while($djr = mysql_fetch_assoc($result)){
					if(!isset($djr['alias']) || $djr['alias'] == '')
						$dj_name[] = $djr['name'];
					else
						$dj_name[] = htmlspecialchars($djr['alias']);
				}
				$dj_name = implode($dj_name, ', ');
		echo "<option value='$show_id' name='$show_id'>$show_id - $dj_name";
		if(isset($show_name) && $show_name != ''){
			echo " - $show_name";
	}
	echo "</option>";
	}
	echo "<input type='submit' />
		</select>
		</form>
		<br /><br /><h2>Or add a show here:</h2>";

	/** include code to add a show	**/
	include('wizbif\admin_show_add.php');
}

/**	Main Edit function **/
function editShow($show_id){
	$q = "SELECT * FROM shows WHERE show_id = '$show_id'";
	$rs = mysql_query($q) or die(mysql_error());
	$row = mysql_fetch_assoc($rs);
echo "<br /><h2>Edit show:</h2><br />";
	$djq = "SELECT djs.name, djs.alias, djs.dj_id FROM djs, show_dj WHERE show_dj.dj_id = djs.dj_id AND show_dj.show_id = $show_id";
	$dj_rs = mysql_query($djq) or die(mysql_error());


/*				$dj_ids = array();
				while($dj_assoc = mysql_fetch_assoc($dj_rs)){
					$dj_ids[] = $dj_rs['dj_id'];
				}
					$dj_id_comma = implode($dj_ids, ', ');
*/

/** Add a DJ to a show: **/
echo "<form name='add_dj' method='POST'><h2>Add a DJ:</h2><select name='dj_id'><option value='%'>Select a DJ</option>";
	$q = "SELECT * FROM djs WHERE still_here = '1' ORDER BY sort_by";
	$rs = mysql_query($q) or die(mysql_error());
	while($row = mysql_fetch_assoc($rs)){
		foreach($row as $k=>$v) $$k=$v;
		/*
		$name = $row['name'];
		$sort_by = $row['sort_by'];
		$alias = $row['alias'];
		$dj_id = $row['dj_id'];
		*/
		echo "<option value='$dj_id'>$sort_by\t-\t$name";
		if($row['alias'])
			echo " ($alias)</option>";
		else
			echo "</option>";
	}
echo "</select>
	<input type='submit' name='add_dj' value='Add DJ' /></form>";


/** Delete an invdividual DJ from a show.	**/
if(mysql_num_rows($dj_rs) > 1){		// Only display if there is more than one DJ.
	?>
	<br />
	<h2>Show DJs: </h2>
	<table>
		<tr>
			<th>Name</th>
			<th></th>
		</tr>

	<?php
	$djs = array();
	while($djr = mysql_fetch_assoc($dj_rs)){
		$dj_id = $djr['dj_id'];
		$djs[$dj_id] = $djr;
		$dj_name = $djs[$dj_id]['name'];
	?>
	
	<tr>
	<form name='delete_dj' method='POST'>
		<td><?php echo $dj_name; ?></td>
		<td>
			<input type='hidden' name='dj_id' value='<?php echo $dj_id; ?>'>
			<input type='hidden' name='dj_name' value='<?php echo $dj_name; ?>' />
			<input type='hidden' name='show_id' value='<?php echo $show_id; ?>' />
			<input type='submit' name='delete_dj' value='Delete DJ' />
		</td>
	</form>
	</tr>
	<?php 
	}
	?>
	</table>
<br />
<?php
	}



/** Delete the show **/
?>
 <p><br />
	<form name='delete_show' method='POST'>
		<input type='hidden' name='show_id' value='<?php echo $show_id; ?>' />
		<input type='submit' name='delete_show' value='Delete this Show' />
	</form>
</p>

<?php
}

/** GET data is used to select a particular show 
*	The reason for this is that it will be easy to add a link to the schedule
*	page for senior staff/admins/whatever - to wsbf.net/show-admin?id=4 etm.
**/
if(!empty($_GET['show_id'])){
	$show_id = $_GET['show_id'];
	editShow($show_id);
}
else{
	selectShow();
}

/** POST data changes the data	**/
/** Add a DJ	**/
if(!empty($_POST['add_dj'])){
	$show_id = $_GET['show_id'];
	$dj_id = $_POST['dj_id'];
	$q = "INSERT INTO show_dj (show_id, dj_id) VALUES('$show_id', '$dj_id')";
	$insert = mysql_query($q) or die(mysql_error());
	echo "DJ successfully added.";
}

/** Delete a DJ	**/
elseif(!empty($_POST['delete_dj'])){
	$show_id = $_POST['show_id'];
	$dj_id = $_POST['dj_id'];
	mysql_query("DELETE FROM show_dj WHERE show_id = '$show_id' AND dj_id = '$dj_id'");
	echo $_POST['dj_name'] . " deleted.";
//	editShow($_POST['show_id']);
}

/** Delete the Show **/
elseif(!empty($_POST['delete_show'])){
	$show_id = $_POST['show_id'];
mysql_query("DELETE FROM shows WHERE show_id = '$show_id'");
mysql_query("DELETE FROM show_dj WHERE show_id = '$show_id");
	echo "Show " . $show_id . " deleted.";
	selectShow();
}



?>