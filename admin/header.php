<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
include 'loginHandler.php';

if (!isset($_SESSION["auth"])) {
    $_SESSION["auth"] = 0;
}

$_SESSION["nav"]["callingpage"] = $page;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Adshow - Admin Panel</title>
    <script type="text/javascript" src="../scripts/showdown.min.js"></script>
    <!--    <script type="text/javascript" src="scripts/jquery-1.10.2.min.js"></script>-->
    <!--    <script src="/js/jquery-1.12.4.min.js"></script>-->
    <script
        src="https://code.jquery.com/jquery-3.1.1.min.js"
        integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
        crossorigin="anonymous"></script>

    <script src="/js/bootstrap.min.js"></script>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/uog.css" rel="stylesheet">

    <!--Local-->
    <script type="text/javascript" src="scripts/default.js"></script>
    <!--    <link rel="stylesheet" type="text/css" href="scripts/default.css"/>-->
    <link rel="stylesheet" type="text/css" href="styles/style.css"/>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<?php include $_SERVER[DOCUMENT_ROOT] . '/profile.php'; ?>

<div class="uog-header-jewel"></div>
<nav class="navbar navbar-default uog-header">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"><img src="/images/header-title.png" alt="LIS"></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="/">Home</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Enquiries<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="/tally/tally.php">Digital tally</a></li>
                        <li><a href="https://helpdesk.glos.ac.uk/Sostenuto/SUsers/" target="_blank">Enquiry
                                tracker</a></li>
                        <li><a href="https://helpdeskapps.glos.ac.uk/hub/index.aspx" target="_blank">Knowledge
                                base</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Statistics<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="http://elisit.glos.ac.uk">View dashboard</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="/statistics/head-count.php">Add head count</a></li>
                        <li><a href="/statistics/gate-count.php">Add gate count</a></li>
                        <li><a href="/statistics/training.php">Add training</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false"><span class="glyphicon glyphicon-user"></span> <span
                            class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#" data-toggle="modal" data-target="#profileModal">Profile</a></li>
                        <li><a href="/logout.php">Log out</a></li>
                    </ul>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<div class="container-fluid">
    <?php include 'navigation.php'; ?>
    <div class="col-md-9">
