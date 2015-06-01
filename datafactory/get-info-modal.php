<?php
date_default_timezone_set(@date_default_timezone_get());

$guisettingsFile = dirname(__FILE__) . '/../config/config.php';
if (file_exists($guisettingsFile)) {
	require_once($guisettingsFile);
} else {
	error_log('PlexWatchWeb :: Config file not found.');
	echo "Config file not found";
	exit;
}

$db = dbconnect();

if (isset($_POST['id'])) {
	$id = $db->escapeString($_POST['id']);
} else {
	error_log('PlexWatchWeb :: POST parameter "id" not found.');
	echo "id field is required.";
	exit;
}

if (isset($_POST['table']) &&
		($_POST['table'] === 'grouped' || $_POST['table'] === 'processed')) {
	$plexWatchDbTable = $_POST['table'];
} else {
	$plexWatchDbTable = dbTable();
}

$results = $db->querySingle("SELECT xml FROM $plexWatchDbTable WHERE id = $id") or die ("Failed to access plexWatch database. Please check your settings.");
$xmlfield = simplexml_load_string($results);
printStreamDetails($xmlfield);
?>