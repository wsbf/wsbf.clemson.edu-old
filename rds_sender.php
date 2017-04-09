<?php
require ("php_serial.class.php");

// The name of your radio station
define("STATION_NAME", "WSBF");
// The longest string DPS can have on the Inovonics 730
define("DPS_MAX",127);
// The longest string the Radio Text can be on the Inovonics 730
define("TEXT_MAX",65);
// The password to put for the song name that lets you send any string you want
define("PASSWORD","IAMADUCK");

// This function converts the string to uppercase because many radios
// with RDS don't support lowercase characters, it also replaces cussing
// with clean words, and tries its best to avoid the Clbuttic Problem
function censor($string){
	// Convert to uppercase
	$string = strtoupper($string);
	
	// get rid of leading spaces
	$string = trim($string);
	
	//Replace the cuss words
	$string = str_replace("SHIT", '$H!T', $string);
	$string = str_replace("FUCK", "FRAK", $string);
	$string = str_replace("CUNT", "C*NT", $string);
	$string = str_replace("ASSHOLE", "ASSHAT", $string);
	$string = str_replace("ASS HOLE", "ASSHAT", $string);
	$string = str_replace("ASS-HOLE", "ASSHAT", $string);
	$string = str_replace("TITTY", "BREAST", $string);
	$string = str_replace("TITTIES", "BREASTS", $string);
	$string = str_replace("TITTYS", "BREASTS", $string);
	$string = str_replace("CLIT", "CL*T", $string);
	$string = str_replace("CHOCL*T", "CHOCLIT", $string);
	$string = str_replace("GODDAMN", "GORRAM", $string);
	$string = str_replace("GODDAM", "GORRAM", $string);
	$string = str_replace("GOD-DAMN", "GORRAM", $string);
	$string = str_replace("GOD DAMN", "GORRAM", $string);
	$string = str_replace("GOD DAM", "GORRAM", $string);
	$string = str_replace("GOD-DAM", "GORRAM", $string);
	$string = str_replace("GODDAMN", "GORRAM", $string);
	$string = str_replace("GODDAMMIT", "GORRAMIT", $string);
	$string = str_replace("GOD DAMMIT", "GORRAMIT", $string);
	$string = str_replace("GOD-DAMMIT", "GORRAMIT", $string);
	$string = str_replace("BITCH", "BETCH", $string);
	$string = str_replace("COCK", "CAWK", $string);
	$string = str_replace("CAWKPIT", "COCKPIT", $string);
	$string = str_replace("GAMECAWK", "GAMECOCK", $string);
	$string = str_replace("NIGGER", "CANADIAN", $string);
	$string = str_replace("SCANADIAN", "SNIGGER", $string);
	$string = str_replace("DICK HEAD", "D-HEAD", $string);
	$string = str_replace("DICK-HEAD", "D-HEAD", $string);
	$string = str_replace("DICKHEAD", "D-HEAD", $string);
	$string = str_replace("PUSSY", "PU**Y", $string);
	$string = str_replace("PU**YCAT", "PUSSYCAT", $string);
	$string = str_replace("PU**Y CAT", "PUSSY CAT", $string);
	$string = str_replace("PU**Y-CAT", "PUSSY-CAT", $string);
	$string = str_replace("OCTOPU**Y", "OCTOPUSSY", $string);
	$string = str_replace("FAGGOT", "FORGET", $string);
	$string = str_replace("SC*NTHORPE", "SCUNTHORPE", $string);
	return $string;
}

// This function is used to strip the artist and song names so that
// the strings fit within the maximum lenght provided
function shorten($song, $artist, $max){
	// Get the length of each to use in the calculations
	$artistLen = strlen($artist);
	$songLen = strlen($song);
	
	// The number of other characters in the string is different
	// depending on whether the string is the DPS or the song text
	if($max == DPS_MAX){
		$extra = strlen(STATION_NAME) + 8;
	} elseif($max == TEXT_MAX){
		$extra = 4;
	}
	
	// Add it all up and see what the total length would be
	$totLen = $artistLen + $songLen + $extra;
	
	// if it's too long, try removing the " THE "s
	if($totLen > $max){
		$song = str_replace(" THE ", " ", $song);
		$artist = str_replace(" THE ", " ", $artist);
		$artistLen = strlen($artist);
		$songLen = strlen($song);
		$totLen = $artistLen + $songLen + $extra;
	}
	
	// if it's still too long, start stripping one character at a time from
	// whichever is longer, the artist name or the song name until the total
	// length is short enough to send
	while($totLen > $max){
		if($songLen > $artistLen){
			$song =  substr($song,0,$songLen - 1);
		} 
		else{
			$artist = substr($artist,0, $artistLen -1);
		}
		
		$artistLen = strlen($artist);
		$songLen = strlen($song);
		$totLen = $artistLen + $songLen + $extra;
	}
		return array($song, $artist);
}


