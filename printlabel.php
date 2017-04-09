<?php
require("connect.php");
require('fpdf.php');
/**
$albums = array();
$albums[] = "H189";
$albums[] = "H193";
$albums[] = "A193";
$albums[] = "B193";
print_r($albums);
**/
$albums[0] = $_GET['a1'];
$albums[1] = $_GET['a2'];
$albums[2] = $_GET['a3'];
$albums[3] = $_GET['a4'];

class PDF extends FPDF {
	function LabelLayout($x,$y,$albumNo,$title,$artist,$genre,$review,$reviewer,$reccs,$noairs) {
		$width = 3.3;
		$linespace = 0.152;
		$ladywidth = 0.55;
		$ladyoffset = 0.002;
		$alnowidth = 0.45;
		$aralgenwidth = 0.49;
		$noairwidth = 0.83;
		$rotspacer = 6.0;
		

		$this->SetXY($x,$y);
		$this->SetFont('','',9);
		
		//$this->SetLeftMargin($x);
		//$this->SetRightMargin($x+$width);
		$this->SetTopMargin($y);
		$this->Image('lady.JPG',$x,$y,$ladywidth-$ladyoffset);

		//rotation
		$this->SetX($x+($width-$alnowidth)-($rotspacer*$linespace));
		$this->Cell($linespace,$linespace,"N","LTRB",0,'C');
		$this->Cell($linespace,$linespace,"H","LTRB",0,'C');
		$this->Cell($linespace,$linespace,"M","LTRB",0,'C');
		$this->Cell($linespace,$linespace,"L","LTRB",0,'C');

		//album id
		$this->SetFont('','B',16);
		$this->SetX($x+($width-$alnowidth)-0.01);
		$this->Cell($alnowidth,$linespace,$albumNo,0,1,'R');

		//artist
		$this->SetX($x+$ladywidth);
		$this->SetFont('','B',9);
		$this->Cell($aralgenwidth,$linespace,"Artist: ");
		$this->SetFont('','');
		$this->MultiCell($width-$ladywidth-$aralgenwidth,$linespace,$artist,0,'L');

		//album
		$this->SetX($x+$ladywidth);
		$this->SetFont('','B');
		$this->Cell($aralgenwidth,$linespace,"Album: ");
		$this->SetFont('','');
		$this->MultiCell($width-$ladywidth-$aralgenwidth,$linespace,$title);

		//genre
		$this->SetX($x+$ladywidth);
		$this->SetFont('','B');
		$this->Cell($aralgenwidth,$linespace,"Genre: ");
		$this->SetFont('','');
		$this->MultiCell($width-$ladywidth-$aralgenwidth,$linespace,$genre);

		//review
		$this->SetX($x);
		$this->SetFont('','B',9);
		$this->Cell($width,$linespace,"Property of WSBF-FM Clemson - 88.1",'',1,'C');
		
		$this->SetFont('','',9);
		$this->SetX($x);
		$review = convert_smart_quotes($review);
		$this->MultiCell($width,$linespace,$review,"LTR",'L');

		//reviewer
		$this->SetX($x);
		$this->SetFont('','B','');
		$this->Cell(0.8,$linespace,"Reviewed by:", "L",'L');
		$this->SetFont('','',9);
		$this->Cell($width-0.8,$linespace,$reviewer,"RB",1,'L');
		$this->SetFont('','',9);

		//reccomended
		if(sizeof($reccs) > 0) {

			$this->SetFont('','',9);
			$reccList = "";
			$this->SetFont('','B','');
			$this->SetX($x);
			$this->Cell($width,$linespace,"Recommended Tracks:", "TR",'L',2);		
			foreach ($reccs as $recc) {
				$reccList .= $recc . ", ";
			}
			$this->SetX($x);
			$reccList = substr_replace($reccList,"",-2);
			$this->SetFont('','',9);			
			$this->MultiCell($width,$linespace,"                    " . $reccList,'LR','L');
		} else {
			$this->SetX($x);
			$this->SetFont('','B');
			$this->Cell($width,$linespace,"Album Apparently Has No Recommended Tracks",'',1,'C');
			$this->SetFont('','',9);
		}

		//no-air
		if(sizeof($noairs) > 0) {

			$this->SetFont('','B',9);
			$noairList = "";
			$this->SetX($x);
			$this->Cell($width,$linespace,"No-Air Tracks:","TR",'L',2);
			foreach ($noairs as $noair) {
				$noairList .= $noair . ", ";
			}
			$this->SetX($x);
			$this->SetFont('','',9);
			$noairList=substr_replace($noairList,"",-2);
			$this->MultiCell($width,$linespace,"               " . $noairList,'LRTB','L');
		} else {
			$this->SetX($x);
			$this->SetFont('','B');
			$this->Cell($width,$linespace,"Album Is FCC Clean",'T',1,'C');
			$this->SetFont('','',9);
		}

	}
}

