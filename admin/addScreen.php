<?php
include 'db.php';
$objDB = new Database();

if (isset($_POST["formSent"]) && $_POST["formSent"] == 'yes') {
    $objDB->addScreen($_POST["location"], $_POST["department"], $_POST["orientation"]);
    header('Location: screens.php');
}

$departmentList = $objDB->getDepartments();
include 'header.php';

?>
    <div>
        <h2>Add screen</h2>
        <form class="form-horizontal" action="addScreen.php" method="post" enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="formSent" value="yes"/>
            <?php if ("user" !== "superadmin") { ?>
                <div class="form-group">
                    <label for="department" class="col-sm-2 control-label">Department</label>
                    <div class="col-sm-10">
                        <select name="department" id="department" class="form-control">
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
            <?php } else { ?>
                <input type="hidden" name="department" value="<?php echo $_SESSION["department"] ?>"/>
            <?php } ?>
            <div class="form-group">
                <label for="location" class="col-sm-2 control-label">Location</label>
                <div class="col-sm-10">
                    <input type="text" name="location" id="location" class="form-control"/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <label class="radio-inline">
                        <input type="radio" name="orientation" id="inlineRadio1" value="0" CHECKED> Landscape
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="orientation" id="inlineRadio2" value="1"> Portrait
                    </label>
                </div>
            </div>
            <div>
                <input type="reset" value="Cancel" class="btn btn-primary">
                <input type="submit" value="Add" class="btn btn-primary">
            </div>
        </form>
    </div>
<?php

include 'footer.php';
?>