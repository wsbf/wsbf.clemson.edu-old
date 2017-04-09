<?php
/** 
	THIS CURRENTLY WORKS ONLY WITH MODIFYING REVIEWS. SUBMITTING REVIEWS GOES TO REVIEW_SUBMIT.PHP
	
	review_backend.php - Zach Musgrave (ztm) - June 2010 
	This takes POSTed input from review_modify.php
	and updates the database accordingly. Care is taken to sanitize correctly and 
	check inputs well.
	
	
**/
	//echo "<pre>"; print_r($_POST); echo "</pre>";
global $user;
require_once("conn.php");
require_once("utils_ccl.php");
require_once("review_lib.php");
sanitizeInput();
if(session_id() == "") session_start();
ob_start();

$mode = "LIST";
$error = "";
$uris = genUriStruct($_SERVER['HTTP_REFERER']);


if(isset($_POST['cdcode'])) {
	$cdcode = $_POST['cdcode'];
	$mode = "UPDATE";
	$uris = updateUriStruct($uris, "cdcode", $cdcode);
}

else if (isset($_POST['custom'])) {
	$mode = "CUSTOM";
}
/*
if($mode != "INSERT" && $mode != "CUSTOM") {
	/** only allow seniorstaffers to edit reviews... also makes it inaccessible out of Drupal *\/
	global $user; if(isset($user)) {
		$qS = "SELECT * FROM djs WHERE still_here=1 AND position != '' AND drupal='".$user->name."'";
		$rs = mysql_query($qS) or die(mysql_error());
		if(mysql_num_rows($rs) != 1) die("NOT AUTHORIZED!");
	} else die("NOT AUTHORIZED!");
}
*/
if($mode != "LIST") {
	if(strlen($_POST['artist']) < 1)
		$error .= "Enter an artist name.<br/>";
	if(strlen($_POST['album']) < 1)
		$error .= "Enter an album name.<br/>";
	if(strlen($_POST['label']) < 1 && $_POST["Self"] != "Self")
		$error .= "Enter a label name, or click 'self-released'.<br/>";
	if(strlen($_POST['label']) < 1 && $_POST["Self"] == "Self")
		$_POST["label"] = $_POST["Self"];
	if(strlen($_POST['genre']) < 1)
		$error .= "Enter a genre, or multiple genres.<br/>";
	if(strlen($_POST['review']) < 1)
		$error .= "Enter a review. Asshat.<br/>";
	if(strlen($_POST['reviewer']) < 1)
		$error .= "Enter your name as the reviewer.<br/>";
		
	$m = $_POST['maxtrack'];
	$ctr = 0;
	for($i = 1; $i <= $m; $i++){
		$recc = $_POST["recc$i"];
		$ctr = $ctr + $recc;
	}
	if($ctr == 0)
		$error .= "You haven't selected any recommended tracks. Please select the most outstanding tracks.";
	
}

//We redirect to the prior page if there are errors.
if($error != "") {
	$_SESSION['errorMessage'] = $error;
	header("Location: ".useUriStruct($uris));
}

$nArtist = $_POST['artist'];
$nAlbum = $_POST['album'];
$nLabel = $_POST['label'];
$nGenre = $_POST['genre'];
$nReviewer = $_POST['reviewer'];
$nReviewText = $_POST['review'];

