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
		$width = 100;
		
		/** start zach's modifications for avery 5168 **/
		$x += 1.5; $y += 2; //1.5,2
		$width = 92; //92
		/** end modifications **/
		
		$this->SetXY($x,$y);
		$this->SetFont('','',8);
		
		$this->Image('lady.JPG',$x,$y);
		$this->SetLeftMargin(18+$x);
		//$this->SetY($this->GetY());	

		//artist
		//$y = $this->GetY();
		$this->SetFont('','B');
		$this->Cell(15,5,"Artist");
		$this->SetFont('','');
		$y = $this->GetY();
		$this->MultiCell(50,5,$artist);
		
		//album id
		$this->SetY($y);
		$this->SetFont('','B');
		$this->Cell(65);
		$this->Cell(15,5,$albumNo);
		$this->SetFont('','');

		//album
		$this->SetY($y + 8);
		$y = $this->GetY();
		$this->SetFont('','B');
		$this->Cell(15,5,"Album");
		$this->SetFont('','');
		$y = $this->GetY();
		$this->MultiCell(100,5,$title);

		//rotation
		$this->SetY($y);
		$this->Cell(61);
		$this->Cell(5,5,"N","LTRB");
		$this->Cell(5,5,"H","LTRB");
		$this->Cell(5,5,"M","LTRB");
		$this->Cell(5,5,"L","LTRB");

		$this->SetLeftMargin(10);

		//genre
		$this->SetY($y + 12);
		$this->SetX($x);
		$this->SetFont('','B');
		$this->Cell(20,5,"Genre(S):");
		$this->SetFont('','');
		$this->MultiCell(50,5,$genre);

		//review
		$this->SetX($x);
		$this->SetFont('','B',8);
		$this->Cell($width,5,"Property of WSBF-FM Clemson - 88.1",'',1,'C');
		$y = $this->GetY();
		
		$this->SetFont('','',8);
		$this->SetX($x);
		$this->MultiCell($width,5,$review,"LTRB");

		//reviewer
		$this->SetX($x);
		$this->MultiCell($width,5,"Reviewed by " . $reviewer);
		$this->SetFont('','',8);

		//$this->SetY($y + 65);
		
		//reccomended
		if(sizeof($reccs) > 0) {
			$this->SetX($x);
			$this->SetFont('','B');
			$this->Cell($width,5,"Recommended Tracks",'',1,'C');

			$this->SetFont('','',8);
			$reccList = "";
		
			foreach ($reccs as $recc) {
				$reccList .= $recc . ", ";
			}
			$this->SetX($x);
			$this->MultiCell($width,5,$reccList,'LRTB');
		} else {
			$this->SetX($x);
			$this->SetFont('','B');
			$this->Cell($width,5,"Album Has No Recommended Tracks",'',1,'C');
			$this->SetFont('','',8);
		}
		//no-air
		if(sizeof($noairs) > 0) {
			$this->SetX($x);
			$this->SetFont('','B',8);
			$this->Cell($width,5,"No-Air Tracks",'',1,'C');

			$this->SetFont('','',8);
			$noairList = "";
			foreach ($noairs as $noair) {
				$noairList .= $noair . ", ";
			}
			$this->SetX($x);
			$this->MultiCell($width,5,$noairList,'LRTB');
		} else {
			$this->SetX($x);
			$this->SetFont('','B');
			$this->Cell($width,5,"Album Is FCC Clean",'',1,'C');
			$this->SetFont('','',8);
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
			$tracks[] = $entry['tTrackNo'] . ". " . $entry['tTrackName'];
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
			$tracks[] = $entry['tTrackNo'] . ". " . $entry['tTrackName'];
		}
	}
	return $tracks;
}

$pdf=new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

$list = "";
$concat = "";
foreach ($albums as $album) {
	$list .= $concat . "cAlbumNo = '" . $album . "'";
	$concat = " or ";
}

$query = "SELECT * FROM libcd WHERE " . $list;
$result = mysql_query($query);

$x = 0;
$y = 11;

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

	$section++;
	if ($section == 2) {
		$x = 110; //110,11
		$y = 11;
	} else if ($section == 3) {
		$x = 0; //0,152
		$y = 152;
	} else if ($section == 4) {
		$x = 110; //110,152
		$y = 152;
	} else if ($section == 5) {
		$x = 0; //0,11
		$y = 11;
		$section = 1;
	}
}
$pdf->Output();
?>