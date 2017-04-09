<?php
/* These are just a few functions that help with changing strings */
function titleCase($string){
	// Proper english capitalization (for title)

	// de-capitalize common words
	$commonWords = array(
		'a',
		'an',
		'and',
		'at',
		'for',
		'in',
		'of',
		'on',
		'or',
		'the',
		'to',
		'with');

	$a = explode(' ', strtolower($string));


	for($i = 0; $i < count($a); $i++){
		if(in_array($a[$i], $commonWords) && $i > 0)
			$a[$i] = strtolower($a[$i]);
		else
			$a[$i] = ucfirst($a[$i]);
	}
	$string = implode(' ', $a);
    return $string;

}

function removeCommonWords($str){
	// remove common search terms
	// call by reference (actually changes the variable)
	$remove = array (
		'at',
		'the',
		'and',
		'of',
		'in',
		'with',
		'&');
	for ($x = 0; $x < count($remove); $x++) {
		$str = preg_replace('/\b' . $remove[$x] .'\b/i', '', $str);
	}

	return($str);
}

?>
