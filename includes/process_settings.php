<?php
session_start();
require_once(dirname(__FILE__) . '/ConfigClass.php');

$config = new ConfigClass();
$config_file = '../config/config.php';
if (file_exists($config_file)) {
	$existing_config = $config::read($config_file);
}

if (!file_exists($_POST['plexWatchDb'])) {
	haveError("Specified DB file doesn't exist!");
}
$plexWatchDb = "\$plexWatch['plexWatchDb'] = '".$_POST['plexWatchDb']."';";

$dateFormat = "\$plexWatch['dateFormat'] = '".$_POST['dateFormat']."';";
$timeFormat = "\$plexWatch['timeFormat'] = '".$_POST['timeFormat']."';";

if (isset($_POST['userPicturesPath'])) {
	$userPicturesPath = "\$plexWatch['userPicturesPath'] = '".$_POST['userPicturesPath']."';";
} else {
	$userPicturesPath = "\$plexWatch['userPicturesPath'] = '';";
}

$pmsIp = "\$plexWatch['pmsIp'] = '".$_POST['pmsIp']."';";
$pmsHttpPort = "\$plexWatch['pmsHttpPort'] = '".$_POST['pmsHttpPort']."';";

$myPlexUser = "\$plexWatch['myPlexUser'] = '".$_POST['myPlexUser']."';";
if (isset($_POST['myPlexPass']) && $_POST['myPlexPass'] !== '') {
	$myPlexPass = "\$plexWatch['myPlexPass'] = '" . base64_encode($_POST['myPlexPass']) . "';";
} else {
	if ($existing_config) {
		$myPlexPass = "\$plexWatch['myPlexPass'] = '" . $existing_config['myPlexPass'] . "';";
	} else {
		$myPlexPass = "\$plexWatch['myPlexPass'] = '';";
	}
}

$globalHistoryGrping = "\$plexWatch['globalHistoryGrouping'] = 'no';";
if ($_POST['globalHistoryGrouping'] == "yes") {
	$globalHistoryGrping = "\$plexWatch['globalHistoryGrouping'] = 'yes';";
}

$userHistoryGrping = "\$plexWatch['userHistoryGrouping'] = 'no';";
if ($_POST['userHistoryGrouping'] == "yes") {
	$userHistoryGrping = "\$plexWatch['userHistoryGrouping'] = 'yes';";
}

$chartsGrping = "\$plexWatch['chartsGrouping'] = 'no';";
if ($_POST['chartsGrouping'] == "yes") {
	$chartsGrping = "\$plexWatch['chartsGrouping'] = 'yes';";
}

//combine all data into one variable
$data = $dateFormat . PHP_EOL . $timeFormat . PHP_EOL . $userPicturesPath . PHP_EOL . $pmsIp . PHP_EOL .
	$pmsHttpPort . PHP_EOL . $plexWatchDb . PHP_EOL . $myPlexUser . PHP_EOL .
	$myPlexPass . PHP_EOL . $globalHistoryGrping . PHP_EOL . $userHistoryGrping .
	PHP_EOL . $chartsGrping;

$func_file = dirname(dirname(__FILE__)) . '/includes/functions.php';

//write data to config.php file
$fp = fopen($config_file, "w+") or haveError("Cannot open file $config_file.");
fwrite($fp, "<?php" . PHP_EOL . PHP_EOL) or haveError("Cannot write to file $config_file.");
fwrite($fp, PHP_EOL . "require_once '$func_file';" . PHP_EOL) or haveError("Cannot write to file $config_file.");
fwrite($fp, $data) or haveError("Cannot write to file $config_file.");
fwrite($fp, PHP_EOL . PHP_EOL . "?>") or haveError("Cannot write to file $config_file.");
fclose($fp);

sleep(1);

//grab myPlex authentication token
require_once(dirname(__FILE__) . '/myplex.php');
$myPlexToken = "\$plexWatch['myPlexAuthToken'] = '".$myPlexAuthToken."';";

//include authentication code in saved data
$data = $dateFormat . PHP_EOL . $timeFormat . PHP_EOL . $userPicturesPath . PHP_EOL . $pmsIp . PHP_EOL .
	$pmsHttpPort . PHP_EOL . $plexWatchDb . PHP_EOL . $myPlexUser . PHP_EOL .
	$myPlexPass . PHP_EOL . $myPlexToken . PHP_EOL . $globalHistoryGrping .
	PHP_EOL . $userHistoryGrping . PHP_EOL . $chartsGrping;

//rewrite data to config.php
$fp = fopen($config_file, "w+") or haveError("Cannot open file $config_file.");
fwrite($fp, "<?php" . PHP_EOL . PHP_EOL) or haveError("Cannot write to file $config_file.");
fwrite($fp, PHP_EOL . "require_once '$func_file';" . PHP_EOL) or haveError("Cannot write to file $config_file.");
fwrite($fp, $data) or haveError("Cannot write to file $config_file.");
fwrite($fp, PHP_EOL . PHP_EOL . "?>") or haveError("Cannot write to file $config_file.");
fclose($fp);

function haveError($msg) {
	header('Location: ../settings.php?e=' . urlencode($msg));
	trigger_error($msg, E_USER_ERROR);
}

// Send the user back to the form
header('Location: ../settings.php?s=' . urlencode('Settings saved.'));
exit;
?>
