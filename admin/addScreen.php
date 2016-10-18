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

include 'db.php';

$objDB = new Database();
$departmentList = $objDB->getDepartments();

?>
<div>
    <h2>Add screen</h2>
    <form class="form-horizontal" action="addScreen.php" method="post" enctype="application/x-www-form-urlencoded">
        <input type="hidden" name="formSent" value="yes"/>
        <div class="form-group">
            <label for="department" class="col-sm-2 control-label">Department</label>
            <div class="col-sm-10">
                <select name="department" id="department" class="form-control"/>
                <?php
                foreach ($departmentList as $department) {
                    if ($department["department"] != "Global") {
                        echo '      <option value="' . $department["ID"] . '">' . $department["department"] . '</option>' . "\n";
                    }
                }
                ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="location" class="col-sm-2 control-label">Location</label>
            <div class="col-sm-10">
                <input type="text" name="location" id="location" class="form-control"/>
            </div>
        </div>
        <div id="buttons">
            <input type="reset" value="Cancel" class="btn btn-primary">
            <input type="submit" value="Add" class="btn btn-primary">
        </div>
    </form>
</div>
<?php

include 'footer.php';
?>
?>