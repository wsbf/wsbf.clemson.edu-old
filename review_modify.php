<?php
/** review_modify.php **/
/**  **/
/** a new implementation, by Zach Musgrave, june2010 **/

require_once("conn.php");
require_once("utils_ccl.php");
sanitizeInput();
// if(session_id() == "") session_start();
global $user; 
$username = $user->name;



define("LABEL_DEFAULT", "foobar records");
define("CDCODE_DEFAULT", "DNE");


$cssStr = "<link type='text/css' rel='stylesheet' media='all' href='http://wsbf.net/wizbif/wizbif.css'/>";
if(function_exists("drupal_set_html_head")) {
	drupal_set_html_head($cssStr);
}
else {
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en'>
<head><title>WSBF Review Modify</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
<?php echo $cssStr; ?>
</head><body>
	<?php
}

$cdcode = "";
$cdid = "";

if(isset($_GET['cdid']) || isset($_GET['cdcode'])) { // closing brace at end of file

$qc = "SELECT * FROM libcd, libartist, liblabel WHERE libcd.c_aID=libartist.aID AND libcd.c_lID=liblabel.lID AND ";
if(isset($_GET['cdcode'])) {
	$action = "EDIT";
	$cdcode = $_GET['cdcode'];
	$qc .= "libcd.cAlbumNo='$cdcode'";
	
	/** only allow seniorstaffers to edit reviews... also makes it inaccessible out of Drupal **/
	if(isset($user)) {
	global $user;
		$qS = "SELECT * FROM djs WHERE still_here=1 AND position != '' AND drupal='".$user->name."'";
		$rs = mysql_query($qS) or die(mysql_error());
		if(mysql_num_rows($rs) != 1) { echo "<h1>NOT AUTHORIZED!</h1>"; die(); }
	} else { echo "<h1>NOT AUTHORIZED!</h1>"; die(); }

	
}

elseif(isset($_GET['cdid'])) {
	$cdcode = CDCODE_DEFAULT; //does not exist
	$action = "WRITE";
	$cdid = $_GET['cdid'];
	$qc .= "libcd.cID='$cdid'";
	
}
else  { echo "cdcode/cdid are missing!"; die(); }


$rsc = mysql_query($qc) or die(mysql_error());
if(mysql_num_rows($rsc) != 1) die("SELECT did not return 1 row! <br>$qc");
$cd = mysql_fetch_array($rsc, MYSQL_ASSOC) or die(mysql_error());


$cdid = $cd['cID'];
$artist = $cd['aPrettyArtistName'];
$album = $cd['cAlbumName'];
$label = $cd['lPrettyLabelName'];
$genre = $cd['cGenre'];
$review = $cd['cReview'];
$reviewer = $cd['cReviewer'];


if(isset($_SESSION['errorMessage'])) {
	echo "<div class='error'>".$_SESSION['errorMessage']."</div>";
	unset($_SESSION['errorMessage']);
}
if(isset($_SESSION['confirmMessage'])) {
	echo "<div class='confirm'>".$_SESSION['confirmMessage']."</div>";
	unset($_SESSION['confirmMessage']);
}

//form target: http://wsbf.net/wizbif/review_backend.php

if($action == "EDIT") {
	echo "<h3 style='margin-top: 1em'><a href='adminreviews'>Edit something else</a></h3>
	<form action='http://wsbf.net/adminreviews' method='POST'>
	";
}
	
if($action == "WRITE") {

	
	if($label == LABEL_DEFAULT) $label = "";
// the following line doesn't work if the user receives an error message.
//	if($reviewer != "" || $review != "") { echo "<h1>NOT AUTHORIZED!</h1>"; die(); }

	$query = "SELECT * FROM djs WHERE drupal='$username'";
	$rs = mysql_query($query) or die(mysql_error());
	while($row = mysql_fetch_assoc($rs)){
	$reviewer = $row['name'];
	}

	echo "<form action='http://wsbf.net/submitreview' method='POST'>";
}

?>


<fieldset><legend><b>Info</b></legend>
<table>
<?php if($action == "EDIT") { ?>
	<tr>
		<td class='info'>CD Code</td>
		<td><input disabled='disabled' class='info' type='text' name='lulz' value="<?php echo $cdcode; ?>" />
			<input type='hidden' name='cdcode' value="<?php echo $cdcode; ?>" /></td>
	</tr>
<?php } if($action == "WRITE") { ?>
	<tr>
		<td class='info'>CD ID</td>
		<td><input disabled='disabled' class='info' type='text' name='lulz' value="<?php echo $cdid; ?>" />
			<input type='hidden' name='cdid' value="<?php echo $cdid; ?>" /></td>
	</tr>
<?php } ?>
	<tr><td class='info'>Artist</td>
	<td>
		<!--<input disabled='disabled' class='info' type='text' name='lulz' value="<?php echo $artist; ?>" />
		<input type='hidden' name='artist' value="<?php echo $artist; ?>" />-->
		<input type='text' class='info' name='artist' value="<?php echo $artist; ?>" />
		
	</td></tr>
	<tr><td class='info'>Album</td>
	<td>
		<!--<input disabled='disabled' class='info' type='text' name='moarlulz' value="<?php echo $album; ?>" />
		<input type='hidden' name='album' value="<?php echo $album; ?>" />-->
		<input type='text' class='info' name='album' value="<?php echo $album; ?>" />
	</td></tr>									
	<tr>
		<td class='info'><i>Self-Released?</i></td>
		<td><input class='info' type='checkbox' 
			<?php
				if($cd['lPrettyLabelName'] == "Self")
					echo "checked='checked' ";
			?>
			name ='Self' value='Self' />
		</td>
	</tr>
	<tr>
		<td class='info'>Label</td>
		<td><input class='info' type='text' name='label' value="<?php echo $label; ?>" /></td>
	</tr>
	<tr>
		<td class='info'>Genre</td>
		<td><input class='info' type='text' name='genre' value="<?php echo $genre; ?>" /></td>
	</tr>
	<tr>
		<td class='info'>Reviewer</td>
		<td><input class='info' type='text' name='reviewer' value="<?php echo $reviewer; ?>" /></td>
	</tr>

</table>
</fieldset>

<fieldset><legend><b>Tracks</b></legend>
<table>
	<tr>
		<td class='track'></td>
		<td class='title'>Title</td>
		<td class='rec'>Recom-mended<img src='http://wsbf.net/i/thumb_up.png' alt='Recommended' /></td>
		<td class='noair'>No-Air<img src='http://wsbf.net/i/delete.png' alt='No-Air (Obscene)' /></td>
	</tr>

<?php

$qt = "SELECT * FROM libtrack WHERE t_cID='$cdid' ORDER BY tTrackNo ASC";
$qtr = mysql_query($qt) or die(mysql_error());
while($tr = mysql_fetch_array($qtr, MYSQL_ASSOC)) {

	$i = $tr['tTrackNo'];
    echo "<tr>\n";
	echo "<td class='track'>$i</td>\n";
	echo "<td class='title'><input style='width: 30em' type='text' name='track$i' value=\"" . $tr['tTrackName'] . "\"/></td>\n";

	echo "<input type='hidden' name='maxtrack' value='$i' />";

	echo "<td class='rec'><input type='hidden' name='recc$i' value='0' />
	<input type='checkbox' "; 
	if($tr['tRecc'] == 1 && $tr['tClean'] == 1)
		echo "checked='checked' ";
	echo " name='recc$i' value='1' /></td>\n";
	
	echo "<td class='noair'><input type='hidden' name='noair$i' value='0' />
	<input type='checkbox' ";
	if($tr['tClean'] == 0)
		echo "checked='checked' ";
	echo " name='noair$i' value='1' /></td>\n";
	echo "</tr>\n";

}/*
if(function_exists(drupal_add_js())){
	drupal_add_js("review_word_count.js");
}*/
?>

</table></fieldset>

<script type='text/javascript' src='http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js'></script>
   <script type='text/javascript'>
     $(document).ready(function() {
		var maxlen = 700;
	$('#charLeft').text(maxlen);
         $('#ta').keyup(function() {
		
            var len = this.value.length;
            if (len >= maxlen) {
                 this.value = this.value.substring(0, maxlen);
             }
             $('#charLeft').text(maxlen - len);
         });
     });
   </script>

<fieldset><legend><b>Review</b></legend>
	<center>You have <h2><span id="charLeft"> </span></h2>characters remaining for your review.</font></center>
	<textarea id="ta" name='review' style='margin: 5px auto; width: 95%; height: 200px;'><?php echo $review; ?></textarea>


</fieldset>

<div><input class='review' type='submit' value='Submit Review' /></div>
</form>

<?php 

} //closing brace from dual isset($_GET...) if (drupal cron modification)

if(!function_exists("drupal_set_html_head")) echo "</body></html>"; ?>