function rdssend($songName, $artistName, $datePassed){
	// The shared memory segment you stored the date in
	$segment_id   = 881;
	// You have to attach to the shared memory segment first
	$shm = shm_attach($segment_id,PHP_INT_SIZE,0600);
	// Then get the date currently stored in the shared memory segment
	$dateStored = shm_get_var($shm,1);

	// Everytime the RDS sender script gets called the Logbook updates the 
	// time saved in the shared memory so we know if we should leave this script
	if($datePassed != $dateStored) {
			// Detach the shared memory segment and exit
			shm_detach($shm);
	}

	// Declare the new Com port
	$COM = new phpSerial;
	
	// Set the serial device "/dev/ttyS0" for linux, "COM1" for windows
	if (substr(PHP_OS,0,3) == 'WIN'){
		$COM->deviceSet("COM1");
	}
	else{
		$COM->deviceSet("/dev/ttyS0");
	}
/*	I suppose PHP doesn't have permissions to execute the mode command, but it's
	fine because we are using the defaults anyway
	// Set the baud rate, parity, length, stop bits, flow control
	$COM->confBaudRate(9600);
	$COM->confParity("none");
	$COM->confCharacterLength(8);
	$COM->confStopBits(1);
	$COM->confFlowControl("none");
*/
	// Remove the bad words and make it all uppercase
	$artistName = censor($artistName);
	$songName = censor($songName);

	// if your song name is the password, we'll keep on sending until Now Playing gets updated
	if ($songName == PASSWORD){
		// See how long the string is so we can see if we need to shorten it
		$artistNameLength = strlen($artistName);
	
		// if the string is longer than the DPS_MAX, get rid of the " THE "s
		if( $artistNameLength > DPS_MAX){
			$artistName = str_replace(" THE ", " ", $artistName);
			$artistNameLength = strlen($artistName);
		}
	
		// if it's still too long, just cut it down to size.
		if( $artistNameLength > DPS_MAX){
			$artistName = substr($artistName,0,DPS_MAX);
			$artistNameLength = strlen($artistName);
		}
	
		// Make the DPS output
		// The Inovonics 730 requires a carriage return at the end of every string
		$dpsOut = "DPS=" . $artistName . chr(13);
		
		// if the string is longer than the TEXT_MAX, get rid of the " THE "s.
		if( $artistNameLength > TEXT_MAX){
			$artistName = str_replace(" THE ", " ", $artistName);
			$artistNameLength = strlen($artistName);
		}
	
		// if it's still too long, just cut it down to size.
		if( $artistNameLength > TEXT_MAX){
			$artistName = substr($artistName,0,TEXT_MAX);
		}
	
		// Make the RT output
		$rtOut = "TEXT=" . $artistName . chr(13);
	
		// Get the date stored again to see if it's been updated
		$dateStored = shm_get_var($shm,1);
	
		// if the stored date hasn't changed, send the output every three minutes
		while ($datePassed == $dateStored){
			// Open the COM port
			$COM->deviceOpen();
			// Send the strings
			$COM->sendMessage($dpsOut);
			$COM->sendMessage($rtOut);
			// Close the port when you're done
			$COM->deviceClose();
	
			sleep(180);
	
			// Grab the stored date again
			$dateStored = shm_get_var($shm,1);
		}
	
		// Detach from the shared memory segment
		shm_detach($shm);
	
		$fs = fopen('test_output.txt', 'w');
		fwrite($fs, $dpsOut);
		fwrite($fs, $rtOut);
		fclose($fs);
	}

	// Or if the song name indicates we're doing a Live Session, format it properly 
	elseif($songName == "LIVE SESSION" || $songName == "LIVE SESSIONS"){
		$dpsOut = "LIVE SESSION WITH " . $artistName . " ON " . STATION_NAME;
		$dpsLen = strlen($dpsOut);
		$rtOut = $artistName . " LIVE ON " . STATION_NAME;
		$rtLen = strlen($rtOut);
		$stationLen = strlen(STATION_NAME) - 1;
		
		// if it's too long we'll drop the station name
		if($dpsLen > DPS_MAX){
			$dpsOut = "LIVE SESSION WITH " . $artistName;
			$dpsLen = strlen($dpsOut);
		}
	
		// if it's still too long then we'll drop the " THE "s.
		if($dpsLen > DPS_MAX){
			$dpsOut = str_replace(" THE ", " ", $dpsOut);
			$dpsLen = strlen($dpsOut);
		}
	
		// And if it's still too long, we'll just cut it short
		if($dpsLen > DPS_MAX){
			$dpsOut = substr($dpsOut,0,DPS_MAX);
		}
	
		// Put it in the format the Inovonics 730 likes
		$dpsOut = "DPS=" . $dpsOut . chr(13);
	
		// Now for the Radio Text, except to make it fun, we need to know
		// the length of the artist name when we go back to calculate the RT+	
		// if it's too long drop the " THE "s.
		if($rtLen > TEXT_MAX){
			$artistName = str_replace(" THE ", " ", $artistName);
			$rtOut = $artistName . " LIVE ON " . STATION_NAME;
			$rtLen = strlen($rtOut);
		}
	
		// if it's still too long we cut the artist name down to size
		if($rtLen > TEXT_MAX){
			// The longest the artist name can be is the TEXT_MAX length,
			// minus the length of the STATION_NAME, plus the 9 characters
			// for " LIVE ON " plus 1 because station len is the length minus 1
			$artMax = TEXT_MAX - ($stationLen + 10);
			$artistName = substr($artistName,0,$artMax);
			$rtOut = $artistName . " LIVE ON " . STATION_NAME;
		}
	
		// Format the output for the Inovonics 730
		$rtOut = "TEXT=" . $rtOut . chr(13);
	
		// Let's calculate some RT+
		// The count starts at zero
		$artistNameLength = strlen($artistName) - 1;
		// This will give the starting position of STATION_NAME
		$stationStart = $artistNameLength + 10;
	
		// This makes it so they are all two digits
		$artistNameLength = str_pad($artistNameLength,2,"0",STR_PAD_LEFT);
		$stationLen = str_pad($stationLen,2,"0",STR_PAD_LEFT);
		$stationStart = str_pad($stationStart,2,"0",STR_PAD_LEFT);
	
		// Type,Starting Position,Length, Type,Starting Position,Length
		$rtpOut = "RTP=04,00," . $artistNameLength . ",31," . $stationStart . "," . $stationLen . chr(13);
	
		// Grab the date currently stored in memory
		$dateStored = shm_get_var($shm,1);
	
		// if it is still the same as the date passed in, send it every three minutes until it no longer is
		while ($datePassed == $dateStored){
			// Open the COM port
			$COM->deviceOpen();
			// Send the strings
			$COM->sendMessage($dpsOut);
			$COM->sendMessage($rtOut);
			$COM->sendMessage($rtpOut);
			// Close the port when you're done
			$COM->deviceClose();
	
			sleep(180);
		
			// Check the date again
			$dateStored = shm_get_var($shm,1);
		}
	
		// Detach from the shared memory and exit
		shm_detach($shm);
	
		$fs = fopen('test_output.txt', 'w');
		fwrite($fs, $dpsOut);
		fwrite($fs, $rtOut);
		fwrite($fs, $rtpOut);
		fclose($fs);
	}
	// if it is just a regular song and artist name and nothing special
	else{
	
		// Call the trim function to cut them down to size for DPS and make the right string
		list($songName, $artistName) = shorten($songName, $artistName, DPS_MAX);
		$dpsOut = "DPS=" . $songName . " BY " . $artistName . " ON " . STATION_NAME . chr(13);
	
		// Call the trim function to cut them down to size for RT and make the string
		list($songName, $artistName) = shorten($songName, $artistName, TEXT_MAX);
		$rtOut = "TEXT=" . $songName . " BY " . $artistName . chr(13);
	
		// Start calculating the RT+ value
		$artistNameLength = strlen($artistName)-1;
		$songNameLength = strlen($songName) - 1;
		// The starting value of the artist name is the length of the song name, plus 4 for " BY "
		// and plus one because the length of the song name is one less than it actually is
		$artistStart = $songNameLength + 5;
	
		// Make it so that they are two digit numbers
		$artistNameLength = str_pad($artistNameLength,2,"0",STR_PAD_LEFT);
		$songNameLength = str_pad($songNameLength,2,"0",STR_PAD_LEFT);
		$artistStart = str_pad($artistStart,2,"0",STR_PAD_LEFT);
	
		// Make the RT+ output with the right format
		$rtpOut = "RTP=01,00," . $songNameLength . ",04," . $artistStart . "," . $artistNameLength . chr(13);
	
		// Get the stored date from the shared memory segment
		$dateStored = shm_get_var($shm,1);
	
		// if the passed date is the same as the stored date, go ahead and send it
		if($dateStored == $datePassed){
			// Open the COM port
			$COM->deviceOpen();
			// Send the DPS String
			$COM->sendMessage($dpsOut);
			$COM->sendMessage($rtOut);
			$COM->sendMessage($rtpOut);
			// Close the port when you're done
			$COM->deviceClose();
		}
		// Detach the shared memory segment and exit
		shm_detach($shm);

		$fs = fopen('test_output.txt', 'w');
		fwrite($fs, $dpsOut);
		fwrite($fs, $rtOut);
		fwrite($fs, $rtpOut);
		fclose($fs);
	}
}
?>