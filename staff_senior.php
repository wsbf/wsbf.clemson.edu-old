<?php
//SENIOR staff-only phone book
//Includes ONLY people with non-empty position fields

//Adapted from old-school staff.php
//Authentication removed
//Zach Musgrave, 8 April 2010
    require_once("conn.php"); //no old functions needed!

// borrowed from james at bandit.co.nz
// http://us.php.net/manual/en/function.array-search.php
// array_search with partial matches and optional search by key
    function array_find($needle, $haystack, $search_keys = false) {
        if(!is_array($haystack)) return false;
        foreach($haystack as $key=>$value) {
            $what = ($search_keys) ? $key : $value;
			// ztm mod: used to be $what, $needle
            if(strpos($needle, $what)!==false) return $key; 
        }
        return false;
    }

/** Patched 24may10, ztm **/	
/** These arrays, and mod below, make the senior staff list show mostly wsbf.net emails. **/
/** Personal emails then may be left in the directory. **/
	
$positions = array("General Manager", "Chief Engineer", "Music Director", 
	"Production Director", "Event Coordinator", "Chief Announcer", 
	"Computer Engineer", "Promotions Director", "Member at Large");
$emails = array("program@wsbf.net", "chief@wsbf.net", "music@wsbf.net", 
	"production@wsbf.net", "events@wsbf.net", "announcer@wsbf.net", 
	"computer@wsbf.net", "promo@wsbf.net", "member@wsbf.net");
$rows = array();	

?>

<table class="chart">
<tr>
<td class="show"><p class="show">Name</p></td>
<td class="show"><p class="show">Position/Hours</p></td>
<td class="show"><p class="show">E-mail</p></td>
<td class="show"><p class="show">Phone</p></td>
</tr>


<?php 
    $alternator = 0;
    $start = 0;
 
    $query = "SELECT * FROM `djs` WHERE `position`!='' and `still_here`=1 and `active` = 1 ORDER BY `sort_by`,`name` ASC";    
    $result = mysql_query($query) or die("Query failed : " . mysql_error());

    while ($dj = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		$position = $dj['position'];  
		/** Patched 24may10, ztm **/
		$key = array_find($position, $positions);
		if($key === FALSE) $email = $dj['email'];
		else $email = $emails[$key];
		
        $name = $dj['name'];
        $phone = $dj['phone'];
	
	$profile = genProfileURL($dj['drupal']);

	if(!empty($drupal)) $profile = 'http://wsbf.net/users/'.$drupal;
	
		$hours = $dj['profile'];
		$alias = $dj['alias'];
		if($alias != "") $alias = "<br/> ($alias)";
	
		// aug 09, ztm edit. we shall enclose the hours in <!--hours--> <!--/hours-->
		$hours = substr($hours, strpos($hours, "<!--hours-->")+12);
		$hours = trim(substr($hours, 0, strpos($hours, "<!--/hours-->")));
	
        $buf = "<td><a href='$profile'>$name</a>$alias</td>";
		$buf .= "<td>$position <br/>";
		if($hours != '')
			$buf .= "<span style='font-size: 75%;'>($hours)</span>";
		$buf .= "</td>";

        $buf .= "<td>$email</td>";
        $buf .= "<td>$phone</td>";
        $buf .= '</tr>';
		
		$rows[$key] .= $buf;
    }
	
	/** ztm: interestingly, this MUST be for, not while or foreach **/
	/** $rows may contain elements 0 to n-1, but they are NOT ordered by key! **/
	/** this is because they were not set in order; PHP does not force cardinality of indices **/
	for($i=0;$i<count($rows);$i++) {
		$alternator = 1 - $alternator;
        if ($alternator) echo "<tr class='chartA'>";
		else echo "<tr class='chartB'>";
		echo $rows[$i];
	}
	
    echo '</table>';
    ?>