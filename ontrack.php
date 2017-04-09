<?PHP
include("wizbif.php");
list($artist, $track, $time) = latestSong();

$track = $track . " <i>by</i> " . $artist;

echo $track;
?>
