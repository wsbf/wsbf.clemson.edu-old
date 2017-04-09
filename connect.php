<?PHP
	$cpw = "";

    // Vital functions, login checks, and database connection
    // Created by Thomas Davidson

    // Connect to MySQL database
    // Change this file if passwords change
    $link = mysql_connect('localhost') or die("Could not connect");
    mysql_select_db('wsbf', $link) or die("Could not select database");

	// Default to no authorization
	$authlevel = 0;
	$showtable = 'shows'; // Change this between 'shows' and 'summershows'
	/**
	// Get passwords
	// admin
	$query = 'SELECT * from `settings` WHERE name="adminpw"';
	$result = mysql_query($query) or die("Query failed : " . mysql_error());
	$admin_pw_row = mysql_fetch_array($result);
	$adminpw = $admin_pw_row['value'];
	// business
	$query = 'SELECT * from `settings` WHERE name="businesspw"';
	$result = mysql_query($query) or die("Query failed : " . mysql_error());
	$business_pw_row = mysql_fetch_array($result);
	$businesspw = $business_pw_row['value'];
	// member
	$query = 'SELECT * from `settings` WHERE name="memberpw"';
	$result = mysql_query($query) or die("Query failed : " . mysql_error());
	$member_pw_row = mysql_fetch_array($result);
	$memberpw = $member_pw_row['value'];
	// dj
	$query = 'SELECT * from `settings` WHERE name="djpw"';
	$result = mysql_query($query) or die("Query failed : " . mysql_error());
	$dj_pw_row = mysql_fetch_array($result);
	$djpw = $dj_pw_row['value'];
	// music
	$query = 'SELECT * from `settings` WHERE name="musicpw"';
	$result = mysql_query($query) or die("Query failed : " . mysql_error());
	$music_pw_row = mysql_fetch_array($result);
	$musicpw = $music_pw_row['value'];

	if (!empty($adminpw) && ($adminpw == $cpw)) {
	    $authlevel = 10;
	} else if (!empty($businesspw) && ($businesspw == $cpw)) {
		$authlevel = 2;
	} else if (!empty($memberpw) && ($memberpw == $cpw)) {
		$authlevel = 3;
	} else if (!empty($djpw) && ($djpw == $cpw)) {
		$authlevel = 1;
	} else if (!empty($musicpw) && ($musicpw == $cpw)) {
		$authlevel = 4;
	}
	**/

	// Make string safe for including in double quotes
	function clean($text) {
	   return str_replace('"', '&quot;', $text);
	}

	// Get a unix timestamp for the oldest archives that should be shown
	function getOldest() {
	    $today = getdate();
	    return mktime($today['hours'],$today['seconds'],$today['minutes'],$today['mon'],$today['mday']-14, $today['year']);
	}

    // Get an array containing info about the CURRENTLY PLAYING song/show
    function getinfo() {
        $bob = "";
		$iDay = date("w");
        $iHour = date("G");
        $iMin = date("i");
        $iHour_orig = $iHour;

        if($iHour == 0)
        {
            $iHour = 23;
            $iDay--;
        } elseif( ($iHour % 2) == 0)
        {
            //round to largest odd hour smaller than current time (sort of a
            // floor function) to find when this show started
            $iHour--;
        } // end If



		// Get current show info
		$query = "SELECT * from lbshow WHERE 1 order by `sID` DESC LIMIT 1";
		$dbMAXPL = mysql_query($query) or die(mysql_error());
		$lastPL = mysql_fetch_assoc($dbMAXPL);

		$query = "SELECT * FROM shows WHERE start_hour = " . $iHour . " AND day = " . $iDay . ";";
		$dbRS = mysql_query($query) or die(mysql_error());
		$expPL = mysql_fetch_assoc($dbRS);

		if ($bob == 'baa')
		    echo "[QOY - $iHour:$iMin]";

        // 15 minute tolerance for early/late shows
        if ($iMin > 45 && $iHour_orig % 2 == 0) {
            $iHour+=2;
            if ($iHour > 24) {
                $iHour -= 24;
                $iDay++;
            }

			$query = "SELECT * FROM shows WHERE start_hour = " . $iHour . " AND day = " . $iDay . ";";
			$dbRS = mysql_query($query) or die(mysql_error());
			$expPL2 = mysql_fetch_assoc($dbRS);

		} else if ($iMin < 15 && $iHour_orig% 2 == 1) {
			$iHour-=2;
			if ($iHour < 1) {
				$iHour += 24;
				$iDay--;
	        }

			$query = "SELECT * FROM shows WHERE start_hour = " . $iHour . " AND day = " . $iDay . ";";
			$dbRS = mysql_query($query) or die(mysql_error());
			$expPL2 = mysql_fetch_assoc($dbRS);

			if ($bob == 'baa')
				echo '[QOY' . $expPL2['dj_name'] . ']';
		}



		$lastPL['sDJName'] = str_replace(" and ", ", ", $lastPL['sDJName']);

		if ($lastPL['sEndTime'] == "0000-00-00 00:00:00") {
			if (($lastPL['sDJName'] == $expPL['dj_name'])) {
	            // DJ is doing his normal show

	            // Get current song info
	            $dbSQL = "SELECT * FROM lbplaylist WHERE pCurrentlyPlaying = 1";
	            $dbRS = mysql_query($dbSQL) or die(mysql_error());
	            $currSong = mysql_fetch_assoc($dbRS);

	            $track =  trim($currSong['pSongTitle']) . " <i>by</i> " . trim($currSong['pArtistName']);

	            return array($track, $expPL['dj_name'], $expPL['show_name'], $expPL['show_id'], $expPL['start_hour']);
	        } else if (($bob=='baa') && is_array($expPL2) && ($lastPL['sDJName'] == $expPL2['dj_name'])) {
	            // DJ is doing his normal show (up to 15min early or up to 15min late)

	            // Get current song info
	            $dbSQL = "SELECT * FROM lbplaylist WHERE pCurrentlyPlaying = 1";
	            $dbRS = mysql_query($dbSQL) or die(mysql_error());
	            $currSong = mysql_fetch_assoc($dbRS);

	            $track = trim($currSong['pSongTitle']) . " <i>by</i> " . trim($currSong['pArtistName']);

	            return array($track, $expPL2['dj_name'], $expPL2['show_name'], $expPL2['show_id'], $expPL2['start_hour']);
	        } else {
				// Freeform, sub, etc

	            // Get current song info
	            $dbSQL = "SELECT * FROM lbplaylist WHERE pCurrentlyPlaying = 1";
	            $dbRS = mysql_query($dbSQL) or die(mysql_error());
	            $currSong = mysql_fetch_assoc($dbRS);

	            $track = trim($currSong['pSongTitle']) . " <i>by</i> " . trim($currSong['pArtistName']);

	            return array($track, $lastPL['sDJName'], "", -1);
			}

	    } else {
			$day = date("d");
			$track = "Please update this script.";
            return array($track, "Automation", "", -1);
			/*
	$current_dir = getcwd();
	$address = "http://wsbf.net/";
	chdir('E:\WAMP\www\\');
            // Automation
            $autopuke = file("autopuke/WSBF_FM.xml");
            $song = substr($autopuke[6], 7, strlen($autopuke[6]) - 9);
            $artist = substr($autopuke[7], 8, strlen($autopuke[7]) - 10);

            $track = ucwords(strtolower($song)) . " <i>by</i> " . ucwords(strtolower($artist));
	chdir($current_dir);
            return array($track, "Automation", "", -1);
			*/
        }
    }
	// Function to create links from a list of DJs
	function djlinks($dj_name, $linkstyle) {
		$label = "";
		if (strpos($dj_name, ',')) {
			// This is a list of DJs, parse it!

			while ($cutoff = strpos($dj_name, ',')) {
				$name = trim(substr($dj_name,0,$cutoff));

				// Get DJ info
				$query = "SELECT * from `djs` WHERE `name`='$name'";
				$result2 = mysql_query($query) or die("Query failed : " . mysql_error());
				$dj_info = mysql_fetch_array($result2);
				$alias = $dj_info['alias'];
				if (empty($alias))
					$alias = $name;


				$label = $label . "<a class='$linkstyle' href='dj.php?name=$name'>$alias</a>, ";

				$dj_name = substr($dj_name,$cutoff + 1);
			}
			// The last little bit left over

			$name = trim($dj_name);


			// Get DJ info
			$query = "SELECT * from `djs` WHERE `name`='$name'";
			$result2 = mysql_query($query) or die("Query failed : " . mysql_error());
			$dj_info = mysql_fetch_array($result2);
			$alias = $dj_info['alias'];
			if (empty($alias))
				$alias = $name;

			$label = $label . "<a class='$linkstyle' href='dj.php?name=$name'>$alias</a>";
		} else {
			// Only one DJ

			// Get DJ info
			$query = "SELECT * from `djs` WHERE `name`='$dj_name'";
			$result2 = mysql_query($query) or die("Query failed : " . mysql_error());
			$dj_info = mysql_fetch_array($result2);
			$alias = $dj_info['alias'];
			if (empty($alias))
				$alias = $dj_name;

			$label = "<a class='$linkstyle' href='dj.php?name=$dj_name'>$alias</a>";
		}
		return $label;
	} // end dj_links

	// Function to get the alias of a DJ
	function dj_alias($dj_name) {
		$dj_name = trim($dj_name);
		$query = "SELECT * from `djs` WHERE `name`='$dj_name'";
		$result = mysql_query($query) or die("Query failed : " . mysql_error());
		$dj_info = mysql_fetch_array($result);
		return $dj_info['alias'];
	}

	// Function to disassemble e-mail addresses
	// and output javascript to reassemble
	function fullyConfuse($email) {
        $cutoff = strpos($email, '@');
        if ($cutoff > 0) {
            $parta = substr($email,0, $cutoff );
            $partc = substr($email,$cutoff + 1, strlen($email) - $cutoff - 5);
            $partb = substr($email,strlen($email) - 3, 3);
            $email = '<SCRIPT language="JavaScript" type="text/javascript">' . " confuse('$parta', '$partb', '', '$partc');\n" . '</SCRIPT>';
        } else {
            $email = "<a href='$email'>$email</a>";
        }
        return $email;
   }

?>
