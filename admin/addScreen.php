<?php

	if (isset($_POST["formSent"]) && $_POST["formSent"] == 'yes') {

		include 'db.php';

		$objDB = new Database();
		$objDB->addScreen($_POST["location"], $_POST["department"]);

		header('Location: screens.php');
		exit;

	}

	$page = 'adshow/admin/addScreen.php';

	include 'header.php';

    include 'navigation.php';
    include 'db.php';

    $objDB = new Database();
    $departmentList = $objDB->getDepartments();

    echo '<div id="contentcontainer">' . "\n";
    echo '  <h2>Add screen</h2>' . "\n";
    echo '  <form action="addScreen.php" method="post" enctype="application/x-www-form-urlencoded">' . "\n";
    echo '    <input type="hidden" name="formSent" value="yes" />' . "\n";
	echo '    <div>' . "\n";
    echo '    <label for="department">Department</label>' . "\n";
    echo '    <select name="department" id="department" />' . "\n";
	foreach ($departmentList as $department) {
		if ($department["department"] != "Global") {
			echo '      <option value="' . $department["ID"] . '">' . $department["department"] . '</option>' . "\n";
		}
	}
    echo '    </select>' . "\n";
	echo '    </div>' . "\n";
	echo '    <div>' . "\n";
    echo '    <label for="location">Location</label>' . "\n";
    echo '    <input type="text" name="location" id="location" />' . "\n";
	echo '    </div>' . "\n";
	echo '    <div id="buttons">' . "\n";
    echo '    <input type="reset" value="Cancel">' . "\n";
    echo '    <input type="submit" value="Add">' . "\n";
	echo '    </div>' . "\n";
    echo '  </form>' . "\n";
    echo '</div>' . "\n";

	include 'footer.php';

?>