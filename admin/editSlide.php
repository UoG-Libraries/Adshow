<?php
/**
 * User: Raphael Jenni
 * Date: 20/10/2016
 */
include "db.php";
$objDB = new Database();
if (isset($_POST["formSent"]) && $_POST["formSent"] == 'yes') {
    $objDB->editSlide($_POST["id"], $_POST["title"], $_POST["text"], $_POST["showTime"], $_POST["imageURL"], $_POST["templateName"]);
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
    <div class="col-md-5">
        <div class="embed-responsive embed-responsive-16by9">
            <iframe class="embed-responsive-item" src="../templates/left_text-templ/template.html"
                    id="template-container" name="template-container"></iframe>
        </div>

    </div>
    <div class="col-md-offset-1 col-md-6">
        <form class="template-editor" action="editSlide.php" method="post"
              enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="formSent" value="yes"/>
            <input type="hidden" name="id" value="<?php echo $id ?>"/>
            <input type="hidden" name="playlistID" value="<?php echo $slide["playlistID"] ?>"/>
            <input type="hidden" name="templateName" id="templateName" value="<?php echo $slide["templateName"] ?>"/>
            <input type="hidden" name="imageURL" id="imageURL" value="<?php echo $slide["imageURL"] ?>"/>
            <div class="row">
                <a href="editPlaylist.php?id=<?php echo $slide["playlistID"] ?>" class="btn btn-primary pull-right">Cancel</a>
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
                           value="<?php echo $slide["title"] ?>"
                           oninput="vm.updatePreview()">
                </div>
                <div class="form-group">
                    <label for="inputText">Text</label>
                    <a target="_blank" href="https://github.com/showdownjs/showdown/wiki/Showdown's-Markdown-syntax">Markdown
                        Syntax</a>
                    <textarea
                        rows="5"
                        class="form-control"
                        name="text"
                        id="inputText"
                        placeholder="Text"
                        oninput="vm.updatePreview()"><?php echo $slide["text"] ?></textarea>
                </div>
                <div class="form-group">
                    <label for="showTime">Show time</label>
                    <div class="input-group">
                        <input type="number"
                               class="form-control"
                               name="showTime"
                               id="showTime"
                               value="<?php echo $slide["playtime"] ?>"
                               placeholder="Amount">
                        <div class="input-group-addon">seconds</div>
                    </div>
                </div>
            </div>
        </form>
        <div class="row imageUploadSection">
            <form class="uploadform " method="post" enctype="multipart/form-data"
                  action='upload.php'>
                <div class="form-group">
                    <label for="imagefile">Upload your image: </label>
                    <div class="input-group">
                        <input type="file" name="imagefile" id="imagefile"/>
                    </div>
                </div>
                <input type="submit" value="Upload" class="btn btn-default" name="submitbtn" id="submitbtn">
            </form>
            <!-- The uploaded image will display here -->
            <div id='viewimage'></div>
        </div>
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