<?php $location = basename($_SERVER['REQUEST_URI']); ?>
<div class="col-md-3" id="navigation">
    <div class="nav-content col-xs-10">
        <ul class="nav nav-pills nav-stacked">
            <li><strong><span class="glyphicon glyphicon-home" aria-hidden="true"></span> Home</strong></li>
            <li <?php echo ($location == "index.php") ? "class='active'" : '' ?>><a href="index.php">Home</a></li>
            <li><strong><span class="glyphicon glyphicon-blackboard" aria-hidden="true"></span> Screens</strong></li>
            <li <?php echo ($location == "screens.php") ? "class='active'" : '' ?>><a href="screens.php">Show
                    screens</a></li>
            <li <?php echo ($location == "addScreen.php") ? "class='active'" : '' ?>><a href="addScreen.php">Add
                    screen</a></li>
            <li><strong><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Playlists</strong></li>
            <li <?php echo ($location == "playlists.php") ? "class='active'" : '' ?>><a href="playlists.php">Show
                    playlists</a></li>
            <li <?php echo ($location == "addPlaylist.php") ? "class='active'" : '' ?>><a href="addPlaylist.php">Add
                    playlist</a></li>
            <li><strong><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Editors &amp; Departments</strong></li>
            <li <?php echo ($location == "") ? "class='active'" : '' ?>><a href="#">Show editors</a></li>
            <li <?php echo ($location == "") ? "class='active'" : '' ?>><a href="#">Add editor</a></li>
            <li <?php echo ($location == "departments.php") ? "class='active'" : '' ?>><a href="departments.php">Show
                    departments</a></li>
            <li <?php echo ($location == "addDepartment.php") ? "class='active'" : '' ?>><a href="addDepartment.php">Add
                    department</a></li>
        </ul>
    </div>
    <div class="nav-controller">
        <span class="glyphicon glyphicon-menu-hamburger" id="nav-controller" aria-hidden="true"></span>
    </div>
</div>