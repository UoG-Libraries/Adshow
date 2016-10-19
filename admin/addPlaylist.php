<?php

if (isset($_POST["formSent"]) && $_POST["formSent"] == 'yes') {

    include 'db.php';

    $objDB = new Database();
    $objDB->addPlaylist($_POST["department"],$_POST["name"], $_POST["active"]);
    header('Location: playlists.php');
    exit;

}

$page = 'adshow/admin/addPLaylist.php';

include 'header.php';

?>
    <div>
        <h2>Add playlist</h2>
        <form class="form-horizontal" action="addPlaylist.php" method="post"
              enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="formSent" value="yes"/>
            <input type="hidden" name="department" value="<?php echo $_SESSION["department"] ?>"/>

            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" name="name" id="name" class="form-control"/>
                </div>
            </div>

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