function getArtist($aID) {
	$query = "SELECT * FROM libartist WHERE aID = '$aID' LIMIT 1";
	$artist = "NOT IN DATABASE";
	$result = mysql_query($query);
	while($entry = mysql_fetch_array($result)) {
		$artist = $entry['aPrettyArtistName'];
	}
	return $artist;
}

function getRecc($cID) {
	$query = "SELECT * FROM libtrack WHERE t_cID = '$cID'";
	$tracks = array();
	$result = mysql_query($query);
	while($entry = mysql_fetch_array($result)) {
		if ($entry['tRecc']) {
			$tracks[] = $entry['tTrackNo'] . "." . $entry['tTrackName'];
		}
	}
	return $tracks;
}

function getNoAir($cID) {
	$query = "SELECT * FROM libtrack WHERE t_cID = '$cID'";
	$tracks = array();
	$result = mysql_query($query);
	while($entry = mysql_fetch_array($result)) {
		if (!$entry['tClean']) {
			$tracks[] = $entry['tTrackNo'];
		}
	}
	return $tracks;
}

$pdf=new PDF('P', 'in', 'A4');
$pdf->AddPage();
$pdf->AddFont('Basicmanual','','svbasicmanual.php');
$pdf->AddFont('Basicmanual','B','svbasicmanual-bold.php');
$pdf->SetFont('Basicmanual','',12);

function convert_smart_quotes($string) 
{ 
	$search = array(chr(0xe2) . chr(0x80) . chr(0x98),
					chr(0xe2) . chr(0x80) . chr(0x99),
					chr(0xe2) . chr(0x80) . chr(0x9c),
					chr(0xe2) . chr(0x80) . chr(0x9d),
					chr(0xe2) . chr(0x80) . chr(0x93),
					chr(0xe2) . chr(0x80) . chr(0x94));

	$replace = array("'",
					 "'",
					 '"',
					 '"',
					 "-",
					 "-");
 
    return str_replace($search, $replace, $string); 
}

$list = "";
$concat = "";
foreach ($albums as $album) {
	$list .= $concat . "cAlbumNo = '" . $album . "'";
	$concat = " or ";
}

$query = "SELECT * FROM libcd WHERE " . $list;
$result = mysql_query($query);

$x = 0.63;
$y = 0.62;

$section = 1;
while($review = mysql_fetch_array($result)) {
	$albumNo = $review['cAlbumNo'];
	$title = $review['cAlbumName'];
	$reviewtext = $review['cReview'];
/** reviewtext above! **/
	$reviewer = $review['cReviewer'];
	$reccs = getRecc($review['cID']);
	$noairs = getNoAir($review['cID']);
	$artist = getArtist($review['c_aID']);
	$genre = $review['cGenre'];
	$pdf->LabelLayout($x,$y,$albumNo,$title,$artist,$genre,$reviewtext,$reviewer,$reccs,$noairs);

	
	if ($section == 1) {
		$x = 4.63; //110,11
		$y = 0.62;
	} else if ($section == 2) {
		$x = 0.63; //0,152
		$y = 5.62;
	} else if ($section == 3) {
		$x = 4.63; //110,152
		$y = 5.62;
	} 
	
	$section++;
}
$pdf->Output();
?>