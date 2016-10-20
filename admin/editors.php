<?php
	include_once 'db.php';
	include_once 'user.php';
	include_once 'header.php';
	
	$db = new Database();
	$user = User::getCurrentUser();
	
	$users = $db->getUsers();
?>
    <div>
        <h2>Editors</h2>
        <table class="table table-striped">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">S-Number</th>
                <th scope="col">Is owner</th>
                <th scope="col">Department</th>
                <th scope="col">Permission</th>
            </tr>

            <?php 
	            foreach ($users as $user) { 
		           $department = $db->getDepartment($user['departmentIDfk'])[0];
	        ?>
                <tr class="line">
                    <td><?php echo $user["ID"]; ?></td>
                    <td><?php echo $user["sNumber"]; ?></td>
                    <td><?php echo $user["owner"] ? "Yes" : "No"; ?></td>
                    <td><?php echo $department['department']; ?></td>
                    <td><?php echo $user['permission']; ?> </td>
                </tr>
            <?php } ?>
        </table>
    </div>
<?php
include 'footer.php';
?>