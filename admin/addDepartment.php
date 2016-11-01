<?php
include_once 'db.php';
include_once 'user.php';

$errorMsg = '';
$successMsg = '';
$name = '';
$admin = '';

if (isset($_POST["department"]) && isset($_POST["admin"])) {
	$name = $_POST['department'];
	$admin = $_POST['admin'];
    $db = new Database();
    
    if (empty($name)) {
    	$errorMsg = 'Name mustn\'t be empty';
    } elseif (empty($admin)) {
	    $errorMsg = 'Admin S-Number mustn\'t be empty';
    } elseif (!preg_match('/^s[0-9]{7}$/', $admin)) {
	    $errorMsg = 'S-Number is invalid';
    }
    
    if (empty($errorMsg)) {
	    $deptID = $db->addDepartment($name);
    
    	if ($deptID == -1) {
		    $errorMsg = "Couldn't add department";
    	} else if (User::userExists($admin)) {
		    $user = User::getUserWithSNumber($admin);
		    if (!$user->isSuperadmin()) {
		    	$user->updatePermission(Permission::Admin);
		    }
		    
		    $user->updateDepartment($deptID);
		    if ($user->commitChanges()) {
			    $successMsg = "Successfully added department and changed ".$user->sNumber." (".$user->fullName.") to the department editor";
		    } else {
			    $errorMsg = "Couldn't change user to department admin";
		    }
    	} else {
		    $name = User::getNameOfUserWithSNumber($admin);
		    
		    if ($db->addUser($admin, 0, $deptID, Permission::Admin, $name['firstname'], $name['lastname'])) {
			    $successMsg = 'Successfully created department and added new department admin';
		    } else {
			    $errorMsg = "Couldn't add new user as department admin";
		    }
    	}
    }
}


include 'header.php';

?>
    <div>
        <h2>Add department</h2>
        <form class="form-horizontal" action="addDepartment.php" method="post" enctype="application/x-www-form-urlencoded">
	        <?php
	    	    if (!empty($errorMsg) || !empty($successMsg)) {
			        ?>
			        	<div class="form-group">
				        	<div class="col-sm-2"></div>
				        	<div class="col-sm-10">
					        	<span class="error"><?php echo $errorMsg; ?></span>
					        	<span class="success"><?php echo $successMsg; ?></span>
				        	</div>
			        	</div>
			        <?php
	    	    }
	    	?>
            <div class="form-group">
                <label for="department" class="col-sm-2 control-label">Department Name</label>
                <div class="col-sm-10">
                    <input type="text" name="department" id="department" class="form-control" value="<?php echo $name; ?>" />
                </div>
            </div>

            <div class="form-group">
                <label for="admin" class="col-sm-2 control-label">Department Administrator</label>
                <div class="col-sm-10">
                    <input type="text" name="admin" id="admin" class="form-control" placeholder="S-Number" value="<?php echo($admin); ?>" />
                </div>
            </div>

            <div>
                <a href="departments.php" class="btn btn-primary">Cancel</a>
                <input type="submit" value="Add" class="btn btn-primary">
            </div>
        </form>
    </div>
<?php include 'footer.php'; ?>