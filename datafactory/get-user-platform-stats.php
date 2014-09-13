<?php

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
}else if ($plexWatch['https'] == 'no') {
        $plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";
}

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

$platformResults = $db->query ("SELECT xml,platform, COUNT(platform) as platform_count FROM ".$plexWatchDbTable." WHERE user = '$user' GROUP BY platform ORDER BY platform ASC") or die ("Failed to access plexWatch database. Please check your settings.");
								 
								
$platformImage = 0;
while ($platformResultsRow = $platformResults->fetchArray()) {

$platformXml = $platformResultsRow['xml'];
$platformXmlField = simplexml_load_string($platformXml);


        if(strstr($platformXmlField->Player['platform'], 'Roku')) {
                $platformImage = "images/platforms/roku.png";
        }else if(strstr($platformXmlField->Player['platform'], 'Apple TV')) {
                $platformImage = "images/platforms/appletv.png";
        }else if(strstr($platformXmlField->Player['platform'], 'Firefox')) {
                $platformImage = "images/platforms/firefox.png";
        }else if(strstr($platformXmlField->Player['platform'], 'Chromecast')) {
                $platformImage = "images/platforms/chromecast.png";
        }else if(strstr($platformXmlField->Player['platform'], 'Chrome')) {
                $platformImage = "images/platforms/chrome.png";
        }else if(strstr($platformXmlField->Player['platform'], 'Android')) {
                $platformImage = "images/platforms/android.png";
        }else if(strstr($platformXmlField->Player['platform'], 'Nexus')) {
                $platformImage = "images/platforms/android.png";
        }else if(strstr($platformXmlField->Player['platform'], 'iPad')) {
                $platformImage = "images/platforms/ios.png";
        }else if(strstr($platformXmlField->Player['platform'], 'iPhone')) {
                $platformImage = "images/platforms/ios.png";
        }else if(strstr($platformXmlField->Player['platform'], 'iOS')) {
                $platformImage = "images/platforms/ios.png";
        }else if(strstr($platformXmlField->Player['platform'], 'Plex Home Theater')) {
                $platformImage = "images/platforms/pht.png";
        }else if(strstr($platformXmlField->Player['platform'], 'Linux/RPi-XBMC')) {
                $platformImage = "images/platforms/xbmc.png";
        }else if(strstr($platformXmlField->Player['platform'], 'Safari')) {
                $platformImage = "images/platforms/safari.png";
        }else if(strstr($platformXmlField->Player['platform'], 'Internet Explorer')) {
                $platformImage = "images/platforms/ie.png";
        }else if(strstr($platformXmlField->Player['platform'], 'Unknown Browser')) {
                $platformImage = "images/platforms/default.png";
        }else if(strstr($platformXmlField->Player['platform'], 'Windows-XBMC')) {
                $platformImage = "images/platforms/xbmc.png";
        }else if(empty($platformXmlField->Player['platform'])) {
                if(strstr($platformXmlField->Player['title'], 'Apple')) {
                        $platformImage = "images/platforms/atv.png";
                //Code below matches Samsung naming standard: [Display Technology: 2 Letters][Size: 2 digits][Generation: 1 letter][Model: 4 digits]
                }else if(preg_match("/TV [a-z][a-z]\d\d[a-z]/i",$platformXmlField->Player['title'])) {
                        $platformImage = "images/platforms/samsung.png";	
                }else{
                        $platformImage = "images/platforms/default.png";
                }
        }
        
        echo "<ul>";
        echo "<div class='user-platforms-instance'>";
                echo "<li>";
                echo "<img class='user-platforms-instance-poster' src='".$platformImage."'></img>";

                if ($platformXmlField->Player['platform'] == "Chromecast") {
                        echo "<div class='user-platforms-instance-name'>Plex/Web (Chrome) & Chromecast</div>";
                }else{
                        echo "<div class='user-platforms-instance-name'>".$platformResultsRow['platform']."</div>";
                }


                echo "<div class='user-platforms-instance-playcount'><h3>".$platformResultsRow['platform_count']."</h3><p> plays</p></div>";
                echo "</li>";
        echo "</div>";
echo "</ul>";
}