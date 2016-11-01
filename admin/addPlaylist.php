<?php
include 'db.php';
$objDB = new Database();

if (isset($_POST["formSent"]) && $_POST["formSent"] == 'yes') {
    $objDB->addPlaylist($_POST["name"], $_POST["active"], $_POST["department"]);
    header('Location: playlists.php');
    exit;

}

$departmentList = $objDB->getDepartments();

include_once 'user.php';
include 'header.php';

?>
    <div>
        <h2>Add playlist</h2>
        <form class="form-horizontal" action="addPlaylist.php" method="post"
              enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="formSent" value="yes"/>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" name="name" id="name" class="form-control"/>
                </div>
            </div>
            <?php if (User::getCurrentUser()->permission == Permission::Superadmin) { ?>
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
                <input type="hidden" name="department" value="<?php echo User::getCurrentUser()->getDepartmentID()?>"/>
            <?php } ?>
            <div class="form-group">
                <label for="active" class="col-sm-2 control-label">Active</label>
                <div class="col-sm-10">
                    <select name="active" id="active" class="form-control">
                        <option value="0" selected="selected">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
            </div>

            <div>
                <input type="reset" value="Cancel" class="btn btn-primary">
                <input type="submit" value="Add" class="btn btn-primary">
            </div>
        </form>
    </div>
<?php include 'footer.php'; ?>