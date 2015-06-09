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

if (!isset($_POST['user'])) {
	echo "User field is required.";
	trigger_error('PlexWatchWeb :: POST parameter "user" not found.', E_USER_ERROR);
}

$database = dbconnect();
$plexWatchDbTable = dbTable('user');
$query = "SELECT title, user, platform, time, " .
		"stopped, ip_address, xml, paused_counter " .
	"FROM " . $plexWatchDbTable . " " .
	"WHERE user = :user " .
	"ORDER BY time DESC " .
	"LIMIT 10";
$params = array(':user'=>$_POST['user']);
$results = getResults($database, $query, $params);
$imgBase = 'includes/img.php?img=';
$imgSize = '&width=136&height=280';

echo '<ul class="dashboard-recent-media">';
// Run through each feed item
while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
	$rawXML = $row['xml'];
	$xml = simplexml_load_string($rawXML);
	$rawMetadata = getPmsData('/library/metadata/' . $xml['ratingKey']);
	if ($metadata = simplexml_load_string($rawMetadata)) {
		$thumbUrl = 'images/poster.png';
		if ($xml['type'] == 'episode') {
			if ($metadata->Video['parentThumb']) {
				$thumbUrl = $imgBase . urlencode($metadata->Video['parentThumb'] . $imgSize);
			} else if ($metadata->Video['grandparentThumb']) {
				$thumbUrl = $imgBase . urlencode($metadata->Video['grandparentThumb'] . $imgSize);
			}
		} else if ($xml['type'] == 'movie') {
			if ($metadata->Video['thumb']) {
				$thumbUrl = $imgBase . urlencode($metadata->Video['thumb'] . $imgSize);
			}
		} else if ($xml['type'] == 'clip') {
			if ($metadata->Video['thumb']) {
				$thumbUrl = $imgBase . urlencode($metadata->Video['thumb'] . $imgSize);
			}
		}
	} else {
		continue;
	}
	echo '<div class="dashboard-recent-media-instance">';
		echo '<li>';
			echo '<div class="poster">';
				echo '<div class="poster-face">';
					echo '<a href="info.php?id=' . $xml['ratingKey'] . '">';
						echo '<img src="' . $thumbUrl . '" class="poster-face">';
					echo '</a>';
				echo '</div>';
			echo '</div>';
			echo '<div class="dashboard-recent-media-metacontainer">';
				if ($xml['type'] == 'episode') {
					$parentIndexPadded = sprintf('%01s', $xml['parentIndex']);
					$indexPadded = sprintf('%02s', $xml['index']);
					echo '<h3>Season ' . $parentIndexPadded . ', Episode ' . $indexPadded . '</h3>';
				} else { // 'movie' || 'clip'
					echo '<h3>' . $xml['title'] . ' (' . $xml['year'] . ')</h3>';
				}
			echo '</div>';
		echo '</li>';
	echo '</div>';
}
echo '</ul>';
?>