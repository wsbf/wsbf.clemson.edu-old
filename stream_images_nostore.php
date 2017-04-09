<?php

// adjust the path to be the **FILESYSTEM** path to your files
$path = './'; // current directory

$file = (isset($_GET['file'])) ? strval($_GET['file']) : '../../listen/studioa.jpg';

$file = $path . $file;

$type = substr($file, -3, 3);

// uncomment for logging
/*
$filename = 'stream_images.log';

// check to see if $filename exists, if not, create it.
touch($filename) or die("Unable to create: " . $filename);

// log file format
$datetime = "[" . date('d/M/Y:h:i:s O') . "]";
$somecontent = $_SERVER[REMOTE_ADDR] . " " . $datetime . " " . $file . "\n";

// open $filename for append.
$handle = fopen($filename, 'a') or die("Could not open file: " . $filename . "\n");

// Write $somecontent to the open file.
fwrite($handle, $somecontent) or die("Could not write to file: " . $filename . "\n");

fclose($handle);
*/
// uncomment for logging

header( "Cache-Control: no-store, no-cache, must-revalidate" );
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Content-Length: " . filesize($file));
header( "Cache-Control: post-check=0, pre-check=0", false );
header( "Pragma: no-cache" );
header( "Pragma: public" ); 

switch ($type)
{
case 'gif':
header("Content-Type: image/gif");
break;
case 'jpg':
header("Content-Type: image/jpeg");
break;
case 'png':
header("Content-Type: image/png");
break;
case 'svg':
header("Content-Type: image/svg+xml");
break;
}

$fh = fopen($file,"rb");

while (!feof($fh))
{
print(fread($fh, filesize($file)));
}

fclose($fh);

?>