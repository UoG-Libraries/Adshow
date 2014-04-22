<?php

	if (isset($_POST["formSent"]) && $_POST["formSent"] == 'yes') {

		include 'db.php';

		$objDB = new Database();
		//$objDB->addPlaylist($_POST["department"]);
print_r($_POST);
		//header('Location: playlists.php');
		exit;

	}

	$page = 'adshow/admin/addDepartment.php';

	include 'header.php';

    include 'navigation.php';

    echo '<div id="contentcontainer">' . "\n";
    echo '  <h2>Add playlist</h2>' . "\n";
    echo '  <form action="addPlaylist.php" method="post" enctype="application/x-www-form-urlencoded">' . "\n";

    echo '    <input type="hidden" name="formSent" value="yes" />' . "\n";
    echo '    <input type="hidden" name="createdBy" value="' . $_SESSION["details"]["id"] . '" />' . "\n";

	echo '    <div>' . "\n";
    echo '    <label for="name">Name</label>' . "\n";
    echo '    <input type="text" name="name" id="name" />' . "\n";
	echo '    </div>' . "\n";

	echo '    <div>' . "\n";
    echo '    <label for="active">Active</label>' . "\n";
	echo '    <select name="active" id="active">' . "\n";
	echo '        <option value="0" selected="selected">No</selected>' . "\n";
	echo '        <option value="1">Yes</selected>' . "\n";
    echo '    </select>' . "\n";
	echo '    </div>' . "\n";

	echo '    <div id="buttons">' . "\n";
    echo '    <input type="reset" value="Cancel">' . "\n";
    echo '    <input type="submit" value="Add">' . "\n";
	echo '    </div>' . "\n";

    echo '  </form>' . "\n";
    echo '</div>' . "\n";

	include 'footer.php';

?>