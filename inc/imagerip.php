<?PHP
// Requires
// $query - the thing you're looking for
$words = split($phrase,'%20');
$url = "http://images.google.com/images?svnum=10&ct=result&cd=1&spell=1&q=";
foreach ($words as $keyword)
	$url .= "$keyword+";

$result = file_get_contents($url);
$index = strpos($result,".jpg");
if ($index) {
	$index2 = strrpos($result,'"',$index);

	$imurl = substr($result,$index2, $index-$index2);
	$thumburl="http://images.google.com/images?q=tbn:4m3iYumM6EUCxM:$imurl";

	echo "<strong>$thumburl</strong><br>";
	echo "<img src='$thumburl'>";
} else {
	echo "Failed to find image.";
}


	