<?php
/**
 * User: Raphael Jenni
 * Date: 20/10/2016
 */
include "db.php";
$objDB = new Database();
if (isset($_POST["formSent"]) && $_POST["formSent"] == 'yes') {
    $objDB->editSlide($_POST["id"], $_POST["title"], $_POST["text"], $_POST["showTime"], $_POST["templateName"]);
    header('Location: editPlaylist.php?id=' . $_POST["playlistID"]);
}

$baseDir = "../templates/";
$dh = opendir($baseDir);
$templateDirs = array();
while (false !== ($filename = readdir($dh))) {
    if ($filename != '.' && $filename != '..' && strpos($filename, '-templ') !== false) {
        $templateDirs[] = $filename;
    }
}
$id = $_GET["id"];
$slide = $objDB->getSlide($id)[0];

include "header.php";
?>
<script src="scripts/slideController.js"></script>
<div class="row">
    <div class="col-md-5 preview">
        <div id="template-div"></div>
    </div>
    <div class="col-md-offset-1 col-md-6">
        <form class="template-editor" action="editSlide.php" method="post"
              enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="formSent" value="yes"/>
            <input type="hidden" name="id" value="<?php echo $id ?>"/>
            <input type="hidden" name="playlistID" value="<?php echo $slide["playlistID"] ?>"/>
            <input type="hidden" name="templateName" id="templateName" value="<?php echo $slide["templateName"] ?>"/>
            <div class="row">
                <input type="submit" class="btn btn-primary pull-right" value="Save"/>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="inputTitle">Title</label>
                    <input type="text"
                           class="form-control"
                           name="title"
                           id="inputTitle"
                           placeholder="Title"
                           value="<?php echo $slide["title"]?>"
                           oninput="vm.updatePreview()">
                </div>
                <div class="form-group">
                    <label for="inputText">Text</label>
                    <textarea
                        rows="5"
                        class="form-control"
                        name="text"
                        id="inputText"
                        placeholder="Text"
                        oninput="vm.updatePreview()"><?php echo $slide["text"]?></textarea>
                </div>
                <div class="form-group">
                    <label for="showTime">Show time</label>
                    <div class="input-group">
                        <input type="number"
                               class="form-control"
                               name="showTime"
                               id="showTime"
                               value="<?php echo $slide["playtime"]?>"
                               placeholder="Amount">
                        <div class="input-group-addon">seconds</div>
                    </div>
                </div>
                <div class="form-group">
                    <input id="uploadFile" placeholder="Choose File" disabled="disabled"/>
                    <div class="fileUpload btn btn-primary row">
                        <span>Upload image</span>
                        <input type="file" accept="image/*" id="uploadBtn" name="file" class="upload"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row template-selector horizontal-scrolling" id="horizontal-scrolling">
    <?php foreach ($templateDirs as $tempDir) { ?>
        <img src="<?php echo $baseDir . $tempDir . "/thumbnail.png" ?>"
             class="templates img-thumbnail"
             id="<?php echo $tempDir ?>"
             onclick="selectTemplate('<?php echo $baseDir ?>', '<?php echo $tempDir ?>')">
    <?php } ?>
</div>