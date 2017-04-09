<?php
/** This is almost completely ripped off from rotation_main.php **/
require_once('conn.php');
require_once('utils_ccl.php');

require_once("review_lib.php");
sanitizeInput();



/** this syntax is called HEREDOC; it is described on php.net **/
$jsStr = <<<JSSTR

//window.alert("jQuery version "+$().jquery);
/*
function stripslashes(str){
return str.replace(/\\/g, '');
}
*/

function openDialog(show_name, dj_name, show_desc) {

	$('#dialog').html("<p style='text-align:center'><img src='/misc/ajax-loader-bar.gif' alt='loading' /></p>");

	$('#dialog').dialog('open');

	if(show_name){
		$('#dialog').dialog({ title: show_name+'' });
	}
	else
		$('#dialog').dialog({ title: dj_name+'' });

$('#dialog').html(show_desc);


}

$(function() {

	$("#tabs-jqui").tabs({
		ajaxOptions: {
			error: function(xhr, status, index, anchor) {
				//function(xhr, status, errorObj) {
				window.alert("ERROR");
				$(anchor.hash).html("Error: Couldn't load this tab!");
				//$('#tabs-jqui').append("<p>Error: Couldn't load this tab!<br><pre>"+status+"</pre></p>");
			}
		}
	});

	$('#dialog').dialog({
		autoOpen: false,
		show: 'blind',
		hide: 'slide',
		width: '1000',
		position: 'center',
		buttons: { "Close": function() { $(this).dialog("close"); } }
	});


	/** buttons: { "Close": function() { $(this).dialog("close"); } } **/
});
JSSTR;

if(isset($user->name)) { // inside drupal
	drupal_add_css('misc/jquery_schedule_theme/css/custom-theme/jquery-ui-1.8.10.custom.css', 'theme');
	drupal_add_js('http://wsbf.net/misc/jquery_schedule_theme/js/jquery-1.4.4.min.js', 'theme');
	drupal_add_js('http://wsbf.net/misc/jquery_schedule_theme/js/jquery-ui-1.8.10.custom.min.js', 'theme');
	drupal_add_css('wizbif/wizbif.css', 'theme');
	drupal_add_js($jsStr, 'inline');
}
else { // NOT inside drupal; but, no authentication needed.
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en'>
	<head><title>WSBF Show Schedule</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link type='text/css' rel='stylesheet' media='all' href='http://wsbf.net/wizbif/wizbif.css'/>

		<link type="text/css" href="http://wsbf.net/misc/jquery_schedule_theme/css/custom-theme/jquery-ui-1.8.10.custom.css" rel="stylesheet" />
		<script type="text/javascript" src="http://wsbf.net/misc/jquery_schedule_theme/js/jquery-1.4.4.min.js"></script>
		<script type="text/javascript" src="http://wsbf.net/misc/jquery_schedule_theme/js/jquery-ui-1.8.10.custom.min.js"></script>
		<script type='text/javascript'><?php echo $jsStr; ?></script>
	</head><body>
<?php
}
?>
<div id='dialog' style='font-size: .75em'>Wait, what?</div>

<div id="tabs-jqui">
	<ul>
	<?php

$tabs = array("W"=>"Week",
					"1"=>"Mon",
					"2"=>"Tues",
					"3"=>"Wed",
					"4"=>"Thurs",
					"5"=>"Fri",
					"6"=>"Sat",
					"0"=>"Sun");
/*
	Camden-Catrayal Johnson's bug
		If logged in via www.wsbf.net instead of wsbf.net, the URL commented below constitutes cross-site scripting (XSS).
	This is only an issue in some browsers (notably, IE 7/8) with high security settings that block all XSS requests.
	Solutions include removing www as a 'virtual' domain or what-have-you. A judicious use of $_SERVER to build a correct
	absolute URL would work as well. But the simplest way is to make the request a relative URL, as seen below.
	OLD: http://wsbf.net/wizbif/rotation_load_category.php?bin=$bin
*/
	foreach($tabs as $tab => $name)
		echo "<li><a href='/wizbif/schedule_load_tab.php?tab=$tab'>$name</a></li>\n";
	?>
	</ul>
</div>
</body>
</html>
