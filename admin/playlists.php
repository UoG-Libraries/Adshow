<?php

	// GET variables
	// action: delete, edit, update
	// id: screen id

	$page = 'adshow/admin/playlists.php';

	include 'header.php';
    include 'navigation.php';
    include 'db.php';

    $objDB = new Database();

	// handle delete playlist request
/*
	if ($_GET["action"] == 'del') {
		$objDB->deleteScreen($_GET["id"]);
		header("Location: screens.php");
		exit;
	}
*/

	echo '<div id="contentcontainer">' . "\n";

	// handle edit playlist request
	if ($_GET["action"] == 'edit') {
		// TODO
	} else {

		// show playlists
		$playlists = $objDB->getPlaylists();

		echo '  <h2>Playlists</h2>' . "\n";
		echo '  <table>' . "\n";
		echo '    <tr>' . "\n";
		echo '      <th scope="col">ID</th>' . "\n";
		echo '      <th scope="col">Name</th>' . "\n";
		echo '      <th scope="col">Active</th>' . "\n";
		echo '      <th scope="col">Created by</th>' . "\n";
		echo '    </tr>' . "\n";
		foreach ($playlists as $playlist) {
			echo '    <tr class="line">' . "\n";
			echo '      <td>' . $playlist["ID"] . '</td>' . "\n";
			echo '      <td>' . $playlist["name"] . '</td>' . "\n";
			if ($playlist["active"]) {
				echo '      <td>Yes</td>' . "\n";
			} else {
				echo '      <td>No</td>' . "\n";
			}
			echo '      <td>' . $playlist["sNumber"] . '</td>' . "\n";
			echo '      <td><a href="playlists.php?action=edit&amp;id=' . $playlist["ID"] . '"><img src="images/pencil5.png" title="Edit playlist" alt="Edit" /></a><a href="playlists.php?action=del&amp;id=' . $playlist["ID"] . '"><img src="images/cancel4.png" title="Delete this playlist" alt="Delete" /></a></td>' . "\n";
			echo '    </tr>' . "\n";
		}
		echo '  </table>' . "\n";
	}
	echo '</div>' . "\n";

	include 'footer.php';

?>