<?php
/**
 * User: Raphael Jenni
 * Date: 17/10/2016
 */

session_start();

if (!isset($_SESSION['auth'])) {
    $_SESSION['auth'] = 'false';
}

$pageArr = pathinfo(__FILE__);
$_SESSION['page'] = "$_SERVER[REQUEST_URI]";

if ($_SESSION['auth'] !== 'true') {
    header('Location: /login.php');
    exit;
}
