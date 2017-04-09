<?php

$list_file = "E:/DRSAUDIOimport_todelete.txt";


$open = fopen($list_file, 'rb');
while($read = fgets($open)){
	if(unlink(trim($read))){
		echo "Successfully deleted $read<br>";
	}
	else {
		echo "<b>*******Failed to delete $read</b><br>";
	}
}
fclose($open);

?>


