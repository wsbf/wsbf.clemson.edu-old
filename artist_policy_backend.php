<?php
require_once('class.artist.php');
require_once('functions.string.php');
require_once($_SERVER['DOCUMENT_ROOT'] . "PFBC/Form.php");

if(!empty($_POST["artistName"])) {










	$a = new Artist();
	$aname = $_POST['artistName'];
	$a->setArtistName($aname);
	$aname = titleCase($aname);

	$dontplay = array(
	"Anyone caught playing $aname on WSBF is to be immediately hanged, drawn, and quartered.",
	"I find $aname <a href='http://www.youtube.com/watch?v=9qLFJuCCMpM'>shallow and pedantic</a>. And against the music policy.",
	"If this cat spoke English, he'd say that you can't play $aname on WSBF. And he looks pretty serious. So don't.<br /><img src='http://www.funnyjunkz.com/wp-content/uploads/2011/06/funny-cat1.jpg'></img>",
	"May the fleas of a thousand camels infest the crotch of he who plays $aname on WSBF. And may his arms be too short to scratch it.",
	"$aname is not to be played on WSBF. However, the Greek philosopher <a href='http://en.wikipedia.org/wiki/Chrysippus'>Chrysippus</a> died from laughing after watching his drunk donkey eat figs.",
	"Don't play $aname on WSBF. Not no, but hell no.",
	"What do $aname and Creed have in common? If you play them on WSBF, I'll kick your ass.",
	"No, you can't play $aname on WSBF. But have you seen their new <a href='http://www.youtube.com/watch?v=dQw4w9WgXcQ'>video?</a>",
	"Unfortunately unplayable. But <a href='http://en.wikipedia.org/wiki/London_Beer_Flood'>this</a> would be a pretty damned cool way to die."
);
	$insult = array_rand(array_flip($dontplay));

	switch( $a->policyCheck() ){
		case 0:
			echo "$aname meets music policy for all shows.";
			break;
		case 1:
			echo "$aname meets music policy for specialty shows only.";
			break;
		case 2:
			echo $insult;
			break;
	}
	unset($a);
}

?>
