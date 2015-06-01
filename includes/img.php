<?php
require_once(dirname(__FILE__) . '/../config/config.php');

$plexWatchPmsUrl = 'http://' . $plexWatch['pmsIp'] . ':' .
	$plexWatch['pmsHttpPort'];

if (!empty($plexWatch['myPlexAuthToken'])) {
	$myPlexAuthToken = '?X-Plex-Token=' . $plexWatch['myPlexAuthToken'];
} else {
	$myPlexAuthToken = '';
}

$imgReq = '';
if (isset($_GET['img'])) {
	$imgReq = $_GET['img'];
} else {
	trigger_error('No image to retrieve specified.', E_USER_ERROR);
}
$url = $plexWatchPmsUrl .
	'/photo/:/transcode' . $myPlexAuthToken .
	'&url=http://127.0.0.1:' . $plexWatch['pmsHttpPort'] . $imgReq;

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