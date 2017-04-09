<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
session_start();
require_once($_SERVER["DOCUMENT_ROOT"] . "PFBC/Form.php");
//require_once('class.artist.php'):
//require_once('functions.string.php');
?>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en'>
<head><title>WSBF Music Policy Check</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link type='text/css' rel='stylesheet' media='all' href='/wizbif/wizbif.css'/>


	<script type='text/javascript'>
//http://www.ibm.com/developerworks/opensource/library/os-php-jquery-ajax/index.html
/*
	$(document).ready(function(){
	$("#search_results").slideUp();
		$("#search_button").click(function(e){
			e.preventDefault();
			ajax_search();
		});
	Can't do this on keyup or will hit billboard rate limiter
	$("#artistName").keyup(function(e){
			e.preventDefault();
			ajax_search();
		});


	});
*/
	function policySearch(){
//	  $("#search_results").show();
	  var search_val=$("#artistName").val();
	  $.post("./artist_policy_backend.php", {artistName : search_val}, function(data){
	   if (data.length>0){
		 $("#search_results").html(data);
	   }
	   else{
	     $("#search_results").html('');
	   }
	  })
	}
	</script>
	</head><body>
	<h1>Check Artist for Music Policy:*</h1>
<p style='font-size: 10pt;'>*This is still under development, and may not work. You have been warned.</p>
<?php



$form = new Form("ajax", 500);



$form->configure(array(
    "ajax" => 1,
    "ajaxCallback" => "policySearch",
));
$form->addElement(new Element_Hidden("form", "ajax"));
$form->addElement(new Element_Textbox("Artist:", "artistName", array(
	"id" => "artistName",
	"name" => "artistName"
	)));
$form->addElement(new Element_Button);
$form->addElement(new Element_HTMLExternal('<div id="search_results"></div>'));




$form->render();
?>

</body>
	</html>
