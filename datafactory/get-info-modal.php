<?php
require_once(dirname(__FILE__) . '/../includes/functions.php');

$itemId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT,
	array('options'=>array('min_range'=>1)));
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
$query = "SELECT xml FROM $plexWatchDbTable WHERE id = :id";
$params = array(':id'=>$itemId);
$results = getResults($database, $query, $params);
$xml = $results->fetchColumn();
$xmlfield = simplexml_load_string($xml);
printStreamDetails($xmlfield);
?>