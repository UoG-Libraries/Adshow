<?php
	include_once 'db.php';
	include_once 'user.php';
	include_once 'header.php';
	
	$db = new Database();
	$user = User::getCurrentUser();
	
	$users = $db->getUsersWithDeptName();
?>
    <div>
        <h2>Editors</h2>
        <table class="table table-striped">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">S-Number</th>
                <th scope="col">Name</th>
                <th scope="col">Is owner</th>
                <th scope="col">Department</th>
                <th scope="col">Permission</th>
                <th scope="col"></th>
            </tr>

            <?php 
	            foreach ($users as $user) { 
	        ?>
                <tr class="line">
                    <td><?php echo $user["ID"]; ?></td>
                    <td><?php echo $user["sNumber"]; ?></td>
                    <td><?php echo($user['firstname'] . ' ' . ucfirst(strtolower($user['lastname']))); ?></td>
                    <td>
	                    <?php if ($user['owner']) { ?>
	                    <img src="images/check.svg" />
	                    <?php } ?>
	                </td>
                    <td><?php echo $user['department']; ?></td>
                    <td><?php echo Permission::getStr($user['permission']); ?> </td>
                    <td>
	                    <a href="editEditors.php?sNumb=<?php echo $user['sNumber']; ?>" title="Edit <?php echo $user['sNumber']; ?>">
		                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
	                    </a>
                    </td>
                </tr>
            <?php 
	            } 
	        ?>
        </table>
    </div>
<?php
include 'footer.php';
?>