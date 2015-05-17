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

$plexWatchDbTable = dbTable('user'); // Grabs user setting
$results = $db->querySingle("SELECT xml FROM $plexWatchDbTable WHERE id = $id") or die ("Failed to access plexWatch database. Please check your settings.");
$xmlfield = simplexml_load_string($results);

$transcoded = array_key_exists('TranscodeSession', $xmlfield);

echo '<div class="span4">';
	echo '<h4>Stream Details</h4>';
		echo '<ul>';
			echo '<h5>Video</h5>';
			if ($transcoded) {
				echo '<li>Stream Type: <strong>'.$xmlfield->TranscodeSession['videoDecision'].'</strong></li>';
				echo '<li>Video Resolution: <strong>'.$xmlfield->TranscodeSession['height'].'p</strong></li>';
				echo '<li>Video Codec: <strong>'.$xmlfield->TranscodeSession['videoCodec'].'</strong></li>';
				echo '<li>Video Width: <strong>'.$xmlfield->TranscodeSession['width'].'</strong></li>';
				echo '<li>Video Height: <strong>'.$xmlfield->TranscodeSession['height'].'</strong></li>';
			} else {
				echo '<li>Stream Type: <strong>Direct Play</strong></li>';
				echo '<li>Video Resolution: <strong>'.$xmlfield->Media['videoResolution'].'p</strong></li>';
				echo '<li>Video Codec: <strong>'.$xmlfield->Media['videoCodec'].'</strong></li>';
				echo '<li>Video Width: <strong>'.$xmlfield->Media['width'].'</strong></li>';
				echo '<li>Video Height: <strong>'.$xmlfield->Media['height'].'</strong></li>';
			}
		echo '</ul>';
		echo '<ul>';
			echo '<h5>Audio</h5>';
			if ($transcoded) {
				echo '<li>Stream Type: <strong>'.$xmlfield->TranscodeSession['audioDecision'].'</strong></li>';
				if ($xmlfield->TranscodeSession['audioCodec'] == "dca") {
					echo '<li>Audio Codec: <strong>dts</strong></li>';
				} else {
					echo '<li>Audio Codec: <strong>'.$xmlfield->TranscodeSession['audioCodec'].'</strong></li>';
				}
				echo '<li>Audio Channels: <strong>'.$xmlfield->TranscodeSession['audioChannels'].'</strong></li>';
			} else {
				echo '<li>Stream Type: <strong>Direct Play</strong></li>';
				if ($xmlfield->Media['audioCodec'] == "dca") {
					echo '<li>Audio Codec: <strong>dts</strong></li>';
				} else {
					echo '<li>Audio Codec: <strong>'.$xmlfield->Media['audioCodec'].'</strong></li>';
				}
				echo '<li>Audio Channels: <strong>'.$xmlfield->Media['audioChannels'].'</strong></li>';
			}
		echo '</ul>';
echo '</div>';
echo '<div class="span4">';
	echo '<h4>Media Source Details</h4>';
	echo '<li>Container: <strong>'.$xmlfield->Media['container'].'</strong></li>';
	echo '<li>Resolution: <strong>'.$xmlfield->Media['videoResolution'].'p</strong></li>';
	echo '<li>Bitrate: <strong>'.$xmlfield->Media['bitrate'].' kbps</strong></li>';
echo '</div>';
echo '<div class="span4">';
	echo '<h4>Video Source Details</h4>';
	echo '<ul>';
		echo '<li>Width: <strong>'.$xmlfield->Media['width'].'</strong></li>';
		echo '<li>Height: <strong>'.$xmlfield->Media['height'].'</strong></li>';
		echo '<li>Aspect Ratio: <strong>'.$xmlfield->Media['aspectRatio'].'</strong></li>';
		echo '<li>Video Frame Rate: <strong>'.$xmlfield->Media['videoFrameRate'].'</strong></li>';
		echo '<li>Video Codec: <strong>'.$xmlfield->Media['videoCodec'].'</strong></li>';
	echo '</ul>';
	echo '<ul></ul>';
	echo '<h4>Audio Source Details</h4>';
	echo '<ul>';
		if ($xmlfield->Media['audioCodec'] == "dca") {
			echo '<li>Audio Codec: <strong>dts</strong></li>';
		} else {
			echo '<li>Audio Codec: <strong>'.$xmlfield->Media['audioCodec'].'</strong></li>';
		}
		echo '<li>Audio Channels: <strong>'.$xmlfield->Media['audioChannels'].'</strong></li>';
	echo '</ul>';
echo '</div>';
?>