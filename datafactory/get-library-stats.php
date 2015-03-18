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


if (!empty($plexWatch['myPlexAuthToken'])) {
    $myPlexAuthToken = $plexWatch['myPlexAuthToken'];
    $fileContents = '';
    if ($fileContents = file_get_contents("".$plexWatchPmsUrl."/status/sessions?X-Plex-Token=".$myPlexAuthToken."")) {
        $statusSessions = simplexml_load_string($fileContents) or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");
    }
    $sections = simplexml_load_file("".$plexWatchPmsUrl."/library/sections?X-Plex-Token=".$myPlexAuthToken."") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");
}else{
    $myPlexAuthToken = '';
    if ($fileContents = file_get_contents("".$plexWatchPmsUrl."/status/sessions?X-Plex-Token=".$myPlexAuthToken."")) {
        $statusSessions = simplexml_load_string($fileContents) or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");
    }
    $sections = simplexml_load_file("".$plexWatchPmsUrl."/library/sections") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");
}

echo "<ul>";

foreach ($sections->children() as $section) {

    if (!empty($plexWatch['myPlexAuthToken'])) {
        if ($section['type'] == "movie") {
            $sectionDetails = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?type=1&sort=addedAt:desc&X-Plex-Container-Start=0&X-Plex-Container-Size=1&X-Plex-Token=".$myPlexAuthToken."") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");

            echo "<li>";
            echo "<h1>".$sectionDetails['totalSize']."</h1><h5>".$section['title']."</h5>";
            echo "</li>";
        }else if ($section['type'] == "show") {
            $sectionDetails = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?type=2&sort=addedAt:desc&X-Plex-Container-Start=0&X-Plex-Container-Size=1&X-Plex-Token=".$myPlexAuthToken."") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");
            $tvEpisodeCount = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?type=4&X-Plex-Container-Start=0&X-Plex-Container-Size=1&X-Plex-Token=".$myPlexAuthToken."") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");

            echo "<li>";
            echo "<h1>".$sectionDetails['totalSize']."</h1><h5>".$section['title']."</h5>";
            echo "</li>";
            echo "<li>";
            echo "<h1>".$tvEpisodeCount['totalSize']."</h1><h5>TV Episodes</h5>";
            echo "</li>";
        }else if ($section['type'] == "artist") {
            $sectionDetails = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?sort=addedAt:desc&X-Plex-Container-Start=0&X-Plex-Container-Size=1&X-Plex-Token=".$myPlexAuthToken."") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");

            echo "<li>";
            echo "<h1>".$sectionDetails['totalSize']."</h1><h5>".$section['title']."</h5>";
            echo "</li>";

        }
    }else{
        if ($section['type'] == "movie") {
            $sectionDetails = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?type=1&sort=addedAt:desc&X-Plex-Container-Start=0&X-Plex-Container-Size=1") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");

            echo "<li>";
            echo "<h1>".$sectionDetails['totalSize']."</h1><h5>".$section['title']."</h5>";
            echo "</li>";
        }else if ($section['type'] == "show") {
            $sectionDetails = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?type=2&sort=addedAt:desc&X-Plex-Container-Start=0&X-Plex-Container-Size=1") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");
            $tvEpisodeCount = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?type=4&X-Plex-Container-Start=0&X-Plex-Container-Size=1") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");

            echo "<li>";
            echo "<h1>".$sectionDetails['totalSize']."</h1><h5>".$section['title']."</h5>";
            echo "</li>";
            echo "<li>";
            echo "<h1>".$tvEpisodeCount['totalSize']."</h1><h5>TV Episodes</h5>";
            echo "</li>";
        }else if ($section['type'] == "artist") {
            $sectionDetails = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?sort=addedAt:desc&X-Plex-Container-Start=0&X-Plex-Container-Size=1") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");

            echo "<li>";
            echo "<h1>".$sectionDetails['totalSize']."</h1><h5>".$section['title']."</h5>";
            echo "</li>";
        }
    }
}

date_default_timezone_set(@date_default_timezone_get());
$db = dbconnect();
$users = $db->querySingle("SELECT count(DISTINCT user) as users FROM processed") or die ("Failed to access plexWatch database. Please check your settings.");
if ($users === false) {
	die ("Failed to access plexWatch database. Please check your settings.");
}

echo "<li>";
echo "<h1>".$users."</h1><h5>Users</h5>";
echo "</li>";


echo "</ul>";