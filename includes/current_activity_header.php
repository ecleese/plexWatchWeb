<?php

require_once(dirname(__FILE__) . '/../config/config.php');

if ($plexWatch['https'] == 'yes') {
	$plexWatchPmsUrl = "https://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpsPort']."";
}else if ($plexWatch['https'] == 'no') {
	$plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";
}
if (!empty($plexWatch['myPlexAuthToken'])) {
	$myPlexAuthToken = $plexWatch['myPlexAuthToken'];			
	define("fileContents", file_get_contents("".$plexWatchPmsUrl."/status/sessions?query=c&X-Plex-Token=".$plexWatch['myPlexAuthToken'].""));
   if (fileContents) {
      $statusSessions = simplexml_load_string(fileContents) or die ('<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>');
   }
}else{
	$myPlexAuthToken = '';			
	define("fileContents", file_get_contents("".$plexWatchPmsUrl."/status/sessions"));
   if (fileContents) {
      $statusSessions = simplexml_load_string($fileContents) or die ('<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>');
   }
}



if ($statusSessions['size'] == '0') {				
	echo "<h3>Current Activity</h3>";
}else{
	echo "<h3>Current Activity <strong>".$statusSessions['size']."</strong> user(s)</h3>";
}
				
?>
