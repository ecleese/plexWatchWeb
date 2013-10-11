<?php

require_once(dirname(__FILE__) . '/../config.php');
			
$statusSessions = simplexml_load_file("http://".$plexWatch['pmsUrl'].":".$plexWatch['pmsPort']."/status/sessions") or die ('Failed to access Plex Media Server. Please check your server and config.php settings.');

	if ($statusSessions['size'] == '0') {				
		echo "<h3>Current Activity</h3>";
	}else{
		echo "<h3>Current Activity <strong>".$statusSessions['size']."</strong> user(s)</h3>";
	}
				
?>