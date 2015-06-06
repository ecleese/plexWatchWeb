<?php
$guisettingsFile = dirname(__FILE__) . '/../config/config.php';
if (!file_exists($guisettingsFile)) {
	$error_msg = 'config file not found';
	echo $error_msg;
	trigger_error($error_msg, E_USER_ERROR);
}
require_once($guisettingsFile);

$myPlexAuthToken = '';
if (empty($plexWatch['myPlexUser']) || empty($plexWatch['myPlexPass'])) {
	return;
}
$host = 'https://plex.tv/users/sign_in.xml';
$username = $plexWatch['myPlexUser'];
$password = base64_decode($plexWatch['myPlexPass']);
$process = curl_init($host);
curl_setopt($process, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/xml; charset=utf-8',
	'Content-Length: 0',
	'X-Plex-Device-Name: plexWatch/Web',
	'X-Plex-Product: plexWatch/Web',
	// FIXME: Version should be specified in the settings
	'X-Plex-Version: ' . 'v1.7.0 dev',
	'X-Plex-Client-Identifier: ' . uniqid('plexWatchWeb', true)
));
curl_setopt($process, CURLOPT_HEADER, false);
curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($process, CURLOPT_USERPWD, $username . ':' . $password);
curl_setopt($process, CURLOPT_TIMEOUT, 30);
curl_setopt($process, CURLOPT_HTTPGET, true);
curl_setopt($process, CURLOPT_POST, true);
curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($process);

$authCode = curl_getinfo($process, CURLINFO_HTTP_CODE);
$curlError = curl_error($process);
curl_close($process);
if ($authCode == 401) {
	// Authentication failure
	$errorCode = 'Plex.tv authentication failed. Check your Plex.tv username and password.';
	return;
} else if ($curlError != 0) {
	// cURL error
	$errorCode = $curlError;
	return;
} else {
	$xml = simplexml_load_string($data);
	if ($xml === false) {
		$errorCode = 'Error: Could not parse Plex.tv XML to retrieve authentication code.';
		return;
	}
	$myPlexAuthToken = $xml['authenticationToken'];
	if (empty($myPlexAuthToken)) {
		$errorCode = 'Error: Could not parse Plex.tv XML to retrieve authentication code.';
	}
}
?>