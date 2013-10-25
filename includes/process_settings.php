<?php
	session_start();
	$plexWatchVer = "\$plexWatch['version'] = '0.0.4.2dev';";
	
    $pmsIp = "\$plexWatch['pmsIp'] = '".$_POST['pmsIp']."';";	
	$pmsHttpPort = "\$plexWatch['pmsHttpPort'] = '".$_POST['pmsHttpPort']."';";
	$pmsHttpsPort = "\$plexWatch['pmsHttpsPort'] = '".$_POST['pmsHttpsPort']."';";
	
	
	if (!isset($_POST['https'])) {
		$https = "\$plexWatch['https'] = 'no';";
	}else if ($_POST['https'] == "yes") {
		$https = "\$plexWatch['https'] = '".$_POST['https']."';";
	}
	
	$plexWatchDb = "\$plexWatch['plexWatchDb'] = '".$_POST['plexWatchDb']."';";
	
	$myPlexUser = "\$plexWatch['myPlexUser'] = '".$_POST['myPlexUser']."';";	
	$myPlexPass = "\$plexWatch['myPlexPass'] = '".$_POST['myPlexPass']."';"; 
	
	
	if (!isset($_POST['globalHistoryGrouping'])) {
		$globalHistoryGrping = "\$plexWatch['globalHistoryGrouping'] = 'no';";
	}else if ($_POST['globalHistoryGrouping'] == "yes") {
		$globalHistoryGrping = "\$plexWatch['globalHistoryGrouping'] = 'yes';";
	}
	
	
	if (!isset($_POST['userHistoryGrouping'])){
		$userHistoryGrping = "\$plexWatch['userHistoryGrouping'] = 'no';";
	}else if ($_POST['userHistoryGrouping'] == "yes") {
		$userHistoryGrping = "\$plexWatch['userHistoryGrouping'] = 'yes';";
	}
	
	
	if (!isset($_POST['chartsGrouping'])){
		$chartsGrping = "\$plexWatch['chartsGrouping'] = 'no';";
	}else if ($_POST['chartsGrouping'] == "yes") {
		$chartsGrping = "\$plexWatch['chartsGrouping'] = 'yes';";
	}
	
	//combine all data into one variable
	$data = "$plexWatchVer\r$pmsIp\r$pmsHttpPort\r$pmsHttpsPort\r$https\r$plexWatchDb\r$myPlexUser\r$myPlexPass\r$globalHistoryGrping\r$userHistoryGrping\r$chartsGrping";
	
	$file = "../config/config.php";
        $func_file = dirname(dirname(__FILE__)) . '/includes/functions.php';
	
	//write data to config.php file
	$fp = fopen($file, "w+") or die("Cannot open file $file.");
	fwrite($fp, "<?php\r\r") or die("Cannot write to file $file.");
	fwrite($fp, $data) or die("Cannot write to file $file.");
	fwrite($fp, "\nrequire_once '$func_file';\n") or die("Cannot write to file $file.");
	fwrite($fp, "\r\r?>") or die("Cannot write to file $file.");
	fclose($fp);
	
	sleep(1);
	
	//grab myPlex authentication token
	require_once(dirname(__FILE__) . '/myplex.php');
	$myPlexToken = "\$plexWatch['myPlexAuthToken'] = '".$myPlexAuthToken."';";
	
	//include authentication code in saved data
	$data = "$plexWatchVer\r$pmsIp\r$pmsHttpPort\r$pmsHttpsPort\r$https\r$plexWatchDb\r$myPlexUser\r$myPlexPass\r$myPlexToken\r$globalHistoryGrping\r$userHistoryGrping\r$chartsGrping";
	
	//rewrite data to config.php
	$fp = fopen($file, "w+") or die("Cannot open file $file.");
	fwrite($fp, "<?php\r\r") or die("Cannot write to file $file.");
	fwrite($fp, $data) or die("Cannot write to file $file.");
	fwrite($fp, "\nrequire_once '$func_file';\n") or die("Cannot write to file $file.");
	fwrite($fp, "\r\r?>") or die("Cannot write to file $file.");
	fclose($fp);

	// check if an error was found - if there was, send the user back to the form  
	if (!empty($httpCode401)) {  
		header('Location: ../settings.php?e='.urlencode($httpCode401)); exit;  
	} 

	// send the user back to the form  
	header("Location: ../settings.php?s=".urlencode("Settings saved.")); exit;  
	
?>
