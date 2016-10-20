<?php
include 'db.php';
$objDB = new Database();

// handle delete department request
if (isset($_GET["action"]) && $_GET["action"] == 'del') {
    $objDB->deleteDepartment($_GET["id"]);
    header("Location: departments.php");
}

include 'header.php';
?>
    <div>
        <?php
        // handle edit department request
        if (isset($_GET["action"]) && $_GET["action"] == 'edit') {
            $departmentDetails = $objDB->getDepartment($_GET["id"]);
        } else {
            // show departments list
            $departmentsList = $objDB->getDepartmentsAndOwner();
            ?>
            <h2>Departments</h2>
            <table class="table table-striped">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Department</th>
                    <th scope="col">Contact</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
                <?php foreach ($departmentsList as $department) { ?>
                    <tr class="line">
                        <td> <?php echo $department["ID"] ?></td>
                        <td> <?php echo $department["department"] ?></td>
                        <td> <?php echo $department["sNumber"] ?></td>
                        <td>
                            <a href="departments.php?action=edit&amp;id='<?php echo $department[" ID"] ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"
                                      title="Edit department details"></span>
                            </a>
                        </td>
                        <td>
                            <a href="#">
                                <span class="glyphicon glyphicon-th-list" aria-hidden="true"
                                      title="View playlists for this department"></span>
                            </a>
                        </td>
                        <td>
                            <a href="departments.php?action=del&amp;id='<?php echo $department[" ID"] ?>">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"
                                      title="Delete this department"></span>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>
<?php include 'footer.php'; ?>