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

$itemId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT,
	['options'=>['min_range'=>1]]);
if (!isset($itemId) || $itemId === false) {
	echo "<p>ID field is required.</p>";
	$error_msg = 'PlexWatchWeb :: POST parameter "id" not found or invalid.';
	trigger_error($error_msg, E_USER_ERROR);
}

if (isset($_POST['table']) &&
		($_POST['table'] === 'grouped' || $_POST['table'] === 'processed')) {
	$plexWatchDbTable = $_POST['table'];
} else {
	$plexWatchDbTable = dbTable();
}

$database = dbconnect();
$query = "SELECT xml FROM :table WHERE id = :id";
$results = getResults($database, $query, [
		'table'=>$plexWatchDbTable,
		'id'=>$itemId
	]);
$xml = $results->fetchColumn();
$xmlfield = simplexml_load_string($xml);
printStreamDetails($xmlfield);
?>