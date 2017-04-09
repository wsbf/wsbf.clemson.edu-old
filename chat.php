<?php
//in-page chat lovingly ripped off from http://net.tutsplus.com/tutorials/javascript-ajax/how-to-create-a-simple-web-based-chat-application/

//dac - august 2010
session_start();
$ip=$_SERVER['REMOTE_ADDR'];
$_SESSION['ip'] = $ip;
require_once('conn.php');

//Gets the alias from listen.php
if(isset($_GET['alias'])){
// $alias = $_GET['alias'];
//echo $alias;
$_SESSION['name'] = $_GET['alias'];
}
 

if(isset($_GET['logout'])){	
/*	
	//Simple exit message
	$fp = fopen("log.html", 'a');
	fwrite($fp, "<div class='msgln'><i>User ". $_SESSION['name'] ." has left the chat session.</i><br></div>");
	fclose($fp);
*/	
	session_destroy();
	header("Location: chat.php"); //Redirect the user
}

function loginForm(){
	echo'
	<div id="loginform"><h1>Live Chat - Talk Your DJ now!</h1>
	<form action="chat.php" method="post">
		<p>Please enter your name to continue:</p>
		<label for="name">Name:</label>
		<input type="text" name="name" id="name" />
		<input type="submit" name="enter" id="enter" value="Enter" />
	</form>
	</div>
	';
}

if(isset($_POST['enter'])){
	if($_POST['name'] != ""){
		$_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
	}
	else{
		echo '<span class="error">Please type in a name</span>';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link type="text/css" rel="stylesheet" href="chat_style.css" />
</head>

<?php
if(!isset($_SESSION['name'])){
	loginForm();
}
else{
?>
<div id="wrapper"><h1>Live Chat</h1>
	<div id="menu">
		<p class="welcome">Welcome, <b><?php echo $_SESSION['name']; ?></b></p>
		<p class="logout"><a id="exit" href="#">Exit Chat</a></p>
		<div style="clear:both"></div>
	</div>	
	<div id="chatbox"><?php
//include('chat_log.php');
	?></div>	
	<form name="message">
		<input name="usermsg" type="text" id="usermsg" size="63" />
		<input name="submitmsg" type="submit"  id="submitmsg" value="Send" />
	</form>
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
<script type="text/javascript">
// jQuery Document
$(document).ready(function(){
	
	//scrolldown on page load
	$("#chatbox").animate({ scrollTop: $("#chatbox").attr("scrollHeight") }, 'normal');
	
	//If user submits the form
	$("#submitmsg").click(function(){
		var clientmsg = $("#usermsg").val();
		if(clientmsg == '')
			return false;
		$.post("chat_post.php", {text: clientmsg});
		//window.alert("You're a stupid fuck. Now go away.");				
		$("#usermsg").attr("value", "");
		return false;
	});
	
	//Load the file containing the chat log
	function loadLog(){		
		var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
		$.ajax({
			url: "chat_log.php",
			cache: false,
			success: function(html){		
				$("#chatbox").html(html); //Insert chat log into the #chatbox div				
				var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
				if(newscrollHeight > oldscrollHeight){
					$("#chatbox").animate({ scrollTop: newscrollHeight }, 1); //Autoscroll to bottom of div
				}
		  	},
		});
		
	}
	setInterval (loadLog, 3000);	//Reload file every 1.5 seconds
	
	//If user wants to end session
	$("#exit").click(function(){ 
		var exit = confirm("Are you sure you want to end the session?");
		if(exit==true){window.location = 'chat.php?logout=true';}		
	});
});
</script>
<?php
}
?>
</body>
</html>