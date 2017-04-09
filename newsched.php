<?php

class psuedouser {
	var $uid;
}

function cmp( $a, $b )
{
  if(  $a->hour ==  $b->hour ){ return 0 ; }
  return ($a->hour < $b->hour) ? -1 : 1;
}

$link = mysql_connect('localhost') or die("Could not connect");
mysql_select_db('drupal') or die("Could not select database");

global $shows;
$shows = array();

function getShowName($drupalUID) {
   $query = "SELECT * FROM `profile_values` WHERE `fid`=9 AND `uid` = '$drupalUID' LIMIT 1";
   $result = mysql_query($query) or die("Query failed : " . mysql_error());
   $showname = mysql_fetch_array($result);
   return $showname['value'];
}

function getShowDesc($drupalUID) {
   $query = "SELECT * FROM `profile_values` WHERE `fid`=13 AND `uid` = '$drupalUID' LIMIT 1";
   $result = mysql_query($query) or die("Query failed : " . mysql_error());
   $showname = mysql_fetch_array($result);
   return $showname['value'];
}

function getShowType($drupalUID) {
   $query = "SELECT * FROM `profile_values` WHERE `fid`=11 AND `uid` = '$drupalUID' LIMIT 1";
   $result = mysql_query($query) or die("Query failed : " . mysql_error());
   $showtype = mysql_fetch_array($result);
   return $showtype['value'];
}

function getDJName($drupalUID) {
   $query = "SELECT * FROM `users` WHERE `uid`='$drupalUID' LIMIT 1";
   $result = mysql_query($query) or die("Query failed : " . mysql_error());
   $user = mysql_fetch_array($result);
   $psuedo = new psuedouser;
   $psuedo->uid = $user['uid'];
   profile_load_profile($psuedo);
   $name = $user['name'];
   if (isset($psuedo->profile_djalias)) {
      $name = $psuedo->profile_djalias;
   }
   return $name;
}

class showtime {
   var $name;
   var $djs = array();
   var $type;
   var $day;
   var $hour;
   var $desc;

   // for detecting ppl with same shows
   var $drupalTime;

   function listDJs() {
      $args = func_get_args();
      $withlinks = false;

      if($args > 0 && $args[0] == true) $withlinks = true;

      $list = "";
      foreach($this->djs as $dj) {
         if($withlinks) {
            $list .= "<a href='/user/" . $dj . "'>" . getDJName($dj) . "</a>";
         } else {
            $list .= getDJName($dj);
         }
         if($dj != end($this->djs)) {
            $list .= ", ";
         }
      }
      return $list;
   }

   function timeString($time) {
      $start = $time;
      $end = $time + 2;
      $string = "";

      if ($start < 12) {
         $string .= $start . "AM - ";
      } else {
         $string .= ($start - 12) . "PM - ";
      }

      if ($end < 12) {
         $string .= $end . "AM";
      } else {
         if ($end != 25) {
           $string .= ($end - 12) . "PM";
         } else {
           $string .="1AM";
         }
      }

      return $string;
   }

   function display() {
      $display = "<div class='" . $this->type . "'>\n";
      $display .= "<div id=\"showtitle\">\n";
      $display .= "<h3><p class='alignleft'>";
      if($this->name)
         $display .= $this->name . " - ";
      $display .= $this->listDJs(true);
      $display .= "</p><p class='aligntime'>";
      $display .= $this->timeString($this->hour);
      $display .= "</p></h3></div>\n";
      $display .= "<div style='clear: both;'></div>\n";
      if($this->desc != "") {
      	$display .= "<br/><div class='desc'>" . $this->desc . "</div>";
      }
      $display .= "</div>\n";
      return $display;
   }

   function __construct($drupalUID, $drupalValue) {
      $this->drupalTime = $drupalValue;
      $timeArray = explode(" ", $drupalValue);

      $this->day = date("N", strtotime($timeArray[0]))+1;
      if ($this->day == 8) $this->day = 1;
      //echo $this->day;
      $this->hour = substr($timeArray[1], 1);
      if(strstr($timeArray[2], "PM")) $this->hour += 12;

      $this->djs[] = $drupalUID;
      $this->name = getShowName($drupalUID);
      $this->desc = getShowDesc($drupalUID);
      $this->type = strtolower(getShowType($drupalUID));
   }

   function addDJ($uid) {
      $this->djs[] = $uid;
   }
}

