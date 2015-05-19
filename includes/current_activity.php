<?php
require_once(dirname(__FILE__) . '/../config/config.php');

$plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";

$fileContents = '';

if (!empty($plexWatch['myPlexAuthToken'])) {
	$myPlexAuthToken = '?X-Plex-Token='.$plexWatch['myPlexAuthToken'];
} else {
	$myPlexAuthToken = '';
}
$fileContents = file_get_contents("" . $plexWatchPmsUrl .
	"/status/sessions" . $myPlexAuthToken) or
	die ("<div class='alert alert-warning'>Failed to access Plex Media Server. " .
		"Please check your settings.</div>");
$statusSessions = simplexml_load_string($fileContents) or die ("Failed to parse Plex response.");

if ($statusSessions['size'] == '0') {
	echo "<h5><strong>Nothing is currently being watched.</strong></h5><br>";
	return; // End execution of the current script file
}

function printPosterFace($session) {
	global $plexWatch, $plexWatchPmsUrl;
	$sessionThumbUrl = "".$plexWatchPmsUrl.
		"/photo/:/transcode?url=http://127.0.0.1:".
		$plexWatch['pmsHttpPort']."";
	if ($session['type'] == "clip" || $session['type'] == 'movie') {
		$sessionThumbUrl .= "".$session['art']."";
	} else {
		$sessionThumbUrl .= "".$session['thumb']."";
	}
	if ($session['type'] == 'track') {
		$sessionThumbUrl .= "&width=300&height=300";
	} else {
		$sessionThumbUrl .= "&width=300&height=169";
	}
	/* Apparently some ancient PMS versions would send out episodes without a
	 * librarySectionID being set, but would set a $session['url']. Since no
	 * current PMS does this the support has been removed. */
	if ($session['type'] == 'track') {
		echo "<div class='art-music-face' " .
			"style='background-image:url(includes/img.php?img=" .
			urlencode($sessionThumbUrl).")'></div>";
	} else {
		echo "<a href='info.php?id=" .$session['ratingKey']. "'>";
			echo "<img src='includes/img.php?img=".urlencode($sessionThumbUrl)."'></img>";
		echo "</a>";
	}
}

function printUserStatus($session) {
	if ($session['type'] == 'track') {
		$action = "playing";
	} else {
		$action = "watching";
	}
	if (empty($session->User['title'])) {
		$userName = 'Local';
		$friendlyName = 'Local';
	} else {
		$userName = $session->User['title'];
		$friendlyName = FriendlyName($session->User['title'], $session->Player['title']);
	}
	if ($session->Player['state'] == "playing") {
		echo "<div class='dashboard-activity-metadata-user'>";
			echo "<a href='user.php?user=".$userName."'>".$friendlyName."</a> is $action";
		echo "</div>";
	} elseif ($session->Player['state'] == "paused") {
		echo "<div class='dashboard-activity-metadata-user'>";
			echo "<a href='user.php?user=".$userName."'>".$friendlyName."</a> has paused";
		echo "</div>";
	} elseif ($session->Player['state'] == "buffering") {
		echo "<div class='dashboard-activity-metadata-user'>";
			echo "<a href='user.php?user=".$userName."'>".$friendlyName."</a> is buffering";
		echo "</div>";
	}
}

function printContentDetails($session) {
	if ($session['type'] == 'track') {
		echo "Artist: <strong>".$session['grandparentTitle']."</strong>";
		echo "<br>";
		echo "Album: <strong>".$session['parentTitle']."</strong>";
		echo "<br>";
	}
	if ($session->Player['state'] == "playing") {
		echo "State: <strong>Playing</strong>";
	} else if ($session->Player['state'] == "paused") {
		echo "State: <strong>Paused</strong>";
	} else if ($session->Player['state'] == "buffering") {
		echo "State: <strong>Buffering</strong>";
	}
	echo "<br>";
	if (!array_key_exists('TranscodeSession', $session)) {
		echo "Stream: <strong>Direct Play</strong>";
	} else {
		echo "Stream: <strong>Transcoding</strong>";
	}
	echo "<br>";
	if ($session['type'] != 'track') {
		printVideoInfo($session);
	}
	printAudioInfo($session);
}

function printVideoInfo($session) {
	if (!array_key_exists('TranscodeSession', $session)) {
		echo "Video: <strong>".$session->Media['videoCodec'].
			" (".$session->Media['width']."x".$session->Media['height']."p)</strong>";
	} else if ($session->TranscodeSession['videoDecision'] == "transcode") {
		echo "Video: <strong>Transcode (".$session->TranscodeSession['videoCodec'].")".
			" (".$session->TranscodeSession['width']."x".$session->TranscodeSession['height']."p)</strong>";
	} else if ($session->TranscodeSession['videoDecision'] == "copy") {
		echo "Video: <strong>Direct Stream (".$session->TranscodeSession['videoCodec'].")".
			" (".$session->TranscodeSession['width']."x".$session->TranscodeSession['height']."p)</strong>";
	}
	echo "<br>";
}

