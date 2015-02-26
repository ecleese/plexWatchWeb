<?php

require_once(dirname(__FILE__) . '/../config/config.php');

$plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";

$fileContents = '';

if (!empty($plexWatch['myPlexAuthToken'])) {
        $myPlexAuthToken = $plexWatch['myPlexAuthToken'];                        
        if ($fileContents = file_get_contents("".$plexWatchPmsUrl."/status/sessions?X-Plex-Token=".$plexWatch['myPlexAuthToken']."")) {
      		$statusSessions = simplexml_load_string($fileContents) or die ('<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>');
  		}
}else{
        $myPlexAuthToken = '';                        
        if ($fileContents = file_get_contents("".$plexWatchPmsUrl."/status/sessions")) {
      		$statusSessions = simplexml_load_string($fileContents) or die ('<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>');
   		}
}



if ($statusSessions['size'] == '0') {                                
        echo "<h3>Current Activity</h3>";
}else{
        echo "<h3>Current Activity <strong>".$statusSessions['size']."</strong> user(s)</h3>";
}
                                
?>