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

$plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";

$plexWatchDbTable = "";
if ($plexWatch['userHistoryGrouping'] == "yes") {
	$plexWatchDbTable = "grouped";
} else if ($plexWatch['userHistoryGrouping'] == "no") {
	$plexWatchDbTable = "processed";
}

$db = dbconnect();

if (isset($_POST['user'])) {
	$user = $db->escapeString($_POST['user']);
} else {
	error_log('PlexWatchWeb :: POST parameter "user" not found.');
	echo "user field is required.";
	exit;
}

$recentlyWatchedResults = $db->query("SELECT title, user, platform, time, stopped, ip_address, xml, paused_counter FROM ".$plexWatchDbTable." WHERE user = '$user' ORDER BY time DESC LIMIT 10");
// Run through each feed item
while ($recentlyWatchedRow = $recentlyWatchedResults->fetchArray()) {
	$request_url = $recentlyWatchedRow['xml'];
	$recentXml = simplexml_load_string($request_url);

	echo "<ul class='dashboard-recent-media'>";

	if ($recentXml['type'] == "episode") {
		if (!empty($plexWatch['myPlexAuthToken'])) {
			$myPlexAuthToken = $plexWatch['myPlexAuthToken'];
			$recentMetadata = "".$plexWatchPmsUrl."/library/metadata/".$recentXml['ratingKey']."?X-Plex-Token=".$myPlexAuthToken."";
		} else {
			$myPlexAuthToken = '';
			$recentMetadata = "".$plexWatchPmsUrl."/library/metadata/".$recentXml['ratingKey']."";
		}

		if ($recentThumbUrlRequest = @simplexml_load_file ($recentMetadata)) {
			$recentThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$recentThumbUrlRequest->Video['parentThumb']."&width=136&height=280";
			$recentgThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$recentThumbUrlRequest->Video['grandparentThumb']."&width=136&height=280";

			echo "<div class='dashboard-recent-media-instance'>";
				echo "<li>";
					echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$recentXml['ratingKey']. "'>";
					if ($recentThumbUrlRequest->Video['parentThumb']) {
						echo "<img src='includes/img.php?img=".urlencode($recentThumbUrl)."' class='poster-face'></img></a></div></div>";
					} elseif ($recentThumbUrlRequest->Video['grandparentThumb']) {
						echo "<img src='includes/img.php?img=".urlencode($recentgThumbUrl)."' class='poster-face'></img></a></div></div>";
					} else {
						echo "<img src='images/poster.png' class='poster-face'></img></a></div></div>";
					}

					echo "<div class=dashboard-recent-media-metacontainer>";
						$parentIndexPadded = sprintf("%01s", $recentXml['parentIndex']);
						$indexPadded = sprintf("%02s", $recentXml['index']);
						echo "<h3>Season ".$parentIndexPadded.", Episode ".$indexPadded."</h3>";
					echo "</div>";
				echo "</li>";
			echo "</div>";
		}
	} else if ($recentXml['type'] == "movie") {
		if (!empty($plexWatch['myPlexAuthToken'])) {
			$myPlexAuthToken = $plexWatch['myPlexAuthToken'];
			$recentMetadata = "".$plexWatchPmsUrl."/library/metadata/".$recentXml['ratingKey']."?X-Plex-Token=".$myPlexAuthToken."";
		} else {
			$myPlexAuthToken = '';
			$recentMetadata = "".$plexWatchPmsUrl."/library/metadata/".$recentXml['ratingKey']."";
		}

		if ($recentThumbUrlRequest = @simplexml_load_file ($recentMetadata)) {
			$recentThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$recentThumbUrlRequest->Video['thumb']."&width=136&height=280";

			echo "<div class='dashboard-recent-media-instance'>";
				echo "<li>";
					echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$recentXml['ratingKey']. "'>";
					if ($recentThumbUrlRequest->Video['thumb']) {
						echo "<img src='includes/img.php?img=".urlencode($recentThumbUrl)."' class='poster-face'></img></a></div></div>";
					} else {
						echo "<img src='images/poster.png' class='poster-face'></img></a></div></div>";
					}

					echo "<div class=dashboard-recent-media-metacontainer>";
						$parentIndexPadded = sprintf("%01s", $recentXml['parentIndex']);
						$indexPadded = sprintf("%02s", $recentXml['index']);
						echo "<h3>".$recentXml['title']." (".$recentXml['year'].")</h3>";
					echo "</div>";
				echo "</li>";
			echo "</div>";
		}
	} else if ($recentXml['type'] == "clip") {
		if (!empty($plexWatch['myPlexAuthToken'])) {
			$myPlexAuthToken = $plexWatch['myPlexAuthToken'];
			$recentMetadata = "".$plexWatchPmsUrl."/library/metadata/".$recentXml['ratingKey']."?X-Plex-Token=".$myPlexAuthToken."";
		} else {
			$myPlexAuthToken = '';
			$recentMetadata = "".$plexWatchPmsUrl."/library/metadata/".$recentXml['ratingKey']."";
		}
		if ($recentThumbUrlRequest = @simplexml_load_file ($recentMetadata)) {
			$recentThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$recentThumbUrlRequest->Video['thumb']."&width=136&height=280";

			echo "<div class='dashboard-recent-media-instance'>";
				echo "<li>";
					echo "<div class='poster'><div class='poster-face'><a href='" .$recentXml['ratingKey']. "'><img src='images/poster.png' class='poster-face'></img></a></div></div>";
					echo "<div class=dashboard-recent-media-metacontainer>";
						$parentIndexPadded = sprintf("%01s", $recentXml['parentIndex']);
						$indexPadded = sprintf("%02s", $recentXml['index']);
						echo "<h3>".$recentXml['title']." (".$recentXml['year'].")</h3>";
					echo "</div>";
				echo "</li>";
			echo "</div>";
		}
	}
}
echo "</ul>";
?>