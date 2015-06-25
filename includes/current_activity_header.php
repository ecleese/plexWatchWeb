<?php
require_once(dirname(__FILE__) . '/functions.php');

$fileContents = getPmsData('/status/sessions') or
	die ("<div class='alert alert-warning'>Failed to access Plex Media Server. " .
		"Please check your settings.</div>");
$statusSessions = simplexml_load_string($fileContents) or die ("Failed to parse Plex response.");

if ($statusSessions['size'] == '0') {
	echo "<h3>Activity</h3>";
} else {
	echo "<h3>Activity <strong>".$statusSessions['size']."</strong> user(s)</h3>";
}
?>