<?php
include 'db.php';
$objDB = new Database();
$summary = $objDB->getSummary();

include 'header.php';
?>

<div>
    <h2>Adshow Summary</h2>
    <ul class="list-group">
        <li class="list-group-item">
            <span class="badge"><?php echo $summary["Department"] ?></span>
            Number of departments:
        </li>
        <li class="list-group-item">
            <span class="badge"><?php echo $summary["User"] ?></span>
            Number of editors:
        </li>
        <li class="list-group-item">
            <span class="badge"><?php echo $summary["Screen"] ?></span>
            Number of screens:
        </li>
        <li class="list-group-item">
            <span class="badge"><?php echo $summary["Playlist"] ?></span>
            Number of playlists:
        </li>
    </ul>

    <!--    <h2>Current slide</h2>-->
</div>
<?php
include 'footer.php';
?>

