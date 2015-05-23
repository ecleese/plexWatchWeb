<?php
date_default_timezone_set(@date_default_timezone_get());

$guisettingsFile = '../config/config.php';
if (file_exists($guisettingsFile)) {
	require_once($guisettingsFile);
} else {
	error_log('PlexWatchWeb :: Config file not found.');
	echo "Config file not found";
	exit;
}

// Connects to your Database
$db = dbconnect();

if (isset($_POST['user'])) {
	$user = $db->escapeString($_POST['user']);
} else {
	error_log('PlexWatchWeb :: POST parameter "user" not found.');
	echo "user field is required.";
	exit;
}

$plexWatchDbTable = dbTable('user');
$userIpAddressesQuery = $db->query("SELECT time,ip_address,platform,xml, COUNT(ip_address) as play_count, strftime('%Y%m%d', datetime(time, 'unixepoch', 'localtime')) as date FROM processed WHERE user = '$user' GROUP BY ip_address ORDER BY time DESC");
$nrow = Array();
$i = 0;
while ($userIpAddresses = $userIpAddressesQuery->fetchArray()) {
	if (!empty($userIpAddresses['ip_address'])) {
		if (strpos($userIpAddresses['ip_address'], "192.168" ) === 0) {
		} else if (strpos($userIpAddresses['ip_address'], "10." ) === 0) {
		} else if (strpos($userIpAddresses['ip_address'], "172.16" ) === 0) { //need a solution to check for 17-31
		} else {
			$userIpAddressesUrl = "http://www.geoplugin.net/xml.gp?ip=".$userIpAddresses['ip_address']."";
			$userIpAddressesData = simplexml_load_file($userIpAddressesUrl) or die ("<div class=\"alert alert-warning \">Cannot access http://www.geoplugin.net.</div>");
			$nrow[$i][] = $userIpAddresses['time'];
			$nrow[$i][] = $userIpAddresses['ip_address'];
			$nrow[$i][] = $userIpAddresses['play_count'];
			$nrow[$i][] = $userIpAddresses['platform'];

			if (empty($userIpAddressesData->geoplugin_city)) {
				$nrow[$i][] = "n/a";
				$nrow[$i][] = "";
			} else {
				$nrow[$i][] = $userIpAddressesData->geoplugin_city.", ".$userIpAddressesData->geoplugin_region;
				$nrow[$i][] = "https://maps.google.com/maps?q=".urlencode($userIpAddressesData->geoplugin_city.", ".$userIpAddressesData->geoplugin_region);
			}
			$i++;
		}
	}
}

$graph_data = array('data'=>$nrow);

echo json_encode($graph_data, JSON_NUMERIC_CHECK);
?>