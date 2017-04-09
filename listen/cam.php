<?PHP
   if ($refresh < 2)
      $refresh = 30;
		
	if (empty($image)) 
		$image = "studioa.jpg";
		
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>WSBF Music Director Tools</title>
    <link rel="stylesheet" type="text/css" href="http://wsbf.net/wp-content/themes/rounded-grey-blog-10/style.css" />
    <link rel="stylesheet" type="text/css" href="wsbf.css" />
</head>

<body class="cam">
<center>
<b>(Refreshing every <?PHP echo $refresh; ?> seconds.)</b>
<br>
<img src="<?PHP echo $image; ?>" border="1" width="320" height="240"><br>
<!-- <img src="http://wsbf.clemson.edu/grafx/black.gif" border=0 width=320 height=240><br> -->
 <font size=-2>View Camera 1: Refreshing every (<a href = "cam.php?refresh=2&image=studioa.jpg">2 seconds</a>) (<a href = "cam.php?refresh=30&image=studioa.jpg">30 seconds</a>)</font>
<br>
 <font size=-2>View Camera 2: Refreshing every (<a href = "cam.php?refresh=2&image=studiob.jpg">2 seconds</a>) (<a href = "cam.php?refresh=30&image=studiob.jpg">30 seconds</a>)</font>
</center>
</body>
</html>
