<?php

if(isset($_GET['artist']) && isset($_GET['song'])){
$artist = $_GET['artist'];
$song = $_GET['song'];

/*
$segment_id= 881;
// You have to attach to the shared memory segment first
$shm = shm_attach($segment_id,PHP_INT_SIZE,0600);
$date = time();
shm_put_var($shm,1,$date);
include("../rds_sender.php");
rdssend($song,$artist,$date);
*/

$fa = fopen('current_artist.txt', 'w');
fwrite($fa, $artist);
fclose($fa);

$fs = fopen('current_song.txt', 'w');
fwrite($fs, $song);
fclose($fs);

// include("../rds_sender_test.php");

//rdssend($song,$artist);
echo "done. $artist $song";
}
else echo "No valid input.";

?>