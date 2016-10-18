<?php $location = basename($_SERVER['REQUEST_URI']);?>

<div class="col-md-3">
    <ul class="nav nav-pills nav-stacked">
        <li><strong>Home</strong></li>
        <li <?php echo ($location == "index.php") ? "class='active'" : '' ?>><a href="index.php">Home</a></li>
        <li><strong>Screens</strong></li>
        <li <?php echo ($location == "screens.php") ? "class='active'" : '' ?>><a href="screens.php">Show screens</a></li>
        <li <?php echo ($location == "addScreen.php") ? "class='active'" : '' ?>><a href="addScreen.php">Add screen</a></li>
        <li><strong>Playlists</strong></li>
        <li <?php echo ($location == "playlists.php") ? "class='active'" : '' ?>><a href="playlists.php">Show playlists</a></li>
        <li <?php echo ($location == "addPlaylist.php") ? "class='active'" : '' ?>><a href="addPlaylist.php">Add playlist</a></li>
        <li><strong>Editors &amp; Departments</strong></li>
        <li <?php echo ($location == "") ? "class='active'" : '' ?>><a href="#">Show editors</a></li>
        <li <?php echo ($location == "") ? "class='active'" : '' ?>><a href="#">Add editor</a></li>
        <li <?php echo ($location == "departments.php") ? "class='active'" : '' ?>><a href="departments.php">Show departments</a></li>
        <li <?php echo ($location == "addDepartment.php") ? "class='active'" : '' ?>><a href="addDepartment.php">Add department</a></li>
    </ul>
</div>