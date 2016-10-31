<?php
	include_once 'db.php';
	include_once 'user.php';
	include_once 'header.php';
	
	$user = User::getCurrentUser();
	
	$users;
	$summary = '';
	
	if ($user->isSuperadmin()) {
		$users = $user->db->getUsersWithDeptName();
		$summary = $user->db->getSummary()['User'];
	} else {
		$users = $user->db->getUsersWithDeptName($user->department['ID']);
		$summary = $user->department['department'];
	}
?>
    <div>
        <h2>Editors (<?php if (!empty($users)) { echo $summary; } else { echo 'No users yet'; } ?>)</h2>
        <?php
	        if (!empty($users)) {
	    ?>
        	<table class="table table-striped">
        	    <tr>
        	        <th scope="col">ID</th>
        	        <th scope="col">S-Number</th>
        	        <th scope="col">Name</th>
        	        <th scope="col">Department</th>
        	        <th scope="col">Permission</th>
        	        <th scope="col"></th>
        	    </tr>
			
        	    <?php 
	    	        foreach ($users as $usr) { 
	    	    ?>
        	        <tr class="line">
        	            <td><?php echo $usr["ID"]; ?></td>
        	            <td><?php echo $usr["sNumber"]; ?></td>
        	            <td><?php echo($usr['firstname'] . ' ' . ucfirst(strtolower($usr['lastname']))); ?></td>
        	            <td><?php echo $usr['department']; ?></td>
        	            <td><?php echo Permission::getStr($usr['permission']); ?> </td>
        	            <td>
	    	                <?php
			                	if ($user->isAdmin() && $usr['permission'] != Permission::Superadmin || $user->isSuperadmin()) {
				                	?>
				                		<a href="editEditors.php?sNumb=<?php echo $usr['sNumber']; ?>" title="Edit <?php echo $usr['sNumber']; ?>">
											<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
	    	                			</a>
				                	<?php
			                	}    
			                ?>
        	            </td>
        	        </tr>
        	    <?php 
	    	        } 
	    	    ?>
        	</table>
        <?php
	        } else {
		        ?>
		        	<span class="bigInfo">There are no editors</span>
		        	<br />
		        	<br />
		        	<a href="addEditor.php" style="font-weight:bold">Add one</a>
		        <?php
	        }
	    ?>
    </div>
<?php
include 'footer.php';
?>