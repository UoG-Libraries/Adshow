<?php
	if (!isset($_GET['id'])) {
		header('HTTP/1.1 404 Not Found');
	}
	
	include_once 'user.php';
		
	// NORMAL EDIT FORM
	$requiredValues = array('sNumb', 'dept', 'permission');
	$complete = TRUE;
	foreach ($requiredValues as $value) {
		if (!isset($_POST[$value])) {
			$complete = FALSE;
		}
	}
	
	if ($complete) {
		$sNumb = $_POST['sNumb'];
		$dept = $_POST['dept'];
		$permission = $_POST['permission'];
		
		$user = User::getUserWithSNumber($sNumb);
		$user->updateDepartment($dept);
		$user->updatePermission($permission);
		
		if ($user->commitChanges()) {
			header('Location: editors.php');
			die('Successfully updated user');
		} else {
			include_once 'header.php';
			?>
				<div>
					<h2>An error occurred</h2>
					<p>We're sorry, but your request couldn't be performed</p>
					<p>Please try again later and if the error still occurs, feel free to inform IT about it.</p>
				</div>
			<?php
			include_once 'footer.php';
		}
	}
		
	// DELETE FORM
	if (isset($_POST['delete'])) {
		$sNumb = $_POST['delete'];
		$user = User::getUserWithSNumber($sNumb);
		if ($user->deleteUser()) {
			header('Location: editors.php');
			die('Successfully deleted user');
		} else {
			include_once 'header.php';
			?>
				<div>
					<h2>An error occurred</h2>
					<p>We're sorry, but your request couldn't be performed</p>
					<p>Please try again later and if the error still occurs, feel free to inform IT about it.</p>
				</div>
			<?php
			include_once 'footer.php';
		}
	}
	
	// ERROR CHECK
	if (!isset($_GET['sNumb'])) {
		include '404.php';
		die();
	}
		
	// PRESENT EDIT MASK
	include_once 'header.php';
	
	$userSNumb = $_GET['sNumb'];
	$user = User::getUserWithSNumber($userSNumb);
	$currentUser = User::getCurrentUser();
	$depts = $user->db->getDepartments();
?>
	<script type="text/javascript">
		(function() {
			"use strict";
			
			window.addEventListener("load", function() {
				var ids = ["dept", "permission"];
				var changed = false;
				
				ids.forEach(function(itm) {
					document.getElementById(itm).addEventListener("change", function() {
						if (!changed) {
							changed = true;
							$("#submitBtn").removeAttr("disabled");
						}
					});
				});
				
				document.getElementById('permission').addEventListener("change", function(e) {
					if (e.target.selectedIndex == 2) {
						document.getElementById("submitBtn").className = "btn btn-warning";
					} else {
						document.getElementById("submitBtn").className = "btn btn-primary";
					}
				})
			});
		})();
	</script>
    <div>
        <h2>Edit <?php echo $user->fullName; ?></h2>
        <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="application/x-www-form-urlencoded">
	        <input type="hidden" name="sNumb" value="<?php echo $userSNumb; ?>" />
            <div class="form-group">
                <label for="sNumb" class="col-sm-2 control-label">S-Number</label>
                <div class="col-sm-10">
                    <input type="text" id="sNumb" class="form-control" value="<?php echo $user->sNumber; ?>" title="You can't edit the sNumber" disabled />
                </div>
            </div>

			<div class="form-group">
                <label for="dept" class="col-sm-2 control-label">Department</label>
                <div class="col-sm-10">
                    <select name="dept" id="dept">
	                    <?php
		                    foreach ($depts as $dept) {
			                    $isUserDept = $dept['ID'] == $user->department['ID'];
			                    echo '<option value="'.$dept['ID'].'"'.($isUserDept ? " selected" : "").'>'.$dept['department'].'</option>';
		                    }
		                ?>
                    </select>
                </div>
            </div>

			<div class="form-group">
				<label for="permission" class="col-sm-2 control-label">Permission</label>
				<div class="col-sm-10">
					<select name="permission" id="permission">
						<?php 
							$reflection = new ReflectionClass('Permission');
							$availablePermissions = $reflection->getConstants();
							
							foreach ($availablePermissions as $desc => $val) {
								if ($currentUser->permission != Permission::Superadmin && $val == Permission::Superadmin) {
									continue;
								}
								
								$selectedAddition = $user->permission == $val ? ' selected' : '';
								echo "<option value=\"$val\"$selectedAddition>$desc</option>";
							}
						?>
					</select>
				</div>
			</div>

			<br />
            <div>
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#myModal">
					Delete <?php echo $user->name; ?>
				</button>
                <div class="spacer" style="width:50px"></div>
                <a href="editors.php" class="btn btn-primary">Cancel</a>
                <input type="submit" id="submitBtn" value="Save" class="btn btn-primary" disabled />
            </div>
        </form>
        
        <!-- Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
		    	<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Do you want do delete <?php echo($user->name); ?>?</h4>
		    		</div>
					<div class="modal-body">
						You can't undo this action!
		    		</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Abort</button>
						<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="application/x-www-form-urlencoded" style="display:inline-block">
							<input type="hidden" name="delete" value="<?php echo $userSNumb; ?>" />
							<input type="submit" class="btn btn-danger" value="Delete permanently"></button>
						</form>
		    		</div>
		    	</div>
			</div>
		</div>
    </div>
<?php include 'footer.php'; ?>
