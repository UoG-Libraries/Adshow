<?php
/**
 * User: Raphael Jenni
 * Date: 28/10/2016
 */

include 'db.php';
$objDB = new Database();

if (isset($_POST["formSent"]) && $_POST["formSent"] == 'yes') {
    $objDB->setPlaylistForScreen($_POST["screenID"], $_POST["playlist"]);
    header('Location: screens.php');
}

$screenID = $_GET["id"];
$selectedPlaylist = $objDB->getPlaylistForScreen($screenID)[0];
$playlists = $objDB->getPlaylistsByDeptID($_GET["dept"]);
include 'header.php';
include_once "user.php";
?>
    <div>
        <h2>Add screen</h2>
        <form class="form-horizontal" action="setPlaylistForScreen.php" method="post" enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="formSent" value="yes"/>
            <input type="hidden" name="screenID" value="<?php echo $screenID ?>"/>
            <div class="form-group">
                <label for="playlist" class="col-sm-2 control-label">Playlist</label>
                <div class="col-sm-10">
                    <select name="playlist" id="playlist" class="form-control">
                        <?php
                        foreach ($playlists as $playlist) { ?>
                            <option
                                value="<?php echo $playlist["ID"] ?>" <?php echo $selectedPlaylist["ID"] == $playlist["ID"] ? "selected" : "" ?>> <?php echo $playlist["name"] ?> </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div>
                <input type="submit" value="Add" class="btn btn-primary">
            </div>
        </form>
    </div>
<?php

include 'footer.php';
?>