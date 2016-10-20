<?php

error_reporting(E_ALL);

$page = 'adshow/admin/addEditor.php';

include_once 'header.php';
include_once 'user.php';

var_dump($_SESSION);
print_r(User::getCurrentUser()->permission);
?>
    <div>
	    <script type="text/javascript">
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
		</script>
        <h2>Add editor</h2>
        <form class="form-horizontal" action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post" enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="createdBy" value="<?php echo $_SESSION["details"]["id"] ?>"/>

            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" name="name" id="name" class="form-control"/>
                </div>
            </div>

            <div class="form-group">
                <label for="active" class="col-sm-2 control-label">Permission</label>
                <div class="col-sm-10">
                    <select name="permission" id="permissionSelector" class="form-control">
                        <option value="0" selected="selected">Editor</option>
                        <option value="1">Administrator</option>
                        <option value="2">Super Administrator</option>
                    </select>
                </div>
            </div>

            <div>
                <input type="reset" value="Cancel" class="btn btn-primary">
                <input type="submit" value="Add" id="submitButton" class="btn btn-primary">
            </div>

        </form>
    </div>
<?php 
	include_once 'footer.php'; 
?>