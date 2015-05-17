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

if ($plexWatch['https'] == 'yes') {
	$plexWatchPmsUrl = "https://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpsPort']."";
} else if ($plexWatch['https'] == 'no') {
	$plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";
}

$db = dbconnect();

if (isset($_POST['user'])) {
	$user = $db->escapeString($_POST['user']);
} else {
	error_log('PlexWatchWeb :: POST parameter "user" not found.');
	echo "user field is required.";
	exit;
}

$plexWatchDbTable = dbTable('user');
$platformResults = $db->query ("SELECT xml,platform, COUNT(platform) as platform_count FROM ".$plexWatchDbTable." WHERE user = '$user' GROUP BY platform ORDER BY platform ASC") or die ("Failed to access plexWatch database. Please check your settings.");
while ($platformResultsRow = $platformResults->fetchArray()) {
	$platformXml = $platformResultsRow['xml'];
	$platformXmlField = simplexml_load_string($platformXml);
	$platformImage = getPlatformImage($platformXmlField);
	echo "<ul>";
		echo "<div class='user-platforms-instance'>";
			echo "<li>";
				echo "<img class='user-platforms-instance-poster' src='".$platformImage."'></img>";

				if ($platformXmlField->Player['platform'] == "Chromecast") {
					echo "<div class='user-platforms-instance-name'>Plex/Web (Chrome) & Chromecast</div>";
				} else {
					echo "<div class='user-platforms-instance-name'>".$platformResultsRow['platform']."</div>";
				}
				echo "<div class='user-platforms-instance-playcount'><h3>".$platformResultsRow['platform_count']."</h3><p> plays</p></div>";
			echo "</li>";
		echo "</div>";
	echo "</ul>";
}
?>
