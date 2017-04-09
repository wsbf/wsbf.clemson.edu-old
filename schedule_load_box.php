<?php

require_once('conn.php');
//require_once('review_lib.php');
require_once('utils_ccl.php');
sanitizeInput();
if(isset($_GET['show_desc'])){
	if($_GET['show_desc'] == '')
		echo "There is not yet a description for this show";
	echo $_GET['show_desc'];
}
else echo "ERROR";


?>