switch($mode) { case "UPDATE": {
	$toUpdate = array(); //fill this up with values to be changed
	
	$q = "SELECT * FROM libcd WHERE cAlbumNo='$cdcode'";
	$rs = mysql_query($q) or die(mysql_error());
	if(mysql_num_rows($rs) !== 1) 
		die("\nError: 0 or duplicate cdcode! $cdcode");
	$rs = mysql_fetch_array($rs, MYSQL_ASSOC);
	$cdid = $rs['cID']; //c_lID c_aID
	$labelid = $rs['c_lID'];
	$artistid = $rs['c_aID'];

	/** ARTIST **/
	$qa = "SELECT * FROM libartist WHERE aID='$artistid'";
	$rsa = mysql_query($qa) or die(mysql_error());
	$rsa = mysql_fetch_array($rsa, MYSQL_ASSOC);
	if(strcmp($rsa['aPrettyArtistName'], unsafeCmp($nArtist)) != 0) {
		$aa = artistCheck($nLabel);
		if($aa === FALSE)
			$aa = insertArtist($nArtist);
		$toUpdate['c_aID'] = $aa;
	}
	
	/** ALBUM **/
	if(strcmp($rs['cAlbumName'], unsafeCmp($nAlbum)) != 0)
		$toUpdate['cAlbumName'] = $nAlbum;
	
	/** LABEL **/
	$ql = "SELECT * FROM liblabel WHERE lID='$labelid'";
	$rsl = mysql_query($ql) or die(mysql_error());
	$rsl = mysql_fetch_array($rsl, MYSQL_ASSOC);
	if(strcmp($rsl['lPrettyLabelName'], unsafeCmp($nLabel)) != 0) {
		$ll = labelCheck($nLabel);
		if($ll === FALSE)
			$ll = insertLabel($nLabel);
		$toUpdate['c_lID'] = $ll;
	}
	
	/** todo? :: cYear, cPromoter  **/
	
	/** REVIEW, REVIEWER, GENRE **/
	if(strcmp($rs['cReview'], unsafeCmp($nReviewText)) != 0)
		$toUpdate['cReview'] = $nReviewText;
	if(strcmp($rs['cReviewer'], unsafeCmp($nReviewer)) != 0)
		$toUpdate['cReviewer'] = $nReviewer;
	if(strcmp($rs['cGenre'], unsafeCmp($nGenre)) != 0)
		$toUpdate['cGenre'] = $nGenre;
	
	/** cBin: Leave. That's what Move Rotation is for. **/

	/** Update libcd! **/
	if(!empty($toUpdate)) {
		$qCdU = "UPDATE libcd SET ";
		$tmp = array();
		foreach($toUpdate as $key => $val)
			$tmp[] = "$key='$val'";
		$tmp = implode(", ", $tmp);
		$qCdU .= $tmp;
		$qCdU .= " WHERE cID='$cdid'";
		
		global $user;
		addAction($user->name, 'libcd', $cdid, 'EDIT', serialize($toUpdate));
		mysql_query($qCdU) or die("\nlibcd update failed: ".mysql_error());
	}
	
	/** libtrack - assume the # of tracks (and IDs) will not change **/
	/** Disregarding tDisc, assuming all entries have 1 disc **/
	$q = "SELECT * FROM libtrack WHERE t_cID='$cdid' ORDER BY tTrackNo ASC";
	$rst = mysql_query($q) or die(mysql_error());
	while($tRow = mysql_fetch_array($rst, MYSQL_ASSOC)) {
		//$tRow tTrackName tClean tRecc
		$trackUpdate = array();
		$trNum = $tRow['tTrackNo'];
		
		//read this page to understand why all checkboxes should be always set
		// http://www.felgall.com/xtutf06a.htm
		if(!isset($_POST["track$trNum"], $_POST["recc$trNum"], $_POST["noair$trNum"]))
			die("\nFatal error: Track name for #$trNum not given.");
		
		//make compatible with SQL schema (checkboxes aren't set if left blank)
		$reccIn = (int)$_POST["recc$trNum"];
		$cleanIn = (int)$_POST["noair$trNum"] ? 0 : 1; //inverse
		
		//echo "$trNum: recc $reccIn, clean $cleanIn<br>";
		//continue;
		
		if($tRow['tClean'] != $cleanIn)
			$trackUpdate['tClean'] = $cleanIn;

		if($tRow['tRecc'] != $reccIn)
			$trackUpdate['tRecc'] = $reccIn;
		
		if(strcmp($tRow['tTrackName'], unsafeCmp($_POST["track$trNum"])) != 0)
			$trackUpdate['tTrackName'] = $_POST["track$trNum"];
		
		/** Update libtrack! **/
		if(!empty($trackUpdate)) {
			$qTrU = "UPDATE libtrack SET ";
			$tmp = array();
			foreach($trackUpdate as $key => $val)
				$tmp[] = "$key='$val'";
			$tmp = implode(", ", $tmp);
			$qTrU .= $tmp;
			$qTrU .= " WHERE tID='".$tRow['tID']."'";
			
			global $user;
			addAction($user->name, 'libtrack', $tRow['tID'], 'EDIT', serialize($trackUpdate));
			mysql_query($qTrU) or die("\nlibtrack update failed: ".mysql_error());
			$toUpdate['libtrack'][$trNum] = $trackUpdate;
		}
	}
	//print_r($toUpdate); die();
	if(!empty($toUpdate))
		$_SESSION['confirmMessage'] = "Confirmation: $nAlbum by $nArtist ($cdcode) was modified successfully.";
	else 
		$_SESSION['errorMessage'] = "Error: No changes were made to $nAlbum by $nArtist ($cdcode).";
	
	//print_r($uris);
	header("Location: ".useUriStruct($uris));
	
	
} break;

/** case INSERT used to be here... **/
case "CUSTOM": {
	
} break;
case "LIST": {
/** BUGS: no end case for "next"
	sort by column not implemented in UI
**/

	$orderField = "cAlbumNo";
	$orderDir = "DESC";
	$limitStart = 0;
	$limitLength = 50;
	if(isset($_GET['order'])) {
		$tmp = explode("-", $_GET['order']);
		$orderField = $tmp[0];
		$orderDir = $tmp[1];
	}
	if(isset($_GET['limit'])) {
		$tmp = explode("-", $_GET['limit']);
		$limitStart = $tmp[0];
		$limitLength = $tmp[1];
	}
	$biglistQ = "SELECT libcd.cAlbumNo, libartist.aPrettyArtistName, libcd.cAlbumName, liblabel.lPrettyLabelName FROM " .
				"libcd, libartist, liblabel WHERE libcd.c_aID=libartist.aID AND libcd.c_lID=liblabel.lID AND libcd.cAlbumNo != '' ";
	$biglistQ .= "ORDER BY $orderField $orderDir LIMIT $limitStart, $limitLength";
//echo "<br>$biglistQ<br><br>";	
	//$biglistQ .= isset($_GET['order']) ? str_replace("-", " ", $_GET['order'])." " : ;
	//$biglistQ .= isset($_GET['limit']) ? "LIMIT ".str_replace("-", " ", $_GET['limit']) : "LIMIT 0,50";
	
	$rsc = mysql_query($biglistQ) or die(mysql_error());
	//$editPageUri = genUriStruct("http://wsbf.net/modifyreviews");
	$editPageUri = genUriStruct("http://wsbf.net/node/1462");
	$thisPageUri = genUriStruct("http://wsbf.net/adminreviews");
	
	
	?><p style='width: 100%; text-align: center;'><?php
	
	if($limitStart != 0) {
		echo "Prior <a href='".useUriStruct(updateUriStruct($thisPageUri, 'limit', ($limitStart-$limitLength)."-".$limitLength));
		echo "'><img src='http://wsbf.net/i/icons/arrow_left.png' alt='left' /></a> | ";
	}
	echo "<a href='".useUriStruct(updateUriStruct($thisPageUri, 'limit', ($limitStart+$limitLength)."-".$limitLength));
	echo "'><img src='http://wsbf.net/i/icons/arrow_right.png' alt='right' /></a> Next";
	
	
	//http://wsbf.net/i/icons/arrow_redo.png
	?>
	</p>
	<table>
		<tr><th>Edit</th>
		<th>Artist</th>
		<th>Album</th>
		<!--<th>Label</th>-->
		</tr>
	<?php
	while($row = mysql_fetch_array($rsc, MYSQL_ASSOC)) {
		$thisUri = useUriStruct(updateUriStruct($editPageUri, "cdcode", $row['cAlbumNo']));
		echo "<tr><td><a href='$thisUri'>
			<img src='http://wsbf.net/i/icons/report_edit.png' alt='edit' /></a> ".$row['cAlbumNo']."</td>";
		
		echo "<td>".$row['aPrettyArtistName']."</td>";
		echo "<td>".$row['cAlbumName']."</td>";
		//echo "<td>".$row['lPrettyLabelName']."</td>";
		echo "</tr>\n";
	}
	
	echo "</table>";
	
	
} break;
default: { 
	die("no mode - weird, huh?");
} } //switch

ob_end_flush();

?>
