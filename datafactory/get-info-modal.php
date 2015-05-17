<?php
date_default_timezone_set(@date_default_timezone_get());

$guisettingsFile = "../config/config.php";
if (file_exists($guisettingsFile)) {
	require_once('../config/config.php');
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
	error_log('PlexWatchWeb :: POST parameter "table" not found or incorrect.');
	echo "table field is required.";
	exit;
}

$results = $db->querySingle("SELECT xml FROM $plexWatchDbTable WHERE id = $id") or die ("Failed to access plexWatch database. Please check your settings.");
$xmlfield = simplexml_load_string($results);
$transcoded = array_key_exists('TranscodeSession', $xmlfield);
if ($transcoded) {
	$data = $xmlfield->TranscodeSession;
} else {
	$data = $xmlfield->Media;
	$data['audioDecision'] = 'Direct Play';
}
// Convert to a friendly name if needed
if ($data['audioCodec'] == 'dca') {
	$data['audioCodec'] = 'dts';
}

echo '<div class="span4">';
	echo '<h4>Stream Details</h4>';
		echo '<ul>';
			echo '<h5>Video</h5>';
			if ($transcoded) {
				echo '<li>Stream Type: <strong>'.$data['videoDecision'].'</strong></li>';
				echo '<li>Video Resolution: <strong>'.$data['height'].'p</strong></li>';
			} else {
				echo '<li>Stream Type: <strong>Direct Play</strong></li>';
				echo '<li>Video Resolution: <strong>'.$data['videoResolution'].'p</strong></li>';
			}
			echo '<li>Video Codec: <strong>'.$data['videoCodec'].'</strong></li>';
			echo '<li>Video Width: <strong>'.$data['width'].'</strong></li>';
			echo '<li>Video Height: <strong>'.$data['height'].'</strong></li>';
		echo '</ul>';
		echo '<ul>';
			echo '<h5>Audio</h5>';
			echo '<li>Stream Type: <strong>'.$data['audioDecision'].'</strong></li>';
			echo '<li>Audio Codec: <strong>'.$data['audioCodec'].'</strong></li>';
			echo '<li>Audio Channels: <strong>'.$data['audioChannels'].'</strong></li>';
		echo '</ul>';
echo '</div>';
echo '<div class="span4">';
	echo '<h4>Media Source Details</h4>';
	echo '<li>Container: <strong>'.$data['container'].'</strong></li>';
	echo '<li>Resolution: <strong>'.$data['videoResolution'].'p</strong></li>';
	echo '<li>Bitrate: <strong>'.$data['bitrate'].' kbps</strong></li>';
echo '</div>';
echo '<div class="span4">';
	echo '<h4>Video Source Details</h4>';
	echo '<ul>';
		echo '<li>Width: <strong>'.$data['width'].'</strong></li>';
		echo '<li>Height: <strong>'.$data['height'].'</strong></li>';
		echo '<li>Aspect Ratio: <strong>'.$data['aspectRatio'].'</strong></li>';
		echo '<li>Video Frame Rate: <strong>'.$data['videoFrameRate'].'</strong></li>';
		echo '<li>Video Codec: <strong>'.$data['videoCodec'].'</strong></li>';
	echo '</ul>';
	echo '<ul></ul>';
	echo '<h4>Audio Source Details</h4>';
	echo '<ul>';
		echo '<li>Audio Codec: <strong>'.$data['audioCodec'].'</strong></li>';
		echo '<li>Audio Channels: <strong>'.$data['audioChannels'].'</strong></li>';
	echo '</ul>';
echo '</div>';
?>
