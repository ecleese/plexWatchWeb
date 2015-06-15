<?php
require_once(dirname(__FILE__) . '/functions.php');

$imgReq = filter_input(INPUT_GET, 'img', FILTER_SANITIZE_URL);
if (!isset($imgReq) || $imgReq === false) {
	$error_msg = 'No image to retrieve specified.';
	echo '<p>' . $error_msg . '</p>';
	trigger_error($error_msg, E_USER_ERROR);
}
$path = '/photo/:/transcode?url=http://127.0.0.1:' . $settings->getPmsPort() .
	$imgReq;

/**********************
 * FIXME: This should use getPMSData(), but we need the content-type
 */
if ($settings->getPlexAuthToken()) {
	$myPlexAuthToken = '&X-Plex-Token=' . $settings->getPlexAuthToken();
} else {
	$myPlexAuthToken = '';
}
$url = $settings->getPmsURL() . $path . $myPlexAuthToken;
$curlHandle = curl_init($url);
curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
$img = curl_exec($curlHandle);
if ($img === false || curl_getinfo($curlHandle, CURLINFO_HTTP_CODE) >= 400) {
	curl_close($curlHandle);
	$msg = 'Failed to retrieve "' . $url . '"';
	echo $msg;
	trigger_error($msg, E_USER_ERROR);
	return false;
}
$imgType = curl_getinfo($curlHandle, CURLINFO_CONTENT_TYPE);
curl_close($curlHandle);
// ******************

if ($img === false) {
	$error_msg = 'Failed to retrieve "' . $path . '"';
	echo '<p>' . $error_msg . '</p>';
	trigger_error($error_msg, E_USER_ERROR);
}
header('Content-Type: ' . $imgType, false);
echo $img;
?>