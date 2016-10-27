<?php
/**
 * User: Raphael Jenni
 * Date: 19/10/2016
 */
include 'db.php';
$objDB = new Database();

if(isset($_GET["action"]) && $_GET["action"] == "del"){
    $objDB->deleteSlide($_GET['slideId']);
    header('Location: editPlaylist.php?id='.$_GET['id']);
}

if (isset($_POST["formSent"]) && $_POST["formSent"] == 'yes') {
    $objDB->editPlaylist($_POST["id"], $_POST["name"], $_POST["active"]);
    header('Location: playlists.php');
}


$id = $_GET["id"];
$playlist = $objDB->getPlaylist($id)[0];
$slides = $objDB->getSlidesFromPlaylist($id);

include 'header.php';

?>
    <div>
        <h2>Edit playlist</h2>
        <form class="form-horizontal" action="editPlaylist.php" method="post"
              enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="formSent" value="yes"/>
            <input type="hidden" name="id" value="<?php echo $playlist["ID"] ?>"/>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" name="name" id="name" class="form-control"
                           value="<?php echo $playlist["name"] ?>"/>
                </div>
            </div>

            <div class="form-group">
                <label for="active" class="col-sm-2 control-label">Active</label>
                <div class="col-sm-10">
                    <select name="active" id="active" class="form-control">
                        <option value="0" <?php echo $playlist["active"] == '0' ? 'selected' : '' ?>>No</option>
                        <option value="1" <?php echo $playlist["active"] == '1' ? 'selected' : '' ?>>Yes</option>
                    </select>
                </div>
            </div>

            <div>
                <a href="playlists.php" class="btn btn-primary">Cancel</a>
                <input type="submit" value="Save" class="btn btn-primary">
            </div>
            <div class="h-space"></div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Slides</h3>
                </div>
                <div class="panel-body">
                    <?php if (count($slides) < 1) { ?>
                        <div>There are no slides yet.</div>
                    <?php } else { ?>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Playtime</th>
                                <th>Template name</th>
                                <th>Active</th>
                                <th></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($slides as $slide) { ?>
                                <tr>
                                    <td><?php echo $slide["ID"] ?></td>
                                    <td><?php echo $slide["title"] ?></td>
                                    <td><span class="badge"><?php echo $slide["playtime"] ?></span> seconds</td>
                                    <td><?php echo $slide["templateName"] ?></td>
                                    <td>
                                        <?php if ($slide["active"] == '1') { ?>
                                            <span class="label label-success">YES</span>
                                        <?php } else { ?>
                                            <span class="label label-danger">NO</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <a href='editSlide.php?id=<?php echo $slide["ID"] ?>'>
                                            <span class=' glyphicon glyphicon-pencil' aria-hidden='true'
                                                  title='Edit slide details'></span>
                                        </a>
                                    </td>
                                    <td>
                                        <a href='editPlaylist.php?id=<?php echo $id?>&amp;action=del&amp;slideId=<?php echo $slide['ID'] ?>'>
                                            <span class='glyphicon glyphicon-remove' aria-hidden='true'
                                            title='Delete this screen'></span>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            } ?>
                            </tbody>
                        </table>
                        <a class="btn btn-default" href="addSlide.php?playlistID=<?php echo $id ?>">Add slide</a>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>
<?php include 'footer.php'; ?>