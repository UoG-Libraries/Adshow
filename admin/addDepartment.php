<?php

if (isset($_POST["formSent"]) && $_POST["formSent"] == 'yes') {

    include 'db.php';

    $objDB = new Database();
    $objDB->addDepartment($_POST["department"], $_POST["owner"]);

    header('Location: departments.php');
    exit;

}

$page = 'adshow/admin/addDepartment.php';

include 'header.php';

?>
    <div id="contentcontainer">
        <h2>Add department</h2>
        <form class="form-horizontal" action="addDepartment.php" method="post"
              enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="formSent" value="yes"/>

            <div class="form-group">
                <label for="department" class="col-sm-2 control-label">Department Name</label>
                <div class="col-sm-10">
                    <input type="text" name="department" id="department" class="form-control"/>
                </div>
            </div>

            <div class="form-group">
                <label for="owner" class="col-sm-2 control-label">Contact (sNumber)</label>
                <div class="col-sm-10">
                    <input type="text" name="owner" id="owner" class="form-control"/>
                </div>
            </div>

            <div>
                <input type="reset" value="Cancel" class="btn btn-default">
                <input type="submit" value="Add" class="btn btn-default">
            </div>
        </form>
    </div>
<?php include 'footer.php'; ?>