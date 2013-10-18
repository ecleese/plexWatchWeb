

<?php

$guisettingsFile = "./../config/config.php";

if (file_exists($guisettingsFile)) { 
	require_once(dirname(__FILE__) . '/../config/config.php');
				
	if (empty($plexWatch['myPlexUser']) && empty($plexWatch['myPlexPass'])) {
		//$myPlexAuthFail = echo "no myPlex username and password set.";
		$myPlexAuthToken = '';
	}else{	

		$host = "https://my.plexapp.com/users/sign_in.xml";
		$username = $plexWatch['myPlexUser'];
		$password = $plexWatch['myPlexPass'];


		$process = curl_init($host);
		curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/xml; charset=utf-8', 'Content-Length: 0', 'X-Plex-Client-Identifier: plexWatchWeb'));
		curl_setopt($process, CURLOPT_HEADER, 0);
		curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($process, CURLOPT_USERPWD, $username . ":" . $password);
		curl_setopt($process, CURLOPT_TIMEOUT, 30);
		curl_setopt($process, CURLOPT_HTTPGET, TRUE);
		curl_setopt($process, CURLOPT_POST, 1);

		curl_setopt($process, CURLOPT_RETURNTRANSFER, true);

		$data = curl_exec($process);
		
		
		
		//Check for 401 (authentication failure)
		$httpCode = curl_getinfo($process, CURLINFO_HTTP_CODE);
		if($httpCode == 401) {
			curl_close($process);
			$myPlexAuthToken = '';
			$httpCode401 = "<i class=\"icon icon-exclamation-sign\"></i> myPlex authentication failed. Check your myPlex username and password.";
		}else{	
			curl_close($process);
			$httpCode401 = '';
			
			$xml = simplexml_load_string($data);
			$myPlexAuthToken = $xml['authenticationToken'];
			
		}
		
		
	}

}else{

echo "config file not found";

}
?>



 
