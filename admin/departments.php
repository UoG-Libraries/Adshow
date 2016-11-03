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
        <div class="head">
            <h2>Departments</h2>
            <a href="addDepartment.php" class="btn btn-default">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add
            </a>
        </div>
        <?php
        $departmentsList = $objDB->getDepartments();

        if (sizeof($departmentsList) > 0) {
            ?>
            <table class="table table-striped">
                <tr>
                    <th scope="col">Department</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
                <?php
                foreach ($departmentsList as $department) {
                    ?>
                    <tr class="line">
                        <td> <?php echo $department["department"] ?></td>
                        <td>
                            <a href="editDepartment.php?id=<?php echo $department['ID'] ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"
                                      title="Edit department details"></span>
                            </a>
                        </td>
                        <td>
                            <a href="playlists.php?dept=<?php echo $department['ID']; ?>">
                                <span class="glyphicon glyphicon-th-list" aria-hidden="true"
                                      title="View playlists for this department"></span>
                            </a>
                        </td>
                        <td>
                            <a href="departments.php?action=del&amp;id=<?php echo $department["ID"] ?>">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"
                                      title="Delete this department"></span>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <?php
        } else {
            ?>
            <span class="bigInfo">There are no departments</span>
            <br/>
            <br/>
            <a href="addDepartment.php" style="font-weight:bold">Add one</a>
            <?php
        }
        ?>
    </div>
<?php include 'footer.php'; ?>