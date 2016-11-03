<?php
include 'db.php';
$objDB = new Database();

if (isset($_POST["formSent"]) && $_POST["formSent"] == 'yes') {
    $playlistID = $objDB->addPlaylist($_POST["name"], $_POST["active"], $_POST["department"], $_POST["orientation"]);
    print_r($_POST);
    if (isset($_POST['addSlide'])) {
        header("Location: addSlide.php?playlistID=$playlistID");
    } else {
        header('Location: playlists.php');
    }
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
                <input type="hidden" name="department" value="<?php echo User::getCurrentUser()->getDepartmentID() ?>"/>
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
                <a href="playlists.php" class="btn btn-primary">Cancel</a>
                <input type="submit" value="Add" class="btn btn-primary">
            </div>

            <div class="h-space"></div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Slides</h3>
                </div>
                <div class="panel-body">
                    <div>There are no slides yet.</div>
                    <input type="submit" value="Add slide" class="btn btn-default" name="addSlide">
                </div>
            </div>
        </form>
    </div>
<?php include 'footer.php'; ?>