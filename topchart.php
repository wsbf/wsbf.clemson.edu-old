<?php
// WSBF Playlist
// David Bowman - created 03-25-2004
// this php file shows the top most-often-played tracks in the last week

//database stuff
require_once("inc/db.php");
require_once("inc/func.php");

$dbConn = mysql_connect($dbHostname);
mysql_select_db($dbName);

// constants
define(STYLEA, "#7d8085");
define(STYLEB, "#3d3e3f");

$dbSQL ="SELECT lastShow, prevWeek from automatron";
$dbRS = mysql_query($dbSQL, $dbConn) or die(mysql_error());
$auto = mysql_fetch_assoc($dbRS);

$start = $auto['prevWeek'];
$stop = $auto['lastShow'];
// echo "FROM $start to $stop";
// read config settings and store in variables

// get number of spins per album
//SELECT pAlbumNo, pTrackNo, pArtistName, pSongTitle, pAlbumTitle, pRecordLabel, count( pAlbumNo )  AS  'numSpins', count( pTrackNo )  AS  'topTrack' FROM lbplaylist WHERE p_Sid >1225 AND p_Sid <=1278 AND pAlbumNo <>  '' AND pAlbumNo <>  'OPTIONAL' AND pAlbumNo <>  '0' AND pAlbumNo <>  'O' GROUP  BY palbumno ORDER  BY  `pAlbumNo`  DESC
$dbSQL = "SELECT pAlbumNo, pTrackNo, pArtistName, pSongTitle, pAlbumTitle, pRecordLabel, count(pAlbumNo) as 'numSpins' from lbplaylist where p_Sid > " . $start . " and p_Sid <= " . $stop . " and length(pAlbumNo) = 4 group by palbumNo order by numSpins DESC;";
$dbTopAlbum = mysql_query($dbSQL, $dbConn) or die(mysql_error());


$sMyPath = $_SERVER['PHP_SELF'];

function name_case($name)
{
   $newname = strtoupper($name[0]);
   for ($i=1; $i < strlen($name); $i++)
   {
       $subed = substr($name, $i, 1);
       if (((ord($subed) > 64) && (ord($subed) < 123)) ||
           ((ord($subed) > 48) && (ord($subed) < 58)))
       {
           $word_check = substr($name, $i - 2, 2);
           if (!strcasecmp($word_check, 'Mc') || !strcasecmp($word_check, "O'"))
           {
               $newname .= strtoupper($subed);
           }
           else if ($break)
           {

               $newname .= strtoupper($subed);
           }
           else
           {
               $newname .= strtolower($subed);
           }
             $break=0;
       }
       else
       {
           // not a letter - a boundary
             $newname .= $subed;
           $break=1;
       }
   }
   return $newname;
}

?>

<html>
<head>
    <title>WSBF Spin Reporter</title>
    <link rel="stylesheet" type="text/css" href="http://wsbf.net/wp-content/themes/rounded-grey-blog-10/style.css" />
    <link rel="stylesheet" type="text/css" href="wsbf.css" />
</head>

<body>
    <h3 align="center">WSBF Top Spins</h3>
    <table>
        <tr>
            <td>#</td>
            <td>Artist</td>
            <td>Album</td>
            <td>Label</td>
            <td># Spins </td>
            <td>Hot Track </td>
<!--
            <td>Played By</td>
-->
        </tr>
<?php
    $count=1;
    $CID=0;
    while($result = mysql_fetch_assoc($dbTopAlbum)) {
        $Cartist= $result['pArtistName'];
        $Calbum=  $result['pAlbumTitle'];
        $Clabel=  $result['pRecordLabel'];
        $Cspins=  $result['numSpins'];
        // get the number of spins per song
        //SELECT pAlbumNo, pTrackNo, pSongTitle, count( pAlbumNo ) AS 'numSpins' FROM lbplaylist WHERE p_Sid >1225 AND p_Sid <=1278 AND length( pAlbumNo ) =4 AND palbumno = 'G320' GROUP BY palbumno, ptrackno ORDER BY `numSpins` DESC LIMIT 1
        $dbSQL="SELECT pAlbumNo, pTrackNo, pSongTitle, count( pAlbumNo ) AS 'numSpins' FROM lbplaylist WHERE p_Sid > " .$start." AND p_Sid <= " . $stop . " AND length( pAlbumNo )  =4 and palbumno = '" . $result['pAlbumNo'] . "' GROUP  BY palbumno, ptrackno ORDER  BY  `numSpins`  DESC LIMIT 1;";
        $dbTopSong = mysql_query($dbSQL, $dbConn) or die(mysql_error());
        $songResult = mysql_fetch_assoc($dbTopSong);

        // find out who is playing the artist
        // SELECT lbshow.sDJName FROM lbshow INNER JOIN lbplaylist ON lbshow.sID = lbplaylist.p_sID WHERE lbplaylist.pAlbumNo = 'G320' AND lbplaylist.p_SID >1225 AND lbplaylist.p_SID <=1278 ORDER BY lbshow.sDJName LIMIT 0 , 30
        $dbSQL = "SELECT lbshow.sDJName FROM lbshow INNER JOIN lbplaylist ON lbshow.sID = lbplaylist.p_sID WHERE lbplaylist.pAlbumNo = '" . $result['pAlbumNo'] ."' AND lbplaylist.p_SID >" .$start." AND lbplaylist.p_SID <=" . $stop . " ORDER BY lbshow.sDJName;";
        $dbWhoPlayed = mysql_query($dbSQL, $dbConn) or die(mysql_error());
        $Csong=$songResult['pSongTitle'];
        if($CID == 1) {
?>
        <tr bgcolor="<?php echo(trim(STYLEB)) ?>">
<?
            $CID = 0;
        } else {
?>
        <tr bgcolor="<?php echo(trim(STYLEA)) ?>">
<?
            $CID=1;
        }
?>
            <td><?php echo($count); $count=$count+1; ?></td>
            <td><a href="http://www.last.fm/music/<?php echo(name_case($Cartist)) ?>" target="_blank"><?php echo(name_case($Cartist)) ?></a></td>            <td><?php echo(name_case($Calbum)) ?></td>
            <td><?php echo(name_case($Clabel)) ?></td>
            <td><?php echo(name_case($Cspins)) ?></td>
            <td><?php echo(name_case($Csong)) ?></td>
<!--
            <td>
<?php
        $Cwho="";
        while($whoPlayed=mysql_fetch_assoc($dbWhoPlayed)) {
            if($Cwho!="") {
                $Cwho=$Cwho.", ";
            }
            $Cwho=$Cwho . name_case($whoPlayed['sDJName']);
        }
        echo($Cwho);
?>
            </td>
-->
        </tr>
<?php
    }
?>
    </table>
</body>
</html>
