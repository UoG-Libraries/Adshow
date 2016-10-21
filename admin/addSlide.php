<?php
/**
 * User: Raphael Jenni
 * Date: 20/10/2016
 */
if (isset($_POST["formSent"]) && $_POST["formSent"] == 'yes') {
    include "db.php";
    $objDB = new Database();
    $objDB->addSlide($_POST["playlistID"], $_POST["title"], $_POST["text"], $_POST["showTime"], $_POST["playlistName"]);
    header('Location: editPlaylist.php?id=' . $_POST["playlistID"]);
}

$baseDir = "../templates/";
$dh = opendir($baseDir);
$templateDirs = array();
while (false !== ($filename = readdir($dh))) {
    if ($filename != '.' && $filename != '..') {
        $templateDirs[] = $filename;
    }
}

include "header.php";
?>
<script src="scripts/slideController.js"></script>
<div class="row">
    <div class="col-md-5 preview">
        <div id="template-div"></div>
    </div>
    <div class="col-md-offset-1 col-md-6">
        <form class="template-editor" action="addSlide.php" method="post"
              enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="formSent" value="yes"/>
            <input type="hidden" name="playlistID" value="<?php echo $_GET["playlistID"]; ?>"/>
            <input type="hidden" name="templateName" id="templateName" value=""/>
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
                        oninput="vm.updatePreview()"></textarea>
                </div>
                <div class="form-group">
                    <label for="showTime">Show time</label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="showTime" id="showTime" placeholder="Amount">
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