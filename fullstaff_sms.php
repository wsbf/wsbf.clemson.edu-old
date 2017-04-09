<?php
//lovingly ripped off from:
// arvin@sudocode.net
// url  : http://sudocode.net/article/66/sending-a-google-voice-sms-using-php/
// date: November 26, 2010

//code to send to multiple texts at a time written by
// David A. Cohen, II, Computer Engineer, WSBF-FM Clemson (http://wsbf.net)
//computer@wsbf.net

//see sms.php for sending it to one person
$GLOBALS['time'] = $time = microtime(true);
require_once('utils_ccl.php');
require_once('class.xhttp.php');
require_once('stream_conn.php');


function sendMessageTo($numbers, $delaySec){
    if(microtime(true)-$GLOBALS['time'] > 60 * 15) die(); // die after 30 minutes
    $data = array();
    // Set account login info
    $data['post'] = array(
      'accountType' => 'GOOGLE',
      'Email'       => 'dj@wsbf.net',
      'Passwd'      => 'jigglybuff',
      'service'     => 'grandcentral',
      'source'      => 'wsbf-sms-multi-send-2.0' // Application's name, e.g. companyName-applicationName-versionID
    );
     
    $response = xhttp::fetch('https://www.google.com/accounts/ClientLogin', $data);

    if(!$response['successful']) {
        echo 'response: '; print_r($response);
        die();
    }

    // Extract Auth
    preg_match('/Auth=(.+)/', $response['body'], $matches);
    $auth = $matches[1];
    // You can also cache this auth value for at least 5+ minutes

    // Erase POST variables used on the previous xhttp call
    $data['post'] = null;

    // Set Authorization for authentication
    // There is no official documentation and this might change without notice
    $data['headers'] = array(
        'Authorization' => 'GoogleLogin auth='.$auth
    );

    $response = xhttp::fetch('https://www.google.com/voice', $data);

    if(!$response['successful']) {
        echo 'response: ' . print_r($response, true) . "<br />";
        die();
    }

    // Extract _rnr_se | This value does not change* Cache this value
    preg_match("/'_rnr_se': '([^']+)'/", $response['body'], $matches);
    $rnrse = $matches[1];

    // $data['headers'] still contains Auth for authentication


    $numSuccesses = $numFailures = 0; // number of successful/failed TEXTS sent
    $sendSuccessNumbers = array();  // array of successful PHONE NUMBERS 
    $sendFailNumbers = array();     // array of failed PHONE NUMBERS

    $chunks = array_chunk($numbers,5); //this function chunks everything into arrays of five
    foreach($chunks as $chunk){
	
	    set_time_limit(25); 
	
	    //the above set_time_limit should prevent maximum execution time errors from happening; the set_time_limit function resets the time to zero each time it is called, so I'm giving it 15 seconds for each text it sends. 15 should be well more than enough (even if i set it to sleep(5) at the end, it still works)
	    /*
	    $phone = '';
	
	    foreach($chunk as $number){
		    $phone .= $number . ","; //this makes each array of five numbers into a string with five numbers separated by commas
	    }*/
	    $phone = implode(', ', $chunk);
	
    //echo $phone ."<br />";

	    // Set SMS options
	    $data['post'] = array (
	        '_rnr_se'     => $rnrse,
	        'phoneNumber' => $phone, // country code + area code + phone number (international notation)
	        'text'        => $text_body,
	        'id'          => ''  // thread ID of message, GVoice's way of threading the messages like GMail
	    );

	    // Send the SMS
	    $response = xhttp::fetch('https://www.google.com/voice/sms/send/', $data);


	    // Evaluate the response
	    $value = json_decode($response['body']);

	    if($value->ok) {
	        echo "SMS message sent! ({$data['post']['phoneNumber']}: {$data['post']['text']}) <br />";
	        $numSuccesses++;
	        foreach($chunk as $number)
	            $sendSuccessNumbers[] = $number;
	        
	    } else {
	        echo "Unable to send SMS! Error Code ({$value->data->code})\n\n <br />";
	        echo 'response: <pre>' . print_r($response, true) . '</pre><br />';
	        $numFailures++;
	        if($value->data->code == 58)
	            $delaySec = ($delaySec * 2); // double number of seconds of delay between sends
	        foreach($chunk as $number)
	            $sendFailNumbers[] = $number;
	    }

    sleep($delaySec); //google won't let you sent a bunch of requests at a time, so it must pause for a couple seconds. IF YOU ARE GETTING A BUNCH OF ERROR(58) CODES, INCREASE THE SLEEP TIME (you may also need to change the sest_time_limit above.)
    }

    echo $numFailures . " Failed Messages to: " . implode(', ', $sendFailNumbers) . "<br /><br />";
    echo $numSuccesses . " Successful Messages to: " . implode(', ', $sendSuccessNumbers) . "<br /><br />";
    
    echo "Trying to send again to failed numbers with delay $delaySec <br />";
    sendMessageTo($sendFailNumbers, $delaySec); 
}





if(!isset($_POST['submit'])){
?><form method='post'><BR /><p>Write your text below. Be patient; it may take up to 5 minutes to work. Don't navigate to any other pages until it says "Complete!" at the bottom.</p>
	<p>Also, please use this with care. No one likes getting harassed with texts; with great power comes great responsibility.</p> <br />
	<script type='text/javascript' src='http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js'></script>
	   <script type='text/javascript'>
	     $(document).ready(function() {
			var maxlen = 160;
		$('#charLeft').text(maxlen);
	         $('#ta').keyup(function() {

	            var len = this.value.length;
	            if (len >= maxlen) {
	                 this.value = this.value.substring(0, maxlen);
	             }
	             $('#charLeft').text(maxlen - len);
	         });
	     });
	   </script>
	<h2><span id="charLeft"> </span> characters remaining. </h2>
	<textarea id='ta' name='text_body' cols='50' rows='10'></textarea><br /><br /><input type='submit' name='submit' value='Send!'>
<?php
}
else{
sanitizeInput();
$text_body = stripslashes($_POST['text_body']);


//your query goes here
$q = "SELECT phone_number FROM `users` WHERE phone_number !='' AND sms_recv=1 and statusID = 0";
$res = mysql_query($q) or die(mysql_error());
while($row = mysql_fetch_assoc($res)){
	$numbers[] = $row['phone_number'];
}
//Google limits 5 recipients per text message, so we must divide it up by fives
$numbers = array();
sendMessageTo($numbers, 15);





$netTime = microtime(true)-$time;
echo "<br />Completed in $netTime seconds!<br /><br />";
}

?>
