<?php
include 'db.php';
$objDB = new Database();

// handle delete screen request
if (isset($_GET["action"]) && $_GET["action"] == 'del') {
    $objDB->deleteScreen($_GET["id"]);
    header("Location: screens.php");
    exit;
}

$screensList = $objDB->getScreensList();

include 'header.php';
?>
<h2>Screens</h2>
<table class="table table-striped">
    <tr>
        <th scope="col">Screen ID</th>
        <th scope="col">Department</th>
        <th scope="col">Location</th>
        <th scope="col"></th>
        <th scope="col" class="hidden-xs"></th>
        <th scope="col" class="hidden-xs"></th>
    </tr>
    <?php foreach ($screensList as $screen) { ?>
        <tr>
            <td><?php echo $screen["ID"] ?></td>
            <td><?php echo $screen["department"] ?></td>
            <td><?php echo $screen["location"] ?></td>
            <td class="col-md-1 hidden-xs">
                <a href="editScreen.php?id=<?php echo $screen["ID"] ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"
                                      title="Edit screen details"></span>
                </a>
            </td>
            <td class="col-md-1 hidden-xs">
                <a href="#">
                                <span class="glyphicon glyphicon-th-list" aria-hidden="true"
                                      title="View playlists for this screen"></span>
                </a>
            </td>
            <td class="col-md-1 hidden-xs">
                <a href="screens.php?action=del&amp;id=<?php echo $screen["ID"] ?>">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"
                                      title="Delete this screen"></span>
                </a>
            </td>
            <td class="col-md-1 hidden-sm hidden-md hidden-lg">
                <a tabindex="0"
                   data-container="body"
                   data-toggle="popover"
                   data-placement="left"
                   data-trigger="focus"
                   data-html="true"
                   data-content="
                   <ul class='list-group'>
                   <li class='list-group-item'>
                   <a href=' editScreen.php?id=<?php echo $screen["ID"] ?>'>
                       <span class=' glyphicon glyphicon-pencil' aria-hidden='true'
                        title='Edit screen details'></span>
                   </a>
                   </li>
                   <li class='list-group-item'>
                       <a href='#'>
                                   <span class='glyphicon glyphicon-th-list' aria-hidden='true'
                                         title='View playlists for this screen'></span>
                       </a>
                   </li>
                   <li class='list-group-item'>
                       <a href='screens.php?action=del&amp;id=<?php echo $screen['ID'] ?>'>
                               <span class='glyphicon glyphicon-remove' aria-hidden='true'
                                     title='Delete this screen'></span>
                       </a>
                   </li>
                </ul>
                ">
                <span class="glyphicon glyphicon-option-vertical" aria-hidden="true"
                      title="Options"></span>
                </a>
            </td>
        </tr>
    <?php } ?>
</table>
<?php
include 'footer.php';

?>

