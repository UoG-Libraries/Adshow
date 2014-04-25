<?php

	// get post data
	$screen = $_POST['screen'];
	$playlist = $_POST['playlist'];
	$slide = $_POST['slide'];

	// set next slide
	if ($_POST['slide'] < 8) {
		$slide++;
	} else {
		$slide = 1;
	}

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

	echo json_encode($slideArray[$slide]);

?>