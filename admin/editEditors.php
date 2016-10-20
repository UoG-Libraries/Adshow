<?php
if (!isset($_GET['id'])) {
	header('HTTP/1.1 404 Not Found');
}

include_once 'user.php';
include 'header.php';

if (!isset($_GET['id'])) {
	include '404.php';
	die();
}

$userID = $_GET['id'];
$user = User::getUserWithID($userID);
print_r($user);

?>
    <div>
        <h2>Edit Editor</h2>
        <form class="form-horizontal" action="editPlaylist.php" method="post" enctype="application/x-www-form-urlencoded">
            <div class="form-group">
                <label for="sNumb" class="col-sm-2 control-label">S-Number</label>
                <div class="col-sm-10">
                    <input type="text" id="sNumb" name="sNumb" class="form-control" />
                </div>
            </div>

			<div class="form-group">
                <label for="dept" class="col-sm-2 control-label">Department</label>
                <div class="col-sm-10">
                    <select name="dept" id="dept">
	                    
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="owner" class="col-sm-2 control-label">Is Owner</label>
                <div class="col-sm-10">
                    <input type="checkbox" id="owner" name="owner" style="margin-top:10px" />
                </div>
            </div>

            <div>
                <a href="editors.php" class="btn btn-primary">Cancel</a>
                <input type="submit" value="Save" class="btn btn-primary">
            </div>
        </form>
    </div>
<?php include 'footer.php'; ?>