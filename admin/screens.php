<?php

// GET variables
// action: delete, edit, update
// id: screen id

include 'db.php';
$objDB = new Database();

// handle delete screen request
if (isset($_GET["action"]) && $_GET["action"] == 'del') {
    $objDB->deleteScreen($_GET["id"]);
    header("Location: screens.php");
    exit;
}

$page = 'adshow/admin/screens.php';

include 'header.php';

?>

<?php

$screensList = $objDB->getScreensList();
?>
    <h2>Screens</h2>
    <table class="table table-striped">
        <tr>
            <th scope="col">Screen ID</th>
            <th scope="col">Department</th>
            <th scope="col">Location</th>
            <th scope="col">Edit</th>
            <th scope="col">Playlists</th>
            <th scope="col">Delete</th>
        </tr>
        <?php foreach ($screensList as $screen) { ?>
            <tr>
                <td><?php echo $screen["ID"] ?></td>
                <td><?php echo $screen["department"] ?></td>
                <td><?php echo $screen["location"] ?></td>
                <td class="col-md-1">
                    <a href="editScreen.php?id=<?php echo $screen["ID"] ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"
                                      title="Edit screen details"></span>
                    </a>
                </td>
                <td class="col-md-1">
                    <a href="#">
                                <span class="glyphicon glyphicon-th-list" aria-hidden="true"
                                      title="View playlists for this screen"></span>
                    </a>
                </td>
                <td class="col-md-1">
                    <a href="screens.php?action=del&amp;id=<?php echo $screen["ID"] ?>">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"
                                      title="Delete this screen"></span>
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php
include 'footer.php';

?>