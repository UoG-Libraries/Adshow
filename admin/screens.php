<?php

	// GET variables
	// action: delete, edit, update
	// id: screen id

	$page = 'adshow/admin/screens.php';

	include 'header.php';
    include 'navigation.php';
    include 'db.php';

    $objDB = new Database();

	// handle delete screen request
	if ($_GET["action"] == 'del') {
		$objDB->deleteScreen($_GET["id"]);
		header("Location: screens.php");
		exit;
	}

	echo '<div id="contentcontainer">' . "\n";

	// handle edit screen request
	if ($_GET["action"] == 'edit') {
		// $screenDtetails = $objDB->getScreen($_GET["id"]);
	} else {

		// show screens list
		$screensList = $objDB->getScreensList();

		echo '  <h2>Screens</h2>' . "\n";
		echo '  <table>' . "\n";
		echo '    <tr>' . "\n";
		echo '      <th scope="col">Screen ID</th>' . "\n";
		echo '      <th scope="col">Department</th>' . "\n";
		echo '      <th scope="col">Location</th>' . "\n";
		echo '      <th scope="col">Actions</th>' . "\n";
		echo '    </tr>' . "\n";
		foreach ($screensList as $screen) {
			echo '    <tr class="line">' . "\n";
			echo '      <td>' . $screen["ID"] . '</td>' . "\n";
			echo '      <td>' . $screen["department"] . '</td>' . "\n";
			echo '      <td>' . $screen["location"] . '</td>' . "\n";
			echo '      <td><a href="screens.php?action=edit&amp;id=' . $screen["ID"] . '"><img src="images/pencil5.png" title="Edit screen details" alt="Edit" /></a><a href="#"><img src="images/song.png" title="View playlists for this screen" alt="Playlists" /></a><a href="screens.php?action=del&amp;id=' . $screen["ID"] . '"><img src="images/cancel4.png" title="Delete this screen" alt="Delete" /></a></td>' . "\n";
			echo '    </tr>' . "\n";
		}
		echo '  </table>' . "\n";
	}
	echo '</div>' . "\n";

	include 'footer.php';

?>