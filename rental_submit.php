<?php

	if($_POST["captcha"] != "wsbf")
		die("You robot. Next time, fill out the captcha correctly. \r\n
			Click <a href='http://wsbf.net/rentals'>here</a> to go back.");


    $req_equip = array();
    $req_equip_amount = array();
    function mysql_fetch_all($result) {
        $all = array();
        while ($row = mysql_fetch_assoc($result)){ $all[] = $row; }
        return $all;
    }

    $email_summ = "";

    require_once("connect.php");

    $email_summ .= $_POST['name'] . " has requested a rental.\n";
    $email_summ .= "Rental Date: " . $_POST['month'] . " " . $_POST['day'] . ", " . $_POST['year'] ."\n";
    $email_summ .= "Phone: " . $_POST['phone'] ."\n";
    $email_summ .= "E-mail: " . $_POST['email'] ."\n";
    $email_summ .= "Event: " . $_POST['event'] ."\n";
    $email_summ .= "Location: " . $_POST['location'] ."\n";
    $email_summ .= "\n";

    $pack_select = "None";
    $query = "SELECT * FROM `equip_package`";
    $result = mysql_query($query) or die("Packages Query failed : " . mysql_error());
    $packages = mysql_fetch_all($result);
    foreach ($packages as $package) {
        if($package['name'] = $_POST['select1']) {
            $pack_select = $package['fullName'];
        }
    }

    $email_summ .= "Original Package Selection: " . $pack_select . "\n";
    $email_summ .= "\n";

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'quantity') !== false && $value != 0) {
            $id = substr($key,8);
            $req_equip[] = $id;
            $req_equip_amount[] = $value;
        }
    }

    $query = "SELECT * FROM `equipment`";
    $result = mysql_query($query) or die("Packages Query failed : " . mysql_error());
    $equipment = mysql_fetch_all($result);

    foreach ($equipment as $equip_item) {
        if(array_search($equip_item['id'], $req_equip) !== false) {
            $email_summ .= $equip_item['name'] . "\nQuantity: " . $req_equip_amount[array_search($equip_item['id'], $req_equip)] . "\n\n";
        }
    }

    if(trim($_POST['comments']) != "Enter extra notes here...") {
        $email_summ .= "Extra Notes: " . $_POST['comments'];
    }

    echo "<center>You rental request has been submitted to our production engineer. A response will be sent soon with equipment avalaibility and pricing.<br />";
    echo "The following information has been sent:</center><br />";
    echo "<div id=\"email\">";
    echo "<pre>" . $email_summ . "</pre>";
    echo "</div>";

    $body = $email_summ;
    $subject = "WSBF-FM Rental Request from " . $_POST['name'];
/*
    $to = "production@wsbf.net,business@wsbf.net,chief@wsbf.net";
//    $to = "computer@wsbf.net";
    $header = "From: The Internets <computer@wsbf.net>\r\n";
    mail($to, $subject, $body, $header);
*/

$Name = "WSBF Rental"; //senders name
$email = "no.reply@wsbf.net"; //senders e-mail adress
$recipient = "production@wsbf.net,business@wsbf.net,chief@wsbf.net";
$mail_body = $body; //mail body
$subject = $subject; //subject

mail($recipient, $subject, $mail_body, $header); //mail command :)
?>