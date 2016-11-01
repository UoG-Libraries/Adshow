<?php
include 'db.php';
include 'user.php';
$objDB = new Database();

if (isset($_GET['action']) && $_GET['action'] == 'del') {
    $objDB->deletePlaylist($_GET['id']);
    header('Location: playlists.php');
}

include 'header.php';
?>
    <div>
        <?php
        // handle edit playlist request
        // show playlists
        $playlists = isset($_GET["dept"]) ? $objDB->getPlaylistsByDeptID($_GET["dept"]) : $objDB->getPlaylists();
        ?>

        <div class="head">
            <h2 class="col-md-11">Playlists</h2>
            <a href="addPlaylist.php" class="btn btn-default pull-right">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add
            </a>
        </div>
        <table class="table table-striped">
            <tr>
                <th scope="col"></th>
                <th scope="col">Name</th>
                <th scope="col">Active</th>
                <th scope="col">Department</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>

            <?php foreach ($playlists as $playlist) { ?>
                <tr class="line">
                    <td style="text-align: right; width: 1%">
                        <?php
                        if ($playlist['global'] && User::getCurrentUser()->isGlobal()) { ?>
                            <span class="glyphicon glyphicon-globe" aria-hidden="true" title="Global"></span>
                        <?php } ?>
                    </td>
                    <td><?php echo $playlist['name'] ?></td>
                    <td>
                        <?php if ($playlist["active"] == '1') { ?>
                            <span class="label label-success">YES</span>
                        <?php } else { ?>
                            <span class="label label-danger">NO</span>
                        <?php } ?>
                    </td>
                    <td><?php echo $playlist['department'] ?></td>
                    <td>
                        <a href="editPlaylist.php?id=<?php echo $playlist['ID'] ?>">
                            <span class="glyphicon glyphicon-pencil" aria-hidden="true" title="Edit playlist"></span>
                        </a>
                    </td>
                    <td>
                        <a href="playlists.php?action=del&amp;id=<?php echo $playlist["ID"] ?>">
                            <span class="glyphicon glyphicon-remove" aria-hidden="true"
                                  title="Delete this playlist"></span>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
<?php
include 'footer.php';
?>