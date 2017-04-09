<?php

// DB initilization
$link = mysql_connect('localhost') or die("Could not connect");
mysql_select_db('wsbf') or die("Could not select database");


function mysql_fetch_all($result) {
    $all = array();
    while ($row = mysql_fetch_assoc($result)){ $all[] = $row; }
    return $all;
}

/*
 * latestMySQLSong()
 * Gets latest song logged in the MySQL DB
 * Returns an array with the artist, track, and time played
 */
function latestMySQLSong() {
    $query = "SELECT * FROM lbplaylist WHERE pCurrentlyPlaying = 1 ORDER BY pDTS DESC LIMIT 1";
    $result = mysql_query($query) or die(mysql_error());

	//added by ztm as patch
	$artist = "zilch"; $track = "zero"; $time = "nada";

    while($record = mysql_fetch_array($result)) {
        $artist = $record['pArtistName'];
        $track = $record['pSongTitle'];
        $time = strtotime($record['pDTS']);
    }

    return array($artist,$track,$time);
}

function currentShow() {
    $query = "SELECT * FROM lbshow ORDER BY sStartTime DESC LIMIT 1";
    $result = mysql_query($query) or die(mysql_error());

    while($record = mysql_fetch_array($result)) {
        if ($record['sEndTime'] == "0000-00-00 00:00:00") {
            $show = $record['sDJName'];            $time = strtotime($record['sStartTime']);
        } else {
            $show = "Automation";
            $time = strtotime($record['sEndTime']);
        }
    }

    return array($show, $time);
}

/*
 * latestSong()
 * Compares songs played in and MySQl
 * Returns an array with the artist, track, and time played
 */
function latestSong() {
    list($dArtist, $dTrack, $dTime) = latestMySQLSong();

        $artist = $dArtist;
        $track = $dTrack;
        $time = $dTime;

    return array($artist, $track, $time);
}

?>
