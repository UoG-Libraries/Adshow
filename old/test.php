<?php

	include $_SERVER['DOCUMENT_ROOT'] . '/classes/ldap.php';

	$myConn = new Ldap;
	$myName = $myConn->getName('s8800000');
	$first = $myName['firstname'];
	$last = $myName['lastname'];
	echo($first . ' ' . ucfirst(strtolower($last)));

?>
