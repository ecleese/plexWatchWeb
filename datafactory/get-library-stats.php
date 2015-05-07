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
$PMSdieMsg = "<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>";


if (!empty($plexWatch['myPlexAuthToken'])) {
    $myPlexAuthToken = "X-Plex-Token=".$plexWatch['myPlexAuthToken'];
} else {
    $myPlexAuthToken = '';
}

if ($fileContents = file_get_contents("".$plexWatchPmsUrl."/status/sessions?".$myPlexAuthToken."")) {
	$statusSessions = simplexml_load_string($fileContents) or die ($PMSdieMsg);
}
$sections = simplexml_load_file("".$plexWatchPmsUrl."/library/sections?".$myPlexAuthToken."") or die ($PMSdieMsg);


echo "<ul>";

foreach ($sections->children() as $section) {
	if (($section['type'] == "movie") || ($section['type'] == "artist"))  {
		$sectionDetails = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?X-Plex-Container-Start=0&X-Plex-Container-Size=0&".$myPlexAuthToken."") or die ($PMSdieMsg);

		echo "<li>";
		echo "<h1>".$sectionDetails['totalSize']."</h1><h5>".$section['title']."</h5>";
		echo "</li>";
	} else if ($section['type'] == "show") {
		$sectionDetails = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?type=2&X-Plex-Container-Start=0&X-Plex-Container-Size=0&".$myPlexAuthToken."") or die ($PMSdieMsg);
		$tvEpisodeCount = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?type=4&X-Plex-Container-Start=0&X-Plex-Container-Size=0&".$myPlexAuthToken."") or die ($PMSdieMsg);

		// Cut ' Shows' from the end of the title
		if (strlen($section['title']) > 6 && strcmp(' Shows', substr($section['title'], -6)) == 0) {
			$title = substr($section['title'], 0, -6);
		} else {
			$title = $section['title'];
		}
		echo "<li>";
		echo "<h1>".$sectionDetails['totalSize']."</h1><h5>".$title." Shows</h5>";
		echo "</li>";
		echo "<li>";
		echo "<h1>".$tvEpisodeCount['totalSize']."</h1><h5>".$title." Episodes</h5>";
		echo "</li>";
	}
}

$db = dbconnect();
$users = $db->querySingle("SELECT count(DISTINCT user) as users FROM processed") or die ("Failed to access plexWatch database. Please check your settings.");
if ($users === false) {
	die ("Failed to access plexWatch database. Please check your settings.");
}

echo "<li>";
echo "<h1>".$users."</h1><h5>Users</h5>";
echo "</li>";

echo "</ul>";