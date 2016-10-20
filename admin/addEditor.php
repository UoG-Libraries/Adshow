<?php
	error_reporting(E_ALL);
	
	$page = 'adshow/admin/addEditor.php';
	
	include_once 'header.php';
	include_once 'user.php';
	
	$user = User::getCurrentUser();
	$errorMsg = "";
	
	$sNumb = '';
	if (isset($_POST['permission']) && isset($_POST['dept']) && isset($_POST['sNumb'])) {
		$permission	= $_POST['permission'];
		$dept		= $_POST['dept'];
		$sNumb 		= $_POST['sNumb'];
		
		// just add a string to errorMsg and it will be displayed. If it's empty, the transaction will be executed
		if (empty($sNumb)) {
			$errorMsg = "S-Number mustn't be empty";
		} else if (!preg_match('/^s[0-9]{7}$/', $sNumb)) {
			$errorMsg = 'Invalid S-Number';
		} else {
			print_r($user->db->addUser($sNumb, 0, $dept, $permission));
		}
	}
?>
    <div>
	    <script type="text/javascript">
		    <?php if ($user->hasAdminPrivileges()) { ?>
		    (function() {
			    window.addEventListener("load", function() {
				    document.querySelector("#permissionSelector").addEventListener("change", function(e) {
						if (e.target.selectedIndex == 2) {
							document.querySelector("#submitButton").className = "btn btn-danger";
						} else {
							document.querySelector("#submitButton").className = "btn btn-primary";
						}
					});
			    });
		    })();
		    <?php } ?>
		</script>
        <h2>Add editor</h2>
        <form class="form-horizontal" action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post" enctype="application/x-www-form-urlencoded">
	        <?php
	    	    if (!empty($errorMsg)) {
			        ?>
			        	<div class="form-group">
				        	<div class="col-sm-2"></div>
				        	<div class="col-sm-10">
					        	<span class="error"><?php echo $errorMsg; ?></span>
				        	</div>
			        	</div>
			        <?php
	    	    }
	    	?>
            <div class="form-group">
                <label for="sNumb" class="col-sm-2 control-label">S-Number</label>
                <div class="col-sm-10">
                    <input type="text" name="sNumb" id="sNumb" class="form-control" value="<?php echo $sNumb; ?>" />
                </div>
            </div>

            <?php
	            if ($user->hasAdminPrivileges()) {
		            ?>
		            	<div class="form-group">
            			    <label for="active" class="col-sm-2 control-label">Permission</label>
            			    <div class="col-sm-10">
            			        <select name="permission" id="permissionSelector" class="form-control">
            			            <option value="0" selected="selected">Editor</option>
            			            <option value="1">Administrator</option>
            			            
            			            <?php
	        			            	
	        			            	if ($user->isSuperadmin()) {
		    			                	echo '<option value="2">Super Administrator</option>';
	        			            	}
	        			            ?>
            			        </select>
            			    </div>
            			</div>
		            <?php
			            
			        if ($user->isSuperadmin()) {		        		
		        		?>
		        			<div class="form-group">
            				    <label for="active" class="col-sm-2 control-label">Department</label>
            				    <div class="col-sm-10">
            				        <select name="dept" class="form-control">
            				            <?php
											$depts = $user->db->getDepartments();
											print_r($depts);
	            				        	foreach ($depts as $dept) {
												echo '<option value="'.$dept['ID'].'">'.$dept['department'].'</option>';
		        							}    
	            				        ?>
            				        </select>
            				    </div>
            				</div>
		        		<?php
			        } else {
				        ?>
				        	<input type='hidden' name='dept' value="<?php echo $user->department['ID']; ?>" />
				        <?php
			        }
	            } else {
		            ?>
		            	<input type="hidden" name="permission" value="0" />
		            	<input type='hidden' name='dept' value="<?php echo $user->department['ID']; ?>" />
		            <?php
	            }
	        ?>

            <div>
                <input type="reset" value="Cancel" class="btn btn-primary">
                <input type="submit" value="Add" id="submitButton" class="btn btn-primary">
            </div>

        </form>
    </div>
<?php 
	include_once 'footer.php'; 
?>
