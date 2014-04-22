<?php

	if (isset($_POST["formSent"]) && $_POST["formSent"] == 'yes') {

		include 'db.php';

		$objDB = new Database();
		$objDB->addDepartment($_POST["department"],$_POST["owner"]);

		header('Location: departments.php');
		exit;

	}

	$page = 'adshow/admin/addDepartment.php';

	include 'header.php';

    include 'navigation.php';

    echo '<div id="contentcontainer">' . "\n";
    echo '  <h2>Add department</h2>' . "\n";
    echo '  <form action="addDepartment.php" method="post" enctype="application/x-www-form-urlencoded">' . "\n";
    echo '    <input type="hidden" name="formSent" value="yes" />' . "\n";

	echo '    <div>' . "\n";
    echo '    <label for="department">Department Name</label>' . "\n";
    echo '    <input type="text" name="department" id="department" />' . "\n";
	echo '    </div>' . "\n";

	echo '    <div>' . "\n";
    echo '    <label for="owner">Contact (sNumber)</label>' . "\n";
    echo '    <input type="text" name="owner" id="owner" />' . "\n";
	echo '    </div>' . "\n";

	echo '    <div id="buttons">' . "\n";
    echo '    <input type="reset" value="Cancel">' . "\n";
    echo '    <input type="submit" value="Add">' . "\n";
	echo '    </div>' . "\n";
    echo '  </form>' . "\n";
    echo '</div>' . "\n";

	include 'footer.php';

?>