function printAudioInfo($session) {
	if (!array_key_exists('TranscodeSession', $session)) {
		if ($session->Media['audioCodec'] == "dca") {
			echo "Audio: <strong>DTS (".$session->Media['audioChannels']."ch)</strong>";
		} else if ($session->Media['audioCodec'] == "ac3") {
			echo "Audio: <strong>Dolby Digital (".$session->Media['audioChannels']."ch)</strong>";
		} else {
			echo "Audio: <strong>".$session->Media['audioCodec'].
				" (".$session->Media['audioChannels']."ch)</strong>";
		}
	} else if ($session->TranscodeSession['audioDecision'] == "transcode") {
		echo "Audio: <strong>Transcode (".$session->TranscodeSession['audioCodec'].")".
			" (".$session->TranscodeSession['audioChannels']."ch)</strong>";
	} else if ($session->TranscodeSession['audioDecision'] == "copy") {
		if ($session->TranscodeSession['audioCodec'] == "dca") {
			echo "Audio: <strong>Direct Stream (DTS)".
				" (".$session->TranscodeSession['audioChannels']."ch)</strong>";
		} else if ($session->Media['audioCodec'] == "ac3") {
			echo "Audio: <strong>Direct Stream (AC3)".
				" (".$session->TranscodeSession['audioChannels']."ch)</strong>";
		} else {
			echo "Audio: <strong>Direct Stream (".$session->TranscodeSession['audioCodec'].")".
				" (".$session->TranscodeSession['audioChannels']."ch)</strong>";
		}
	}
}

function printSession($session) {
	echo "<div class='instance'>";
		echo "<div class='poster'>";
			echo "<div class='dashboard-activity-poster-face'>";
				printPosterFace($session);
			echo "</div>";
			echo "<div class='dashboard-activity-metadata-wrapper'>";
				echo "<div class='dashboard-activity-instance-overlay'>";
					echo "<div class='dashboard-activity-metadata-progress-minutes'>";
						if ($session['duration'] == 0) {
							$percentComplete = 0;
						} else {
							$percentComplete = sprintf("%2d", ($session['viewOffset'] / $session['duration']) * 100);
						}
						if ($percentComplete >= 90) {
							$percentComplete = 100;
						}
						echo "<div class='progress progress-warning'>";
							echo "<div class='bar' style='width: ".$percentComplete."%'>".$percentComplete."%</div>";
						echo "</div>";
					echo "</div>";
					echo "<div class='dashboard-activity-metadata-platform'>";
						echo "<img src='".getPlatformImage($session)."'></>";
					echo "</div>";
					printUserStatus($session);
					echo "<div class='dashboard-activity-metadata-title'>";
						if ($session['type'] == 'episode') {
							echo "<a href='info.php?id=" .$session['ratingKey']. "'>".
								$session['grandparentTitle'].' - "'.$session['title'].'"</a>';
						} else if ($session['type'] == 'movie') {
							echo "<a href='info.php?id=" .$session['ratingKey']."'>".$session['title']."</a>";
						} else if ($session['type'] == 'clip') {
							echo "".$session['title']."";
						} else if ($session['type'] == 'track') {
							echo $session['grandparentTitle']." - ".$session['title'];
						}
					echo "</div>";
				echo "</div>"; // .dashboard-activity-instance-overlay
				echo "<div id='infoDetails-".$session->Player['machineIdentifier']."' class='collapse out'>";
					echo "<div class='dashboard-activity-info-details-overlay'>";
						echo "<div class='dashboard-activity-info-details-content'>";
							printContentDetails($session);
						echo "</div>"; // .dashboard-activity-info-details-content
					echo "</div>"; // .dashboard-activity-info-details-overlay
				echo "</div>"; // #infoDetails-
			echo "</div>"; // .dashboard-activity-metadata-wrapper
		echo "</div>"; // .poster
		echo "<div class='dashboard-activity-button-info'>";
			echo "<button type='button' class='btn btn-warning' data-toggle='collapse' data-target='#infoDetails-".$session->Player['machineIdentifier']."'>";
				echo "<i class='icon-info-sign icon-white'></i>";
			echo "</button>";
		echo "</div>"; // .dashboard-activity-button-info
	echo "</div>"; // .instance
}

// Run through each feed item
foreach ($statusSessions->Video as $session) {
	printSession($session);
}
foreach ($statusSessions->Track as $session) {
	printSession($session);
}
?>
