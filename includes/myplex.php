

<?php

$guisettingsFile = "./../config/config.php";

if (file_exists($guisettingsFile)) { 
	require_once(dirname(__FILE__) . '/../config/config.php');
				
	if (empty($plexWatch['myPlexUser']) && empty($plexWatch['myPlexPass'])) {
		$myPlexAuthToken = '';
	}else{	

		$host = "https://my.plexapp.com/users/sign_in.xml";
		$username = $plexWatch['myPlexUser'];
		$password = base64_decode($plexWatch['myPlexPass']);


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
		$authCode = curl_getinfo($process, CURLINFO_HTTP_CODE);
		if($authCode == 401) {
			curl_close($process);
			$myPlexAuthToken = '';
			$errorCode = "<i class=\"icon icon-exclamation-sign\"></i> myPlex authentication failed. Check your myPlex username and password.";
		
		//Check for curl error
		}else if(curl_errno($process)) {	
			$curlError = curl_error($process);
			echo $curlError;
			$errorCode = "<i class=\"icon icon-exclamation-sign\"></i> ".$curlError."";
			curl_close($process);
			$myPlexAuthToken = '';
			
		}else{	
			$xml = simplexml_load_string($data);
			$myPlexAuthToken = $xml['authenticationToken'];
			
			if (empty($myPlexAuthToken)) {
				$errorCode = "<i class=\"icon icon-exclamation-sign\"></i> Error: Could not parse myPlex XML to retrieve authentication code.";
				curl_close($process);
			}else{
				$errorCode = '';
				curl_close($process);
				
			}	
		}
		
		
	}

}else{

echo "config file not found";

}
?>



 
