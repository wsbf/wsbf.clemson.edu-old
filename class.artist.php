<?php
/*	David Cohen - 06/15/2011
	These functions are made to check whether an artist is allowed to be played on rotation shows, specialty shows, or both.

	I'm starting this Artist class; hopefully this will be expanded and modified come the new website
*/
require_once('class.xhttp.php');
require_once('functions.string.php');

class Artist{
	private $artistName;
	// other private variables to come - id, etc

	public function setArtistName($name){
		$this->artistName = $name;
	}
	public function getArtistName(){
		return($this->artistName);
	}
	private function riaaCheck(){

		// returns 0 if there are no records - playable on rotation shows
		// returns 1 if no records in last two years; OK for specialty, not rotation
		// returns 2 if records found in last two years; not ok for anyone

		$cleanName = removeCommonWords($this->artistName);
		$nameArr = explode(' ', $cleanName);

		// might be human name, so maybe we should check...
		if(count($nameArr) == 2)
			$humanName = $nameArr[1] . ", " . $nameArr[0];

		$result = 0;
			$riaa_fetch = array();
			$riaa_fetch['normal']['post'] = array(
				'advanced' => '1',
	 //			'terms' => $cleanName,
				'advancedArtist' => "%$cleanName%",
				'advancedFormat' => 'ALBUM',
				);

			$response = xhttp::fetch('http://www.riaa.com/search/searchgp.php', $riaa_fetch['normal']);

			$res = json_decode($response['body'], true);
			if($res['error'] != '')
				return(-1);
//var_dump($response);
			// if there are no records at all, check to see if human
			if($res['totalrecords'] == 0 && !empty($humanName)){
						$riaa_fetch['human']['post'] = array(
				'advanced' => '1',
	 //			'terms' => $cleanName,
				'advancedArtist' => "%$humanName%",
				'advancedFormat' => 'ALBUM',
				);

				$response = xhttp::fetch('http://www.riaa.com/search/searchgp.php', $riaa_fetch['human']);
				$resA = json_decode($response['body'], true);
					if($resA['error'] != '')
						die('Error: $resA[\'error\']');
				if($resA['totalrecords'] != 0)
					$res = $resA;
			}
			
	/* 
		echo "Total number of records: " . $res['totalrecords'] . "<br />";
		echo "
			<table><tr>
				<th>Artist</th>
				<th>Album</th>
				<th>Certification</th>
				<th>Certification Date</th>
				<th>Release Date</th>
			</tr>";

		foreach($res['resultset'] as $entry){
			echo "<tr><td>" . $entry['Artist'] . "</td><td>" . $entry['Title'] . "</td><td>" . $entry['formattedAwardAndDescription'] . "</td><td>" . $entry['CertificationDate'] . "</td><td>" . $entry['ReleaseDate'] . "</td></tr>";
		}

		echo "</table>";
*/

			if($res['totalrecords'] == 0)
				return(0);
			// check the most recent result. if sooner than two years ago, return 2.
			if(	strtotime($res['resultset'][0]['CertificationDate']) >= strtotime('-2 Years')	)
				return(2);

			// otherwise, return 1 [no rotation play.]
			else return(1);


	}

	private function billboardCheck(){
	// use the billboard api to check whether an artist fits the music policy
	// returns 0 for playable on all shows, 1 for specialty only, 2 for no shows
	// http://developer.billboard.com/docs/read/The_Chart_Service/Resources/Search
	// refer to passwords file for credentials


		$cleanName = removeCommonWords($this->artistName);
		$cleanName = preg_replace('/[\`\~\!\@\#\$\%\^\*\(\)\;\,\.\'\/\_]/i', '-',$cleanName);

		$base_url = "http://api.billboard.com/apisvc/chart/v1/list";
		$authkey = "px26rpdxpwaszrz4mqbktds5";
//		$authkey = "txkttmnu46cb7q62dh9fdbp7";


		$data = array('id'=> '379',
			'format' => 'json',
			'artist'=> $cleanName,
			'sdate'=> '1900-01-01',
			'edate'=> date('Y-m-d'),
			'api_key' => $authkey
		);

		$request = $base_url . "?" . http_build_query($data);

	//	echo $request ."<br />";
		$json = xhttp::fetch($request);

		// unsuccessful request is probably due to hitting request limit (2 per second), so just wait a second
		if(!$json['successful']){
		$errorCode = $json['headers']['x-mashery-error-code'];
			if($errorCode == 'ERR_403_DEVELOPER_OVER_QPS'){
				sleep(1);
				return( $this->billboardCheck() );
			}
			else return(-1);
			}

		// implied else

		// get json_decode array
		$response = json_decode($json['body'], true);

	//	var_dump($json);

		// no results; playable on rotation
		if(empty($response['searchResults']))
			return(var_dump($json));
		if($response['searchResults']['totalRecords'] == 0)
			return(0);

		// results in last two years - not ok for anyone
		if( strtotime( $response['searchResults']['chartItem'][0]['chart']['issueDate']) >= strtotime('-2 years'))
			return(2);

		// else playable only on specialty shows
		else return(1);


		}

	public function policyCheck(){
	// returns 0 if anyone can play it, 1 if only specialty, and 2 if neither
	// checks to see if an artist fits the music policy
	// rotation shows: no artists with Au/Pt albums or billboard hot 100 hits
	// speciality: no artists with these that have certified in the last 2 years
//	echo $this->riaaCheck() . "<br />";
//	echo $this->billboardCheck() . "<br />";

		$riaa = $this->riaaCheck();
		$billboard = $this->billboardCheck();
		if($riaa == -1 || $billboard == -1)
			return(-1);
		else
			return(max ($riaa, $billboard) );

	}



}

?>
