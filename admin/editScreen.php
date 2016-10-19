<?php
/**
 * User: Raphael Jenni
 * Date: 18/10/2016
 */


if (isset($_POST["formSent"]) && $_POST["formSent"] == 'yes') {

    include 'db.php';

    $objDB = new Database();
    $objDB->editScreen($_POST["location"], $_POST["department"], $_POST['orientation'], $_POST["id"]);

    header('Location: screens.php');
    exit;

}

$page = 'adshow/admin/addScreen.php';
include 'header.php';
include 'db.php';


$objDB = new Database();
$departmentList = $objDB->getDepartments();

$id = $_GET["id"];
$screen = $objDB->getScreen($id)[0];


?>
    <div>
        <h2>Edit screen</h2>
        <form class="form-horizontal" action="editScreen.php" method="post" enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="formSent" value="yes"/>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="form-group">
                <label for="department" class="col-sm-2 control-label">Department</label>
                <div class="col-sm-10">
                    <select name="department" id="department" class="form-control">
                        <?php
                        foreach ($departmentList as $department) {
                            if ($department["department"] != "Global") {
                                ?>
                                <option
                                    value="<?php echo $department["ID"] ?>"
                                    <?php echo $department["ID"] == $screen['departmentIDfk'] ? "selected" : "" ?>
                                ><?php echo $department["department"] ?> </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class=" form-group
                        ">
                <label for="location" class="col-sm-2 control-label">Location</label>
                <div class="col-sm-10">
                    <input type="text" name="location" id="location" class="form-control"
                           value="<?php echo $screen['location'] ?>"/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <label class="radio-inline">
                        <input type="radio" name="orientation" id="inlineRadio1" value="0" <?php echo $screen["orientation"] == '0' ? 'CHECKED' : '' ?>> Landscape
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="orientation" id="inlineRadio2" value="1" <?php echo $screen["orientation"] == '1' ? 'CHECKED' : '' ?>> Portrait
                    </label>
                </div>
            </div>
            <div>
                <a href="screens.php" class="btn btn-primary">Cancel</a>
                <input type="submit" value="Save" class="btn btn-primary">
            </div>
        </form>
    </div>
<?php

include 'footer.php';
?>