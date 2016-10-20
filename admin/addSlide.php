<?php
/**
 * User: Raphael Jenni
 * Date: 20/10/2016
 */

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
<script>addCss('<?php echo $baseDir . "2.templ/style.css"?>')</script>
<div class="row">
    <div class="col-md-5 preview">
        <div>
            <?php include $baseDir . "2.templ/template.html" ?>
        </div>
    </div>
    <div class="col-md-offset-1 col-md-6">
        <form>
            <div class="row">
                <button type="submit" class="btn btn-primary pull-right">Save</button>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" name="title" id="title" placeholder="Title">
                </div>
                <div class="form-group">
                    <label for="text">Text</label>
                    <textarea rows="5" class="form-control" name="text" id="text" placeholder="Text"></textarea>
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
    <?php foreach ($templateDirs as $tempDir) {
        $thumbnail = $baseDir . $tempDir . "/thumbnail.png" ?>
        <img src="<?php echo $thumbnail ?>" class="templates img-thumbnail">
    <?php } ?>
</div>