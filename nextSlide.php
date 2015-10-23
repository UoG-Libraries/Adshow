<?php

	// get post data
	$screen = $_POST['screen'];
	$playlist = $_POST['playlist'];
	$slide = $_POST['slide'];

	// local playlist currently playing
	if ($playlist == 0) {
	
		switch ($screen) {
			case '1':
				include 'parkData.php';
				break;
			case '2':
				include 'fchData.php';
				break;
			case '3':
				include 'oxstallsData.php';
				break;
			case '4':
				include 'suData.php';
				break;
		}

		if ($slide == count($slideArray)) {

			// last slide played, change to global playlist
			include 'globalData.php';

			if (count($globalSlideArray) == 0) {
				// no slides in global playlist, start again
				echo '{"action":"reload"}';
			} else {
				$playlist = 1;
				$slide = 1;				
				echo json_encode($globalSlideArray[$slide]);
			}
			
		} else {

			//  play next slide
			$slide++;
			$slideArray[$slide]['slide'] = $slide;
			echo json_encode($slideArray[$slide]);

		}

	// global playlist currently playing	
	} else {

		include 'globalData.php';

		if ($slide == count($globalSlideArray)) {

			// last slide played, start again
			echo '{"action":"reload"}';

		} else {

			// play next slide
			$slide++;
			$globalSlideArray[$slide]['slide'] = $slide;

			echo json_encode($globalSlideArray[$slide]);

		}

	}
	

?>