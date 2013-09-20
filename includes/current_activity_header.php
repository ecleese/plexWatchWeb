<?php

include_once('../config.php');
			
$statusSessions2 = simplexml_load_file("http://".$plexWatch['pmsUrl'].":32400/status/sessions");
					
echo "<h3>Current Activity <strong>".$statusSessions2['size']."</strong> user(s)</h3>";
					
					
?>