function findShow($day, $hour) {
   global $shows;
   foreach ($shows as $show) {
      //echo $day .":" .$hour . "(" . $show->day . ":" . $show->hour . ") ";
      if(intval($show->day) == intval($day) && intval($show->hour) == intval($hour)) {
         return $show;
      }
   }
   return false;
}

function findShows($day) {
   global $shows;
   $dayshows = array();
   foreach ($shows as $show) {
      //echo $day .":" .$hour . "(" . $show->day . ":" . $show->hour . ") ";
      if(intval($show->day) == intval($day)) {
         $dayshows[] = $show;
      }
   }
   return $dayshows;
}

function newShow($drupalValue) {
   global $shows;
   foreach ($shows as $show) {
      if($show->drupalTime == $drupalValue) {
         return false;
      }
   }
   return true;
}

function addDJ($drupalUID, $drupalValue) {
   global $shows;
   foreach ($shows as $show) {
      if($show->drupalTime == $drupalValue) {
         $show->addDJ($drupalUID);
      }
   }
}

$query = "SELECT * FROM `profile_values` WHERE `fid`=10 AND `value` != '0'";
$result = mysql_query($query) or die("Query failed : " . mysql_error());
while($row = mysql_fetch_array($result)) {
   if(newShow($row['value'])) {
      $shows[] = new showtime($row['uid'], $row['value']);
   } else {
      addDJ($row['uid'], $row['value']);
   }
}
?>
<style type="text/css">
a:hover{
color: #DFE44F;
}
p{
margin: 0;
padding: 5px;
line-height: 1.5em;
text-align: justify;
}
#wrapper{
width: 550px;
margin: 0 auto;
}
.box{
}
.boxholder{
padding: 5px;
}
.tab{
float: left;
height: 32px;
width: 77px;
margin: 0 1px 0 0;
text-align: center;
}
.tabtxt{
margin: 0;
font-size: 12px;
font-weight: normal;
padding: 9px 0 0 0;
text-align: center;
}
.alignleft {
float: left;
}
.aligntime {
float: right;
color: #cfc0b2;
}
.desc {
background: #301e22;
padding: 10px;
margin-top: -23px;
}
.specialty {
border-width: 2px;
border-style: dotted;
border-color: #c8221f;
padding: 1px;
}
.rotation {
border-width: 2px;
border-style: dotted;
border-color: #4c343a;
padding: 1px;
}
</style>
<script type="text/javascript" src="/wizbif/scripts/prototype.lite.js"></script>
<script type="text/javascript" src="/wizbif/scripts/moo.fx.js"></script>
<script type="text/javascript" src="/wizbif/scripts/moo.fx.pack.js"></script>
<script type="text/javascript">
function init(){
	var stretchers = document.getElementsByClassName('box');
	var toggles = document.getElementsByClassName('tab');
	var myAccordion = new fx.Accordion(
		toggles, stretchers, {opacity: false, height: true, duration: 600}
	);
	//hash functions
	var found = false;
	toggles.each(function(h3, i){
		var div = Element.find(h3, 'nextSibling');
			if (window.location.href.indexOf(h3.title) > 0) {
				myAccordion.showThisHideOpen(div);
				found = true;
			}
		});
		if (!found) myAccordion.showThisHideOpen(stretchers[0]);
}
</script>
<div id="wrapper">
	<div id="content">
	<div class="tab" title="first"><h3 class="tabtxt"><a href="#">Sunday</a></div></h3>
	<div class="tab"><h3 class="tabtxt" title="second"><a href="#">Monday</a></h3></div>
	<div class="tab"><h3 class="tabtxt" title="third"><a href="#">Tuesday</a></h3></div>
	<div class="tab"><h3 class="tabtxt" title="fourth"><a href="#">Wednesday</a></h3></div>
	<div class="tab"><h3 class="tabtxt" title="fifth"><a href="#">Thursday</a></h3></div>
	<div class="tab"><h3 class="tabtxt" title="sixth"><a href="#">Friday</a></h3></div>
	<div class="tab"><h3 class="tabtxt" title="seventh"><a href="#">Saturday</a></h3></div>
	<div class="boxholder">
<?php
for ($i = 1; $i < 8; $i++) {
	$dayshows = findShows($i);
	usort($dayshows, 'cmp');
	echo "<div class=\"box\">\n";
	foreach ($dayshows as $show) {
		echo $show->display() . "<br/>\n";
	}
	echo "</div>\n";
}
?>
	</div>
</div>
</div>
<script type="text/javascript">
	Element.cleanWhitespace('content');
	init();
</script>
