<?php
/**
 * User: Raphael Jenni
 * Date: 20/10/2016
 */

include "header.php";
?>
<div class="row">
    <div class="col-md-6">
        <pre
            style="width: 100%;
            height: 350px;
            background-color: black;
            color: white;
            text-align: center">Preview</pre>
    </div>
    <div class="col-md-6">
        <form>
            <div class="form-group">
                <label for="exampleInputEmail1">Title</label>
                <input type="text" class="form-control" id="" placeholder="Title">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Text</label>
                <textarea rows="5" class="form-control" id="" placeholder="Text"></textarea>
            </div>
            <div class="form-group">
                <label for="exampleInputFile">Image</label>
                <input type="file" id="exampleInputFile">
            </div>
            <button type="submit" class="btn btn-default">Save</button>
        </form>
    </div>
</div>
<div class="row template-selector horizontal-scrolling" id="horizontal-scrolling">
    <div class="templates"></div>
    <div class="templates"></div>
    <div class="templates"></div>
    <div class="templates"></div>
    <div class="templates"></div>
    <div class="templates"></div>
    <div class="templates"></div>
    <div class="templates"></div>
    <div class="templates"></div>
    <div class="templates"></div>
</div>