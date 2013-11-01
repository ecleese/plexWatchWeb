

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
		curl_setopt($process, CURLOPT_HEADER, 1);
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
			$myPlexAuthToken = '';
			$curlError = curl_error($process);
			echo $curlError;
			$errorCode = "<i class=\"icon icon-exclamation-sign\"></i> ".$curlError."";
			curl_close($process);
			
		}else{	
			curl_close($process);
			$errorCode = '';
			$xml = simplexml_load_string($data);
			$myPlexAuthToken = $xml['authenticationToken'];
			echo $myPlexAuthToken;
		}
		
		
	}

}else{

echo "config file not found";

}
?>



 
