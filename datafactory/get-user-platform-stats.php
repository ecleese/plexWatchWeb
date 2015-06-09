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

$database = dbconnect();

if (!isset($_POST['user'])) {
	echo "User field is required.";
	trigger_error('PlexWatchWeb :: POST parameter "user" not found.', E_USER_ERROR);
}

$plexWatchDbTable = dbTable('user');
$query = "SELECT xml, platform, COUNT(platform) as platform_count " .
	"FROM $plexWatchDbTable " .
	"WHERE user = :user " .
	"GROUP BY platform " .
	"ORDER BY platform ASC";
$params = array(':user'=>$_POST['user']);
$platformResults = getResults($database, $query, $params);
while ($row = $platformResults->fetch(PDO::FETCH_ASSOC)) {
	$platformXml = $row['xml'];
	$platformXmlField = simplexml_load_string($platformXml);
	$platformImage = getPlatformImage($platformXmlField);
	echo '<ul>';
		echo '<div class="user-platforms-instance">';
			echo '<li>';
				echo '<img class="user-platforms-instance-poster" src="' . $platformImage . '">';
				if ($platformXmlField->Player['platform'] == 'Chromecast') {
					echo '<div class="user-platforms-instance-name">';
						echo 'Plex/Web (Chrome) & Chromecast';
					echo '</div>';
				} else {
					echo '<div class="user-platforms-instance-name">';
					 	echo $row['platform'];
					echo '</div>';
				}
				echo '<div class="user-platforms-instance-playcount">';
					echo '<h3>' . $row['platform_count'] . '</h3><p> plays</p>';
				echo '</div>';
			echo '</li>';
		echo '</div>';
	echo '</ul>';
}
?>