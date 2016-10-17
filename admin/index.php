<?php
include 'header.php';

$page = 'adshow/admin/index.php';


include 'navigation.php';
include 'db.php';

$objDB = new Database();
$summary = $objDB->getSummary();

echo '<div id="contentcontainer">' . "\n";
echo '  <h2>Adshow Summary</h2>' . "\n";
echo '  <p class="summary">Number of departments: ' . $summary["Department"] . '</p>' . "\n";
echo '  <p class="summary">Number of editors: ' . $summary["User"] . '</p>' . "\n";
echo '  <p class="summary">Number of screens: ' . $summary["Screen"] . '</p>' . "\n";
echo '  <p class="summary">Number of playlists: ' . $summary["Playlist"] . '</p>' . "\n";
echo '</div>' . "\n";

include 'footer.php';


