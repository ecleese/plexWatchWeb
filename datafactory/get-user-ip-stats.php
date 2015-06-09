<?php
date_default_timezone_set(@date_default_timezone_get());

$guisettingsFile = dirname(__FILE__) . '/../config/config.php';
if (file_exists($guisettingsFile)) {
	require_once($guisettingsFile);
} else {
	error_log('PlexWatchWeb :: Config file not found.');
	echo "Config file not found";
	exit;
}

if (!isset($_POST['user'])) {
	echo "User field is required.";
	trigger_error('PlexWatchWeb :: POST parameter "user" not found.', E_USER_ERROR);
}

$database = dbconnect();
$plexWatchDbTable = dbTable('user');
$query = "SELECT time, ip_address, platform, xml," .
		"COUNT(ip_address) as play_count, " .
		"strftime('%Y%m%d', datetime(time, 'unixepoch', 'localtime')) as date " .
	"FROM processed " .
	"WHERE user = :user " .
	"GROUP BY ip_address " .
	"ORDER BY time DESC";
$params = array(':user'=>$_POST['user']);
$results = getResults($database, $query, $params);
$nrow = array();
$i = 0;
while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
	if (empty($row['ip_address'])) {
		continue;
	}
	$nrow[$i][] = $row['time'];
	$nrow[$i][] = $row['ip_address'];
	$nrow[$i][] = $row['play_count'];
	$nrow[$i][] = $row['platform'];
	if (
			(substr_compare($row['ip_address'], '192.168.', 0, 8) == 0) ||
			(substr_compare($row['ip_address'], '10.', 0, 3) == 0) ||
			(
				(preg_match('/172\.\d\d\./', $row['ip_address']) == 1) &&
				(substr($row['ip_address'], 4, 2) > 15 &&
					substr($row['ip_address'], 4, 2) < 32)
			)
		) {
		// Private IP Address
		$nrow[$i][] = "n/a";
		$nrow[$i][] = "";
	} else {
		// Public, so attempt to geolocate
		$rowUrl = "http://www.geoplugin.net/xml.gp?ip=" . $row['ip_address'];
		$rowData = simplexml_load_file($rowUrl)
			or die ('<div class="alert alert-warning ">Cannot access '.
				'http://freegeoip.net</div>');
		if (empty($rowData->geoplugin_city)) {
			$nrow[$i][] = "n/a";
			$nrow[$i][] = "";
		} else {
			$nrow[$i][] = $rowData->geoplugin_city . ", " . $rowData->geoplugin_region;
			$nrow[$i][] = "https://maps.google.com/maps?q=" .
				urlencode($rowData->geoplugin_city . ", " . $rowData->geoplugin_region);
		}
	}
	$i++;
}

$graph_data = array('data'=>$nrow);
echo json_encode($graph_data, JSON_NUMERIC_CHECK);
?>