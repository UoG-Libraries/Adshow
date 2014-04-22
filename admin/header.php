<?php

  session_start();

  if (!isset($_SESSION["auth"])) {
    $_SESSION["auth"] = 0;
  }

  $_SESSION["nav"]["callingpage"] = $page;

  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
  echo '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n\n";

  echo '<head>' . "\n";
  echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />' . "\n";
  echo '<script type="text/javascript" src="scripts/jquery-1.10.2.min.js"></script>' . "\n";
  echo '<script type="text/javascript" src="scripts/default.js"></script>' . "\n";
  echo '<link rel="stylesheet" type="text/css" href="scripts/default.css" />' . "\n";
  echo '<title>Adshow - Admin Panel</title>' . "\n";
  echo '</head>' . "\n\n";

  echo '<body>' . "\n\n";

  if ($_SESSION["auth"] == 1) {
    echo '<p id="topbar">Hello ' . $_SESSION["details"]["firstname"] . ' ' . $_SESSION["details"]["lastname"] . ' | <a href="/scripts/logout.php">logout</a></p>' . "\n\n";
  }

  echo '<div id="headercontainer">' . "\n";
  echo '     <h1>Adshow</h1>' . "\n";
  echo '     <p>Admin Panel</p>' . "\n";
  echo '</div>' . "\n\n";

?>