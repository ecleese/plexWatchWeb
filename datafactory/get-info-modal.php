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

$plexWatchDbTable = "";

if ($plexWatch['globalHistoryGrouping'] == "yes") {
	$plexWatchDbTable = "grouped";
} else if ($plexWatch['globalHistoryGrouping'] == "no") {
	$plexWatchDbTable = "processed";
}
$results = $db->querySingle("SELECT xml FROM $plexWatchDbTable WHERE id = $id") or die ("Failed to access plexWatch database. Please check your settings.");
$xmlfield = simplexml_load_string($results);

if (array_key_exists('TranscodeSession',$xmlfield)) { ?>
	<div class="span4">
		<h4>Stream Details</h4>
		<ul>
			<h5>Video</h5>
			<li>Stream Type: <strong><?php echo $xmlfield->TranscodeSession['videoDecision']; ?></strong></li>
			<li>Video Resolution: <strong><?php echo $xmlfield->TranscodeSession['height']; ?>p</strong></li>
			<li>Video Codec: <strong><?php echo $xmlfield->TranscodeSession['videoCodec']; ?></strong></li>
			<li>Video Width: <strong><?php echo $xmlfield->TranscodeSession['width']; ?></strong></li>
			<li>Video Height: <strong><?php echo $xmlfield->TranscodeSession['height']; ?></strong></li>
		</ul>
		<ul>
			<h5>Audio</h5>
			<li>Stream Type: <strong><?php echo $xmlfield->TranscodeSession['audioDecision']; ?></strong></li>
			<?php if ($xmlfield->TranscodeSession['audioCodec'] == "dca") { ?>
				<li>Audio Codec: <strong>dts</strong></li>
			<?php } else { ?>
				<li>Audio Codec: <strong><?php echo $xmlfield->TranscodeSession['audioCodec']; ?></strong></li>
			<?php } ?>
			<li>Audio Channels: <strong><?php echo $xmlfield->TranscodeSession['audioChannels']; ?></strong></li>
		</ul>
	</div>
	<div class="span4">
		<h4>Media Source Details</h4>
		<li>Container: <strong><?php echo $xmlfield->Media['container']; ?></strong></li>
		<li>Resolution: <strong><?php echo $xmlfield->Media['videoResolution']; ?>p</strong></li>
		<li>Bitrate: <strong><?php echo $xmlfield->Media['bitrate']; ?> kbps</strong></li>
	</div>
	<div class="span4">
		<h4>Video Source Details</h4>
		<ul>
			<li>Width: <strong><?php echo $xmlfield->Media['width']; ?></strong></li>
			<li>Height: <strong><?php echo $xmlfield->Media['height']; ?></strong></li>
			<li>Aspect Ratio: <strong><?php echo $xmlfield->Media['aspectRatio']; ?></strong></li>
			<li>Video Frame Rate: <strong><?php echo $xmlfield->Media['videoFrameRate']; ?></strong></li>
			<li>Video Codec: <strong><?php echo $xmlfield->Media['videoCodec']; ?></strong></li>
		</ul>
		<ul></ul>
		<h4>Audio Source Details</h4>
		<ul>
			<?php if ($xmlfield->Media['audioCodec'] == "dca") { ?>
				<li>Audio Codec: <strong>dts</strong></li>
			<?php } else { ?>
				<li>Audio Codec: <strong><?php echo $xmlfield->Media['audioCodec']; ?></strong></li>
			<?php } ?>
			<li>Audio Channels: <strong><?php echo $xmlfield->Media['audioChannels']; ?></strong></li>
		</ul>
	</div>
<?php } else { ?>
	<div class="span4">
		<h4>Stream Details</strong></h4>
		<ul>
			<h5>Video</h5>
			<li>Stream Type: <strong>Direct Play</strong></li>
			<li>Video Resolution: <strong><?php echo $xmlfield->Media['videoResolution']; ?>p</strong></li>
			<li>Video Codec: <strong><?php echo $xmlfield->Media['videoCodec']; ?></strong></li>
			<li>Video Width: <strong><?php echo $xmlfield->Media['width']; ?></strong></li>
			<li>Video Height: <strong><?php echo $xmlfield->Media['height']; ?></strong></li>
		</ul>
		<ul>
			<h5>Audio</h5>
			<li>Stream Type: <strong>Direct Play</strong></li>
			<?php if ($xmlfield->Media['audioCodec'] == "dca") { ?>
				<li>Audio Codec: <strong>dts</strong></li>
			<?php } else { ?>
				<li>Audio Codec: <strong><?php echo $xmlfield->Media['audioCodec']; ?></strong></li>
			<?php } ?>
			<li>Audio Channels: <strong><?php echo $xmlfield->Media['audioChannels']; ?></strong></li>
		</ul>
	</div>
	<div class="span4">
		<h4>Media Source Details</h4>
		<li>Container: <strong><?php echo $xmlfield->Media['container']; ?></strong></li>
		<li>Resolution: <strong><?php echo $xmlfield->Media['videoResolution']; ?>p</strong></li>
		<li>Bitrate: <strong><?php echo $xmlfield->Media['bitrate']; ?> kbps</strong></li>
	</div>
	<div class="span4">
		<h4>Video Source Details</h4>
		<ul>
			<li>Width: <strong><?php echo $xmlfield->Media['width']; ?></strong></li>
			<li>Height: <strong><?php echo $xmlfield->Media['height']; ?></strong></li>
			<li>Aspect Ratio: <strong><?php echo $xmlfield->Media['aspectRatio']; ?></strong></li>
			<li>Video Frame Rate: <strong><?php echo $xmlfield->Media['videoFrameRate']; ?></strong></li>
			<li>Video Codec: <strong><?php echo $xmlfield->Media['videoCodec']; ?></strong></li>
		</ul>
		<ul> </ul>
		<h4>Audio Source Details</h4>
		<ul>
			<?php if ($xmlfield->Media['audioCodec'] == "dca") { ?>
				<li>Audio Codec: <strong>dts</strong></li>
			<?php } else { ?>
				<li>Audio Codec: <strong><?php echo $xmlfield->Media['audioCodec']; ?></strong></li>
			<?php } ?>
			<li>Audio Channels: <strong><?php echo $xmlfield->Media['audioChannels']; ?></strong></li>
		</ul>
	</div>
<?php } ?>