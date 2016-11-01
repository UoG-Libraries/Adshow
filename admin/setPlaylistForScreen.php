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
$playlists = $objDB->getActivePlaylistsByDeptID($_GET["dept"]);
include 'header.php';
include_once "user.php";
?>
    <div>
        <h2>Set playlist for screen</h2>
        <?php if (count($playlists) < 1) { ?>
            <div>
                <span>No playlist available for this screen in this department. </span>
                <a href="addPlaylist.php" class="btn btn-default">Add one</a>
            </div>
        <?php } else { ?>
            <form class="form-horizontal" action="setPlaylistForScreen.php" method="post"
                  enctype="application/x-www-form-urlencoded">
                <input type="hidden" name="formSent" value="yes"/>
                <input type="hidden" name="screenID" value="<?php echo $screenID ?>"/>
                <div class="form-group">
                    <label for="playlist" class="col-sm-2 control-label">Playlist</label>
                    <div class="col-sm-10">
                        <select name="playlist" id="playlist" class="form-control">
                            <?php
                            foreach ($playlists as $playlist) {
                                if ($playlist["active"]) {
                                    ?>
                                    <option
                                        value="<?php echo $playlist["ID"] ?>" <?php echo $selectedPlaylist["ID"] == $playlist["ID"] ? "selected" : "" ?>> <?php echo $playlist["name"] ?> </option>
                                <?php }
                            } ?>
                        </select>
                    </div>
                </div>
                <div>
                    <input type="submit" value="Set" class="btn btn-primary">
                </div>
            </form>
        <?php } ?>
    </div>
<?php

include 'footer.php';
?>