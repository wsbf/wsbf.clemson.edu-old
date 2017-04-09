<?php

require_once("conn.php");
require_once("utils_ccl.php");
require_once("review_lib.php");
sanitizeInput();
global $user; //necessary to access $user->name later

/** this syntax is called HEREDOC; it is described on php.net **/
$jsStr = <<<JSSTR

//window.alert("jQuery version "+$().jquery);
function movealbum(cdcode, bin) {
	
	var user = '%%USERNAME%%'; //will be replaced before insertion
	
	//window.alert(cdcode+" "+bin + " "+user);
	$('#'+cdcode).html("<td colspan='4' style='text-align:center'><img src='/misc/ajax-loader-bar.gif' alt='loading' /></td>");
	
	$.get("/wizbif/moverot_update_bin.php", { user: user, bin: bin, cdcode: cdcode },
		function(data) {
			$('#'+cdcode).fadeOut(400);
			if(data != 'success')
				window.alert("ERROR :: "+data);
	    	
		}
	);
	
	//take it out of this table
	//$('#'+cdcode).remove();
}

$(function() {
	$("#tabs-jqui").tabs({
		ajaxOptions: {
			error: function(xhr, status, index, anchor) {
				$(anchor.hash).html("Error: Couldn't load this tab!");
			}
		}
	});
});


JSSTR;


if(isset($user->name)){ // inside drupal; no authentication needed

	drupal_add_js('http://wsbf.net/misc/jqui-182/js/jquery-1.4.2.min.js', 'theme');
	drupal_add_js('http://wsbf.net/misc/jqui-182/js/jquery-ui-1.8.2.custom.min.js', 'theme');
	
	drupal_add_css('misc/jqui-182/css/swanky-purse/jquery-ui-1.8.2.custom.css', 'theme');
	drupal_add_css('wizbif/wizbif.css', 'theme');
	$jsStr = str_replace('%%USERNAME%%', $user->name, $jsStr);
	drupal_add_js($jsStr, 'inline');
}
else { // NOT inside drupal; make sure user is current senior staff
	
	$username = '';
	if(isset($_GET['user'])) {
		$username = $_GET['user'];
		$qu = "SELECT * FROM djs WHERE still_here=1 AND position !='' AND drupal='$username'";
		$rs = mysql_query($qu) or die(mysql_error());
		if(mysql_num_rows($rs) != 1)
			die("You are not authorized to view this page.");
	} 
	else die("You are not authorized to view this page.");

	$jsStr = str_replace('%%USERNAME%%', $username, $jsStr);	
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en'>
	<head><title>WSBF Move Rotation</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
	<link type='text/css' rel='stylesheet' media='all' href='http://wsbf.net/wizbif/wizbif.css'/>
	<link rel='stylesheet' id='pricss' type='text/css' href='http://wsbf.net/misc/jqui-182/css/swanky-purse/jquery-ui-1.8.2.custom.css'></link>
	<script type='text/javascript' src='http://wsbf.net/misc/jqui-182/js/jquery-1.4.2.min.js'></script>
	<script type='text/javascript' src='http://wsbf.net/misc/jqui-182/js/jquery-ui-1.8.2.custom.min.js'></script>

	<script type='text/javascript'><?php echo $jsStr; ?></script>
	</head><body>
	<?php
	
	
}



$categories = array("R"=>"Recently Reviewed", 
					"N"=>"New Rotation", 
					"H"=>"Heavy Rotation", 
					"M"=>"Medium Rotation", 
					"L"=>"Light Rotation", 
					"O"=>"Optional [Library]");

?>
<div id="tabs-jqui"><ul>
	<?php	
	/** Same to the Camden... bug detailed in moverot_main.php
		http://wsbf.net/wizbif/moverot_load_category.php?bin=$bin
	**/
	foreach($categories as $bin => $name) 
		echo "<li><a href='/wizbif/moverot_load_category.php?bin=$bin'>$name</a></li>\n";
	?>
	</ul></div>
</body></html>