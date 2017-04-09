<?php
//require_once("config/settings.php"); //CONDOR_PATH

/** SECURITY FUNCTIONALITY **/

/** iterate over superglobals and make safe for SQL usage
	call this before doing anything else in your script
	do NOT call any string escaping functions on these vars afterwards
	or you'll end up escaping them twice! (ztm, 4jun10)

	database connection MUST be initialized before sanitizeInput() is called!
	otherwise, the mysql function called below breaks...
**/
//require_once("include/database.php");
function sanitize(&$arr) {		// call by reference
	foreach($arr as $key => $val)
	if(!is_array($val))
		$arr[$key] = mysql_real_escape_string($val);
	else sanitize($val);
}
/** void sanitizeInput() **/
function sanitizeInput() {
	sanitize($_GET);
	sanitize($_POST);
	//sanitize($_REQUEST);
}

function unsanitize(&$arr) {
	foreach($arr as $key => $val)
	if(!is_array($val))
		$arr[$key] = stripslashes($val);
	else unsanitize($val);
}

function unsanitizeInput() {
	unsanitize($_GET);
	unsanitize($_POST);
	//sanitize($_REQUEST);
}

function htmlSanitize(&$arr) {		// to encode/decode when assholes name their show <3
	foreach($arr as $key => $val)	// dac 5/20/11
	if(!is_array($val))
		$arr[$key] = mysql_real_escape_string(htmlspecialchars($val));
	else htmlSanitize($val);
}

function htmlSanitizeInput() {
	htmlSanitize($_GET);
	htmlSanitize($_POST);
	//sanitize($_REQUEST);
}

function htmlUnsanitize(&$arr){
	foreach($arr as $key => $val)
	if(!is_array($val))
		$arr[$key] = htmlspecialchars_decode(stripslashes($val));
	else htmlUnsanitize($val);
}

function htmlUnsanitizeInput() {
	htmlUnsanitize($_GET);
	htmlUnsanitize($_POST);
	//sanitize($_REQUEST);
}

/* htmlspecialchars for an array - but call by reference */
function htmlDisplaySanitize(&$arr){
	foreach($arr as $key => $val)
	if(!is_array($val))
		$arr[$key] = htmlspecialchars($val);
	else htmlSpecial($val);
}






/** this function is called when submitting a modification.
	we don't want to modify things that haven't changed, but escaping with
	sanitizeInput IS a change! so when comparing, use this to check the comparator.
	don't send it to SQL though; that would be a security hole.
**/
function unsafeCmp($inp) {
	$find = array("\\r", "\\n", "\'", "\"");
	$replace = array("\r", "\n", "'", '"');
	//original: str_replace("\\r\\n", "\r\n", $nReviewText);
	return str_replace($find, $replace, $inp);
}




/** URI HANDLING FUNCTIONS **/

/**		genUriStruct takes a full/partial/relative URI and
	returns an associative array that can be modified easily.
		useUriStruct takes one of these arrays and gives a
	string to insert into HTML/what-have-you.
		updateUriStruct modifies an array and updates its GET
	parameters; if the parm doesn't exist already it is added,
	and if it does exist it is modified.	(ztm, 4jun10)
**/

/** array genUriStruct(string $uri) **/
function genUriStruct($uri, $prefix="") {
	$ret['base'] = $prefix;
	$ret['parms'] = array();
	$ret['end'] = "";

	//if using prefix then base ends in /, else is empty
	if(!empty($ret['base']))
		if($ret['base'][strlen($ret['base'])-1] !== "/")
			$ret['base'] .= "/";

	//strip starting slash: relative REQUEST_URI has one
	if($uri[0] == "/")
		$uri = substr($uri,1);

	//take care of end tags, and remove from $uri
	$tmp = strpos($uri, "#");
	if($tmp !== FALSE) {
		$ret['end'] = substr($uri,$tmp+1);
		$uri = substr($uri, 0, $tmp);
	}

	//if no ? then done, else split off base
	$tmp = strpos($uri, "?");
	if($tmp === FALSE) {
		$ret['base'] .= $uri;
		return $ret;
	}
	else {
		$ret['base'] .= substr($uri,0,$tmp);
		$uri = substr($uri, $tmp+1);
	}

	//bust up remaining query into array
	//note: we assume first value in duplicate values is correct
	//but, if there are no bugs there should be no duplicates!
	foreach(explode("&", $uri) as $parmstr) {
		$tmp = explode("=",$parmstr);

		if(!array_key_exists($tmp[0], $ret['parms']))
			$ret['parms'][$tmp[0]] = $tmp[1];
	}
	return $ret;
}
/** string useUriStruct(array $uris) **/
function useUriStruct($uris) {
	//echo "<pre>"; print_r($uris); echo "</pre>";

	$ret = $uris['base'];
	if(!empty($uris['parms']))
		$ret .= "?";
	foreach($uris['parms'] as $name => $val)
		if($name != NULL)
			$ret .= "$name=$val&amp;";

	//take off last &
	$ret = substr($ret, 0, strlen($ret) - strlen("&amp;"));
	if(!empty($uris['end']))
		$ret .= "#".$uris['end'];
	return $ret;
}
/** array updateUriStruct(array $uris, string $parm, string $value) **/
function updateUriStruct($uris, $parm, $value) {
	$uris['parms'][$parm] = urlencode($value);
	return $uris;
}
/** mixed getUriStructParm(array $uris, string $parm) **/
function getUriStructParm($uris, $parm) {
	return $uris['parms'][$parm];
	//this should give NULL if not set...
}



?>
