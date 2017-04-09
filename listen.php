<!--<script type="text/javascript" src="http://wsbf.net/wsbf/jquery.cycle.all.pack.js"></script>-->
<?php

//require_once("wsbf.php");
require_once("stream_conn.php");
/** this is included in every page via block_schedulenext.php **/
//drupal_add_js('misc/listen.js');
drupal_add_js('misc/swfobject.js');


global $user;
$alias = -1; // this value is tested for below, when including the chat page
if($username){
	$username = $user->name;
	$djquery = "SELECT * FROM djs WHERE drupal='$username'";
	$qdj = mysql_query($djquery) or die(mysql_error());
	
	//if user has an account but is not a DJ
	if(mysql_num_rows($qdj) < 1)
		$alias = $username;
	//else, the user is a DJ and shows up in that table
	else {
		$dj = mysql_fetch_array($qdj, MYSQL_ASSOC);
		$alias = ($dj['alias'] == '') ? $dj['name'] : $dj['alias'];
	}
}

?>
<div id='left' style='padding: 12px; width: 45%; float: left; text-align: center'>
	<h3>iTunes</h3>
	<a href="http://wsbf.net/high.m3u">High Quality</a>
	<br/>
	<a href="http://wsbf.net/low.m3u">Low Quality</a> 
	<br />
	<br />
	<h3>Current Song</h3>
	<div id="track_ajax"><img src="/misc/ajax-loader.gif"/></div>
	<br />
	<h3>Show</h3>
	<div id="dj_ajax"><img src="/misc/ajax-loader.gif"/></div>
	<br />
	<h3>Request Line</h3>
	<div>(864) 656-WSBF<br /><i>(864) 656-9723</i><br /><br /></div>

	<div id='playlist_ajax'></div>
</div>
<div id='right' style='padding:12px; width: 45%; float: right; text-align: center'>
	
	<h3>Webcam</h3>
	
	<div id="cam">
		<div id="container">
			<a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this rotator.
		</div>
		<script type="text/javascript">
			var s1 = new SWFObject("http://wsbf.net/wizbif/imagerotator.swf","rotator","320","240","7");
			s1.addVariable('file', encodeURIComponent('http://wsbf.net/camera/studioa.jpg'));
			s1.addVariable('showicons',false);
			s1.addVariable('transition','fade');
			s1.addVariable('shownavigation',false);
			s1.write("container");
		</script>
	</div> 
	<br/>
	<h3>Live Audio Stream</h3>
	<p id='preview'>Stream goes here.</p>
	<script type='text/javascript' src='/wizbif/swfobject.js'></script>
	<script type='text/javascript'>
		var s2 = new SWFObject('/wizbif/player.swf','player','320','31','9');
		s2.addParam('allowfullscreen','true');
		s2.addParam('allowscriptaccess','always');
		s2.addVariable('type', 'sound');
		s2.addVariable('title', 'WSBF-FM Live Stream');
		// Shoutcast stream - Brap.FM
		// s2.addVariable('file', encodeURIComponent('http://wsbf.net:8000/high'));
		s2.addVariable('file', encodeURIComponent('http://stream.wsbf.net:8000/high'));
		s2.addVariable('skin', encodeURIComponent('/wizbif/modieus.swf'));
		s2.write('preview');
	</script>
	
</div>
<div id='bottom' style='padding: 12px; width: 90%; clear: both; text-align: center'>

<script id="sid0010000019982581110">(function() {function async_load(){s.id="cid0010000019982581110";s.src='http://st.chatango.com/js/gz/emb.js';s.style.cssText="width:385px;height:495px;";s.async=true;s.text='{"handle":"wsbfchat","styles":{"a":"FF6600","b":100,"c":"FFFFFF","d":"FFFFFF","g":"333333","j":"333333","k":"FF6600","l":"FF6600","m":"FF6600","n":"FFFFFF","p":"12","s":1}}';var ss = document.getElementsByTagName('script');for (var i=0, l=ss.length; i < l; i++){if (ss[i].id=='sid0010000019982581110'){ss[i].id +='_';ss[i].parentNode.insertBefore(s, ss[i]);break;}}}var s=document.createElement('script');if (s.async==undefined){if (window.addEventListener) {addEventListener('load',async_load,false);}else if (window.attachEvent) {attachEvent('onload',async_load);}}else {async_load();}})();</script>

<!-- Previous chat module written by David Cohen -->
	<?php
	//If you're logged into Drupal, you don't have to sign into the chat!
//	if($alias != -1)
//		echo "<iframe src='wizbif/chat.php?alias=$alias' width='100%' height='600px' />";
//	else 
//		echo "<iframe src='wizbif/chat.php' width='100%' height='600px' />";
	?>
</div>