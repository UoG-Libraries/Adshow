<?php
	include_once 'db.php';
	$db = new Database();
	
	// FORM REQUEST EVALUATION
	if (isset($_POST['id']) && isset($_POST['name'])) {
		$id = $_POST['id'];
		$newName = $_POST['name'];
		
		if (!is_numeric($id) || $id < 0) {
			displayErr(400, "Bad Request");
		}
		
		if ($db->editDepartment($id, $newName)) {
			header("Location: departments.php");
			die("Successfully edited department");
		} else {
			displayErr(500, "Internal Server Error");
		}
	} else if (isset($_POST['delete'])) {
		$id = $_POST['delete'];
		
		if (!is_numeric($id) || $id < 0) {
			displayErr(400, "Bad Request");
		}
		
		if ($db->deleteDepartment($id)) {
			header("Location: departments.php");
			die("Successfully deleted department");
		} else {
			displayErr(500, "Internal Server Error");
		}
	}
	
	// GENERAL FUNCTIONS
	function displayErr($numb, $txt) {
		header("HTTP/1.1 $numb $txt");
		include_once 'header.php';
		include_once "$numb.php";
		include_once 'footer.php';
		die();
	}
	
	// FORM PRESENTATION AND VALIDATION
	if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		displayErr(404, "Not Found");
	}
		
	$id = $_GET["id"];
	$department = $db->getDepartment($id);
	
	if (sizeof($department) == 0) {
		displayErr(500, "Internal Server Error");
	} else {
		$department = $department[0];
	}
	
	include_once 'header.php';
?>
    <div>
        <h2>Edit <?php echo $department['department']; ?></h2>
        <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="application/x-www-form-urlencoded">
	        <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo $department['department']; ?>" title="You can't edit the sNumber" />
                </div>
            </div>
            
            <br />
            <div>
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#myModal">
					Delete <?php echo $department['department']; ?>
				</button>
                <div class="spacer" style="width:50px"></div>
                <a href="departments.php" class="btn btn-primary">Cancel</a>
                <input type="submit" id="submitBtn" value="Save" class="btn btn-primary" />
            </div>
        </form>
        
        <!-- Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
		    	<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Do you want to delete the department <?php echo($department['department']); ?>?</h4>
		    		</div>
					<div class="modal-body">
						You can't undo this action!
		    		</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Abort</button>
						<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="application/x-www-form-urlencoded" style="display:inline-block">
							<input type="hidden" name="delete" value="<?php echo $department['ID']; ?>" />
							<input type="submit" class="btn btn-danger" value="Delete"></button>
						</form>
		    		</div>
		    	</div>
			</div>
		</div>
    </div>
<?php include 'footer.php'; ?>