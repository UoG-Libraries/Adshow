<?php
include 'db.php';
$objDB = new Database();

include 'header.php';
?>
    <div>
        <?php
        // handle edit playlist request
        // show playlists
        $playlists = isset($_GET["dept"]) ? $objDB->getPlaylistsByDeptID($_GET["dept"]) :$objDB->getPlaylists();
        ?>

        <h2>Playlists</h2>
        <table class="table table-striped">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Active</th>
                <th scope="col">Department</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>

            <?php foreach ($playlists as $playlist) { ?>
                <tr class="line">
                    <td><?php echo $playlist["ID"] ?></td>
                    <td><?php echo $playlist["name"] ?></td>
                    <td><?php echo $playlist["active"] ? "Yes" : "No" ?></td>
                    <td><?php echo $playlist["department"] ?></td>
                    <td>
                        <a href="editPlaylist.php?id=<?php echo $playlist["ID"] ?>">
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