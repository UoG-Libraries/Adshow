<?php
/**
 * User: Raphael Jenni
 * Date: 17/10/2016
 */

session_start();
if (isset($_GET['login']) && $_GET['login'] == 'SalÜÜ') {
    $_SESSION['auth'] = 'true';
    if (isset($_GET['sNumber'])) {
        $_SESSION['sNumber'] = $_GET['sNumber'];
    } else {
        $_SESSION['sNumber'] = 's2101125';
    }
}

if (!isset($_SESSION['auth'])) {
    $_SESSION['auth'] = 'false';
}

$pageArr = pathinfo(__FILE__);
$_SESSION['page'] = "$_SERVER[REQUEST_URI]";

if ($_SESSION['auth'] !== 'true') {
    header('Location: /login.php');
    exit;
}
