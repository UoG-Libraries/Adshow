<?php
include_once 'user.php';
$location = basename($_SERVER['REQUEST_URI']);
$currentUser = User::getCurrentUser();
?>
<div class="col-md-3" id="navigation">
    <div class="nav-content col-xs-10">
        <ul class="nav nav-pills nav-stacked">
            <li <?php echo ($location == "index.php") ? "class='active'" : '' ?>>
                <a href="index.php">
                    <span class="glyphicon glyphicon-home" aria-hidden="true"></span> Home
                </a>
            </li>
            <li <?php echo ($location == "screens.php") ? "class='active'" : '' ?>>
                <a href="screens.php">
                    <span class="glyphicon glyphicon-blackboard" aria-hidden="true"></span> Screens
                </a>
            </li>
            <li <?php echo ($location == "playlists.php") ? "class='active'" : '' ?>>
                <a href="playlists.php">
                    <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Playlists
                </a>
            </li>
            <li <?php echo ($location == "editors.php") ? "class='active'" : '' ?>>
                <a href="editors.php">
                    <span class="glyphicon glyphicon-user" aria-hidden="true"></span> Editors
                </a>
            </li>
            <?php
            if ($currentUser->isSuperadmin()) {
                ?>
                <li <?php echo ($location == "departments.php") ? "class='active'" : '' ?>>
                    <a href="departments.php">
                        <span class="glyphicon glyphicon-record" aria-hidden="true"></span> Departments
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>
    <div class="nav-controller">
        <span class="glyphicon glyphicon-menu-hamburger" id="nav-controller" aria-hidden="true"></span>
    </div>
</div>