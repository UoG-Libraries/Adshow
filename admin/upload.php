<?php
/**
 * User: Raphael Jenni
 * Date: 26/10/2016
 */

ini_set('display_errors',1);
error_reporting(E_ALL);

$file_formats = array("jpg", "png", "gif", "bmp"); // Set File format
$filepath = "../upload_files/";
if (isset($_POST['submitbtn']) && $_POST['submitbtn'] == "Upload") {
    $name = $_FILES['imagefile']['name'];
    $size = $_FILES['imagefile']['size'];

    if (strlen($name)) {
        $extension = substr($name, strrpos($name, '.') + 1);
        if (in_array($extension, $file_formats)) {
            if ($size < (2048 * 1024)) {
                $imagename = md5(uniqid() . time()) . "." . $extension;
                $tmp = $_FILES['imagefile']['tmp_name'];
                if (move_uploaded_file($tmp, $filepath . $imagename)) {
                    echo '<input type="hidden" id="uploaded_image_name" value="'.$imagename . '" />';
                    echo '<script>updateImage();</script>';
                } else {
                    echo "Could not move the file.";
                }
            } else {
                echo "Your image size is bigger than 2MB.";
            }
        } else {
            echo "Invalid file format.";
        }
    } else {
        echo "Please select image..!";
    }
    exit();
}

?>