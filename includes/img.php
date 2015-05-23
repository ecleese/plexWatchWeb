<?php
require_once(dirname(__FILE__) . '/../config/config.php');

$imgReq = '';
if (isset($_GET['img']) && (substr($_GET['img'], 0, 4) == 'http')) {
	$imgReq = $_GET['img'];
}
$url = $imgReq;
if (!empty($plexWatch['myPlexAuthToken'])) {
	$url = $imgReq . "&X-Plex-Token=" . $plexWatch['myPlexAuthToken'];
}

$img = file_get_contents($url);
if ($img === false) {
	trigger_error("Failed to retrieve \"$url\"", E_USER_ERROR);
}
foreach ($http_response_header as $value) {
	if (preg_match('/^Content-Type:/i', $value)) {
		header($value, false);
	}
}
echo $img;
?>