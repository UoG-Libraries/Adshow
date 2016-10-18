<?php

// GET variables
// action: delete, edit, update
// id: screen id

$page = 'adshow/admin/playlists.php';

include 'header.php';
include 'db.php';

$objDB = new Database();

// handle delete playlist request
/*
	if ($_GET["action"] == 'del') {
		$objDB->deleteScreen($_GET["id"]);
		header("Location: screens.php");
		exit;
	}
*/
?>
    <div id="contentcontainer">
<?php
// handle edit playlist request
if ($_GET["action"] == 'edit') {
    // TODO
} else {
    // show playlists
    $playlists = $objDB->getPlaylists();
    ?>

    <h2>Playlists</h2>
    <table class="table table-striped">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Name</th>
            <th scope="col">Active</th>
            <th scope="col">Created by</th>
            <th scope="col">Edit</th>
            <th scope="col">Delete</th>
        </tr>

        <?php foreach ($playlists as $playlist) { ?>
            <tr class="line">
                <td><?php echo $playlist["ID"] ?></td>
                <td><?php echo $playlist["name"] ?></td>
                <td><?php echo $playlist["active"] ? "Yes" : "No" ?></td>
                <td><?php echo $playlist["sNumber"] ?></td>
                <td>
                    <a href="playlists.php?action=edit&amp;id='<?php $playlist["ID"] ?>'">
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true" title="Edit playlist"></span>
                    </a>
                </td>
                <td>
                    <a href="playlists.php?action=del&amp;id='<?php $playlist["ID"] ?>'">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true" title="Delete this playlist"></span>
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>
    </div>
<?php }
include 'footer.php';
?>