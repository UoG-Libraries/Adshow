<?php

	// GET variables
	// action: delete, edit
	// id: department id

	$page = 'adshow/admin/departments.php';

	include 'header.php';
    include 'navigation.php';
    include 'db.php';

    $objDB = new Database();

	// handle delete department request
	if (isset($_GET["action"]) && $_GET["action"] == 'del') {
		$objDB->deleteDepartment($_GET["id"]);
		header("Location: departments.php");
		exit;
	}

	echo '<div id="contentcontainer">' . "\n";

	// handle edit department request
	if (isset($_GET["action"]) && $_GET["action"] == 'edit') {
		$departmentDetails = $objDB->getDepartment($_GET["id"]);
	} else {

		// show departments list
		$departmentsList = $objDB->getDepartmentsAndOwner();

		echo '  <h2>Departments</h2>' . "\n";
		echo '  <table>' . "\n";
		echo '    <tr>' . "\n";
		echo '      <th scope="col">ID</th>' . "\n";
		echo '      <th scope="col">Department</th>' . "\n";
		echo '      <th scope="col">Contact</th>' . "\n";
		echo '      <th scope="col">Actions</th>' . "\n";
		echo '    </tr>' . "\n";
		foreach ($departmentsList as $department) {
			echo '    <tr class="line">' . "\n";
			echo '      <td>' . $department["ID"] . '</td>' . "\n";
			echo '      <td>' . $department["department"] . '</td>' . "\n";
			echo '      <td>' . $department["sNumber"] . '</td>' . "\n";
			echo '      <td><a href="departments.php?action=edit&amp;id=' . $department["ID"] . '"><img src="images/pencil5.png" title="Edit department details" alt="Edit" /></a><a href="#"><img src="images/song.png" title="View playlists for this department" alt="Playlists" /></a><a href="departments.php?action=del&amp;id=' . $department["ID"] . '"><img src="images/cancel4.png" title="Delete this department" alt="Delete" /></a></td>' . "\n";
			echo '    </tr>' . "\n";
		}
		echo '  </table>' . "\n";
	}
	echo '</div>' . "\n";

	include 'footer.php';

?>