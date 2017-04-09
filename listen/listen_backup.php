<!--<script type="text/javascript" src="http://wsbf.net/wsbf/jquery.cycle.all.pack.js"></script>-->
<?php
require_once("wsbf.php");
drupal_add_js('misc/listen.js');
drupal_add_js('misc/swfobject.js');
?>

<table width="100%">
<tr><td valign="top" style='padding-right: 12px' width="200">
<center>
<h3>iTunes</h3><a href="http://wsbf.net/high.m3u">High Quality</a><br/><a href="http://wsbf.net/low.m3u">Low Quality</a> <br />
<br />
<h3>Current Song</h3><div id="track"><img src="/misc/ajax-loader.gif"/></div><br />
<h3>Show</h3><div id="dj"><img src="/misc/ajax-loader.gif"/></div><br />
<h3>Current Internet Listeners</h3><div id="listeners"><img src="/misc/ajax-loader.gif"/></div>
<br />
<h3>Request Line</h3>(864) 656-WSBF<br /><i>(864) 656-9723</i><br>
<td valign="top" width="350">
<center>
<h3>Webcam</h3>
<div id="cam" style="text-align: center;">
	<div id="container"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this rotator.</div>
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
s2.addVariable('file', encodeURIComponent('http://wsbf.net:8002'));
s2.addVariable('skin', encodeURIComponent('/wizbif/modieus.swf'));
s2.write('preview');
</script>
</center>
</td>
</tr>
</table>
<br />
<center>
<?PHP
    if ($id > 0) {
       echo "";
	$dj_name = $dj;
       include('archives_flash.php');
       //echo "<a href='index.php?page=archives&id=$showID'>Archives for this show</a>";
    }
?>
</center>
