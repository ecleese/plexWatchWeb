<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>plexWatch</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- css styles -->
    <link href="css/plexwatch.css" rel="stylesheet">
	<link href="css/plexwatch-tables.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet" >
	<link href="css/xcharts.css" rel="stylesheet" >
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }
    </style>

    <!-- touch icons -->
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/icon_iphone.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/icon_ipad.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/icon_iphone@2x.png">
	<link rel="apple-touch-icon" sizes="144x144" href="images/icon_ipad@2x.png">

  </head>

  <body>
  
  <?php include ("header.php"); ?>

	<?php
	
	date_default_timezone_set(@date_default_timezone_get());
	
	$guisettingsFile = "config/config.php";
	
	if (file_exists($guisettingsFile)) { 
		require_once(dirname(__FILE__) . '/config/config.php');
	}else{
		header("Location: settings.php");
	}

	
	if ($plexWatch['https'] == "yes") {
		$plexWatchPmsUrl = "https://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpsPort']."";
	}else if ($plexWatch['https'] == "no") {
		$plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";
	}else{
	}

	function formatBytes($bytes, $precision = 2) { 
		$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
		$bytes = max($bytes, 0); 
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($units) - 1); 
		$bytes /= (1 << (10 * $pow)); 
		return round($bytes, $precision) . ' ' . $units[$pow]; 
	} 		

	$user = $_GET['user'];

	$db = dbconnect();
	
	if ($plexWatch['userHistoryGrouping'] == "yes") {
		$plexWatchDbTable = "grouped";
	}else if ($plexWatch['userHistoryGrouping'] == "no") {
		$plexWatchDbTable = "processed";
	}
	
	$numRows = $db->querySingle("SELECT COUNT(*) as count FROM ".$plexWatchDbTable." ");
	$userInfo = $db->query("SELECT user,xml FROM ".$plexWatchDbTable." WHERE user = '$user' ORDER BY time DESC LIMIT 1") or die ("Failed to access plexWatch database. Please check your settings.");
	
	$userStatsDailyCount = $db->querySingle("SELECT COUNT(*) FROM ".$plexWatchDbTable." WHERE datetime(stopped, 'unixepoch', 'localtime') >= date('now', 'localtime') AND user='$user' ");
	
	

			        
	$userPlays = $db->query("SELECT strftime('%Y-%m-%d', datetime(time, 'unixepoch', 'localtime')) as date, COUNT(title) as count FROM $plexWatchDbTable WHERE user = '$user' GROUP BY date ORDER BY date ASC;") or die ("Failed to access plexWatch database. Please check your settings.");
					$userPlaysNum = 0;
					$userPlayFinal = '';
					while ($userPlay = $userPlays->fetchArray()) {
						$userPlaysNum++;
						$userPlayDate[$userPlaysNum] = $userPlay['date'];
						$userPlayCount[$userPlaysNum] = $userPlay['count'];
						$userPlayTotal = "{ \"x\": \"".$userPlayDate[$userPlaysNum]."\", \"y\": ".$userPlayCount[$userPlaysNum]." }, ";
						$userPlayFinal .= $userPlayTotal;
						//echo $userPlayFinal;
					}		        
	
	
	

	
			        
	
	$userStatsDailyTimeFetch = $db->query("SELECT time,stopped,paused_counter FROM ".$plexWatchDbTable." WHERE datetime(stopped, 'unixepoch', 'localtime') >= date('now', 'localtime') AND user='$user' ");
	$userStatsDailyTimeViewedTime = 0;
	while ($userStatsDailyTimeRow = $userStatsDailyTimeFetch->fetchArray()) {
		$userStatsDailyTimeToTimeRow = strtotime(date("m/d/Y g:i a",$userStatsDailyTimeRow['stopped']));
		$userStatsDailyTimeFromTimeRow = strtotime(date("m/d/Y g:i a",$userStatsDailyTimeRow['time']));
		$userStatsDailyTimePausedTimeRow = round(abs($userStatsDailyTimeRow['paused_counter']) ,1);			
		$userStatsDailyTimeViewedTimeRow = round(abs($userStatsDailyTimeToTimeRow - $userStatsDailyTimeFromTimeRow - $userStatsDailyTimePausedTimeRow) ,0);
		$userStatsDailyTimeViewedTimeRowLength = strlen($userStatsDailyTimeViewedTimeRow);

		$userStatsDailyTimeViewedTime += $userStatsDailyTimeViewedTimeRow;
		$userStatsDailyTimeViewedTimeDays = floor($userStatsDailyTimeViewedTime / 86400);
		$userStatsDailyTimeViewedTimeHours = floor(($userStatsDailyTimeViewedTime % 86400 ) / 3600);
		$userStatsDailyTimeViewedTimeMinutes = floor(($userStatsDailyTimeViewedTime % 3600 ) / 60);
	}								
	

	$userStatsWeeklyCount = $db->querySingle("SELECT COUNT(*) FROM ".$plexWatchDbTable." WHERE datetime(stopped, 'unixepoch') >= datetime('now', '-7 days', 'localtime') AND user='$user' ");
	
	$userStatsWeeklyTimeFetch = $db->query("SELECT time,stopped,paused_counter FROM ".$plexWatchDbTable." WHERE datetime(stopped, 'unixepoch', 'localtime') >= datetime('now', '-7 days', 'localtime') AND user='$user' ");
	$userStatsWeeklyTimeViewedTime = 0;
	while ($userStatsWeeklyTimeRow = $userStatsWeeklyTimeFetch->fetchArray()) {
		$userStatsWeeklyTimeToTimeRow = strtotime(date("m/d/Y g:i a",$userStatsWeeklyTimeRow['stopped']));
		$userStatsWeeklyTimeFromTimeRow = strtotime(date("m/d/Y g:i a",$userStatsWeeklyTimeRow['time']));
		$userStatsWeeklyTimePausedTimeRow = round(abs($userStatsWeeklyTimeRow['paused_counter']) ,1);			
		$userStatsWeeklyTimeViewedTimeRow = round(abs($userStatsWeeklyTimeToTimeRow - $userStatsWeeklyTimeFromTimeRow - $userStatsWeeklyTimePausedTimeRow) ,0);
		$userStatsWeeklyTimeViewedTimeRowLength = strlen($userStatsWeeklyTimeViewedTimeRow);
		
		$userStatsWeeklyTimeViewedTime += $userStatsWeeklyTimeViewedTimeRow;
		$userStatsWeeklyTimeViewedTimeDays = floor($userStatsWeeklyTimeViewedTime / 86400);
		$userStatsWeeklyTimeViewedTimeHours = floor(($userStatsWeeklyTimeViewedTime % 86400 ) / 3600);
		$userStatsWeeklyTimeViewedTimeMinutes = floor(($userStatsWeeklyTimeViewedTime % 3600 ) / 60);
	}

	
	$userStatsMonthlyCount = $db->querySingle("SELECT COUNT(*) FROM ".$plexWatchDbTable." WHERE datetime(stopped, 'unixepoch', 'localtime') >= datetime('now', '-30 days', 'localtime') AND user='$user' ");
	
	$userStatsMonthlyTimeFetch = $db->query("SELECT time,stopped,paused_counter FROM ".$plexWatchDbTable." WHERE datetime(stopped, 'unixepoch', 'localtime') >= datetime('now', '-30 days', 'localtime') AND user='$user' ");
	$userStatsMonthlyTimeViewedTime = 0;
	while ($userStatsMonthlyTimeRow = $userStatsMonthlyTimeFetch->fetchArray()) {
		$userStatsMonthlyTimeToTimeRow = strtotime(date("m/d/Y g:i a",$userStatsMonthlyTimeRow['stopped']));
		$userStatsMonthlyTimeFromTimeRow = strtotime(date("m/d/Y g:i a",$userStatsMonthlyTimeRow['time']));
		$userStatsMonthlyTimePausedTimeRow = round(abs($userStatsMonthlyTimeRow['paused_counter']) ,1);			
		$userStatsMonthlyTimeViewedTimeRow = round(abs($userStatsMonthlyTimeToTimeRow - $userStatsMonthlyTimeFromTimeRow - $userStatsMonthlyTimePausedTimeRow) ,0);
		$userStatsMonthlyTimeViewedTimeRowLength = strlen($userStatsMonthlyTimeViewedTimeRow);
		
		$userStatsMonthlyTimeViewedTime += $userStatsMonthlyTimeViewedTimeRow;
		$userStatsMonthlyTimeViewedTimeDays = floor($userStatsMonthlyTimeViewedTime / 86400);
		$userStatsMonthlyTimeViewedTimeHours = floor(($userStatsMonthlyTimeViewedTime % 86400 ) / 3600);
		$userStatsMonthlyTimeViewedTimeMinutes = floor(($userStatsMonthlyTimeViewedTime % 3600 ) / 60);
	}
	
	
	$userStatsAlltimeCount = $db->querySingle("SELECT COUNT(*) FROM ".$plexWatchDbTable." WHERE user='$user' ");
	
	$userStatsAlltimeTimeFetch = $db->query("SELECT time,stopped,paused_counter,xml FROM ".$plexWatchDbTable." WHERE user='$user' ");
	$userStatsAlltimeTimeViewedTime = 0;
	while ($userStatsAlltimeTimeRow = $userStatsAlltimeTimeFetch->fetchArray()) {
		$userStatsAlltimeTimeToTimeRow = strtotime(date("m/d/Y g:i a",$userStatsAlltimeTimeRow['stopped']));
		$userStatsAlltimeTimeFromTimeRow = strtotime(date("m/d/Y g:i a",$userStatsAlltimeTimeRow['time']));
		$userStatsAlltimeTimePausedTimeRow = round(abs($userStatsAlltimeTimeRow['paused_counter']) ,1);			
		$userStatsAlltimeTimeViewedTimeRow = round(abs($userStatsAlltimeTimeToTimeRow - $userStatsAlltimeTimeFromTimeRow - $userStatsAlltimeTimePausedTimeRow) ,0);
		$userStatsAlltimeTimeViewedTimeRowLength = strlen($userStatsAlltimeTimeViewedTimeRow);
		
		$userStatsAlltimeTimeViewedTime += $userStatsAlltimeTimeViewedTimeRow;
		$userStatsAlltimeTimeViewedTimeDays = floor($userStatsAlltimeTimeViewedTime / 86400);
		$userStatsAlltimeTimeViewedTimeHours = floor(($userStatsAlltimeTimeViewedTime % 86400 ) / 3600);
		$userStatsAlltimeTimeViewedTimeMinutes = floor(($userStatsAlltimeTimeViewedTime % 3600 ) / 60);
		
	}
	
	
	$rowCountData = 0;
	while ($row = $userStatsAlltimeTimeFetch->fetchArray()) {
	$rowCountData++;
	$request_url = $row['xml'];
	$xmlfield = simplexml_load_string($request_url); 
	$duration = $xmlfield['duration'];
	$viewOffset = $xmlfield['viewOffset'];
	$percentComplete = ($duration == 0 ? 0 : sprintf("%2d", ($viewOffset / $duration) * 100));
	if ($percentComplete >= 90) {	
	$percentComplete = 100;
	}
	$size = $xmlfield->Media->Part['size'];
	$dataTransferred = ($percentComplete / 100 * ($size));
	$totalDataTransferred += $dataTransferred;	    
	}
	
	
	if ($plexWatch['userHistoryGrouping'] == "yes") {
		$results = $db->query("SELECT title, user, platform, time, stopped, ip_address, xml, paused_counter FROM processed WHERE user = '$user' AND stopped IS NULL UNION ALL SELECT title, user, platform, time, stopped, ip_address, xml, paused_counter FROM ".$plexWatchDbTable." WHERE user = '$user' ORDER BY time DESC") or die ("Failed to access plexWatch database. Please check your settings.");
	}else if ($plexWatch['userHistoryGrouping'] == "no") {
		$results = $db->query("SELECT title, user, platform, time, stopped, ip_address, xml, paused_counter FROM ".$plexWatchDbTable." WHERE user = '$user' ORDER BY time DESC") or die ("Failed to access plexWatch database. Please check your settings.");
	}
	
	echo "<div class='container-fluid'>";
		echo "<div class='row-fluid'>";
			echo "<div class='span12'>";
				echo "<div class='user-info-wrapper'>";
					while ($userInfoResults= $userInfo->fetchArray()) {
						$userInfoXml = $userInfoResults['xml'];
						$userInfoXmlField = simplexml_load_string($userInfoXml); 
						if (empty($userInfoXmlField->User['thumb'])) {
							echo "<div class='user-info-poster-face'><img src='images/gravatar-default-80x80.png'></></div>";
						}else if (strstr($userInfoXmlField->User['thumb'], "?d=404")) {
							echo "<div class='user-info-poster-face'><img src='images/gravatar-default-80x80.png'></></div>";
						}else{
							echo "<div class='user-info-poster-face'><img src='".$userInfoXmlField->User['thumb']."'></></div>";
						}
					}
					
					echo "<div class='user-info-username'>".FriendlyName($user)."</div>";
					echo "<div class='user-info-nav'>";
						echo "<ul class='user-info-nav'>";
							echo "<li class='active'><a href='#profile' data-toggle='tab'>Profile</a></li>";
							echo "<li><a href='#userAddresses' data-toggle='tab'>IP Addresses</a></li>";
							echo "<li><a href='#userHistory' data-toggle='tab'>History</a></li>";
							
						echo "</ul>";
					echo"</div>";			
				echo"</div>";		
			echo"</div>";
					
					
		echo "</div>";
	echo "</div>";
	
	echo "<div class='tab-content'>";
	
		echo "<div class='tab-pane active' id='profile'>";
		
			echo "<div class='container-fluid'>";	
				echo "<div class='row-fluid'>";
					echo "<div class='span12'>";
						echo "<div class='wellbg'>";
							echo "<div class='wellheader'>";
								echo "<div class='dashboard-wellheader'>";
									echo"<h3>Global Stats</h3>";
								echo"</div>";
							echo"</div>";

							echo "<div class='user-overview-stats-wrapper'>";
								echo"<ul>";
								
									echo "<div class='user-overview-stats-instance'>";
										echo "<li>";
										echo "<div class='user-overview-stats-instance-text'>";
											echo "<h4>Today</h4>";
											if ($userStatsDailyCount == 1) {
												echo "<h3>".$userStatsDailyCount."</h3><p>play</p>";
											}else{
												echo "<h3>".$userStatsDailyCount."</h3><p>plays</p>";
											}
											
											if (empty($userStatsDailyTimeViewedTimeRowLength)){
												echo "<h1> / </h1><h3>0</h3><p> mins</p>";
											}else if ($userStatsDailyTimeViewedTimeRowLength == 10) {
												echo "";
											}else if (empty($userStatsDailyTimeViewedTimeMinutes) && empty($userStatsDailyTimeViewedTimeHours) && empty($userStatsDailyTimeViewedTimeDays)) {
												echo "<h1> / </h1><h3>0</h3><p> mins</p>";	
											}else if ($userStatsDailyTimeViewedTimeDays == 0 && $userStatsDailyTimeViewedTimeHours == 0 && $userStatsDailyTimeViewedTimeMinutes == 1) {
												echo "<h1> / </h1><h3>".$userStatsDailyTimeViewedTimeMinutes."</h3> <p>min</p>";
											}else if ($userStatsDailyTimeViewedTimeDays == 0 && $userStatsDailyTimeViewedTimeHours == 0) {
												echo "<h1> / </h1><h3>".$userStatsDailyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsDailyTimeViewedTimeDays == 0 && $userStatsDailyTimeViewedTimeHours == 1) {
												echo "<h1> / </h1><h3>".$userStatsDailyTimeViewedTimeHours."</h3> <p>hr </p><h3>".$userStatsDailyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsDailyTimeViewedTimeDays == 0 && $userStatsDailyTimeViewedTimeMinutes == 1) {
												echo "<h1> / </h1><h3>".$userStatsDailyTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsDailyTimeViewedTimeMinutes."</h3> <p>min</p>";
											}else if ($userStatsDailyTimeViewedTimeDays == 0) {
												echo "<h1> / </h1><h3>".$userStatsDailyTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsDailyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsDailyTimeViewedTimeDays == 1) {
												echo "<h1> / </h1><h3>".$userStatsDailyTimeViewedTimeDays."</h3> <p>day </p><h3>".$userStatsDailyTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsDailyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsDailyTimeViewedTimeDays == 1 && $userStatsDailyTimeViewedTimeHours == 1) {
												echo "<h1> / </h1><h3>".$userStatsDailyTimeViewedTimeDays."</h3> <p>day </p><h3>".$userStatsDailyTimeViewedTimeHours."</h3> <p>hr </p><h3>".$userStatsDailyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsDailyTimeViewedTimeDays == 1 && $userStatsDailyTimeViewedTimeHours == 1 && $userStatsDailyTimeViewedTimeMinutes == 1) {
												echo "<h1> / </h1><h3>".$userStatsDailyTimeViewedTimeDays."</h3> <p>day </p><h3>".$userStatsDailyTimeViewedTimeHours."</h3> <p>hr </p><h3>".$userStatsDailyTimeViewedTimeMinutes."</h3> <p>min</p>";
											}else{
												echo "<h1> / </h1><h3>".$userStatsDailyTimeViewedTimeDays."</h3> <p>days </p><h3>".$userStatsDailyTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsDailyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}
										echo"</div>";
										echo "</li>";
									echo"</div>";	
								
									echo "<div class='user-overview-stats-instance'>";
										echo "<li>";
										echo "<div class='user-overview-stats-instance-text'>";
										echo "<h4>Last week</h4>";
											if ($userStatsWeeklyCount == 1) {
												echo "<h3>".$userStatsWeeklyCount."</h3><p>play</p>";
											}else{
												echo "<h3>".$userStatsWeeklyCount."</h3><p>plays</p>";
											}
											
											if (empty($userStatsWeeklyTimeViewedTimeRowLength)){
												echo "<h1> / </h1><h3>0</h3><p> mins</p>";
											}else if ($userStatsWeeklyTimeViewedTimeRowLength == 10) {
												echo "";
											}else if (empty($userStatsWeeklyTimeViewedTimeMinutes) && empty($userStatsWeeklyTimeViewedTimeHours) && empty($userStatsWeeklyTimeViewedTimeDays)) {
												echo "<h1> / </h1><h3>0</h3><p> mins</p>";	
											}else if ($userStatsWeeklyTimeViewedTimeDays == 0 && $userStatsWeeklyTimeViewedTimeHours == 0 && $userStatsWeeklyTimeViewedTimeMinutes == 1) {
												echo "<h1> / </h1><h3>".$userStatsWeeklyTimeViewedTimeMinutes."</h3> <p>min</p>";
											}else if ($userStatsWeeklyTimeViewedTimeDays == 0 && $userStatsWeeklyTimeViewedTimeHours == 0) {
												echo "<h1> / </h1><h3>".$userStatsWeeklyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsWeeklyTimeViewedTimeDays == 0 && $userStatsWeeklyTimeViewedTimeHours == 1) {
												echo "<h1> / </h1><h3>".$userStatsWeeklyTimeViewedTimeHours."</h3> <p>hr </p><h3>".$userStatsWeeklyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsWeeklyTimeViewedTimeDays == 0 && $userStatsWeeklyTimeViewedTimeMinutes == 1) {
												echo "<h1> / </h1><h3>".$userStatsWeeklyTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsWeeklyTimeViewedTimeMinutes."</h3> <p>min</p>";
											}else if ($userStatsWeeklyTimeViewedTimeDays == 0) {
												echo "<h1> / </h1><h3>".$userStatsWeeklyTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsWeeklyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsWeeklyTimeViewedTimeDays == 1) {
												echo "<h1> / </h1><h3>".$userStatsWeeklyTimeViewedTimeDays."</h3> <p>day </p><h3>".$userStatsWeeklyTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsWeeklyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsWeeklyTimeViewedTimeDays == 1 && $userStatsWeeklyTimeViewedTimeHours == 1) {
												echo "<h1> / </h1><h3>".$userStatsWeeklyTimeViewedTimeDays."</h3> <p>day </p><h3>".$userStatsWeeklyTimeViewedTimeHours."</h3> <p>hr </p><h3>".$userStatsWeeklyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsWeeklyTimeViewedTimeDays == 1 && $userStatsWeeklyTimeViewedTimeHours == 1 && $userStatsWeeklyTimeViewedTimeMinutes == 1) {
												echo "<h1> / </h1><h3>".$userStatsWeeklyTimeViewedTimeDays."</h3> <p>day </p><h3>".$userStatsWeeklyTimeViewedTimeHours."</h3> <p>hr </p><h3>".$userStatsWeeklyTimeViewedTimeMinutes."</h3> <p>min</p>";
											}else{
												echo "<h1> / </h1><h3>".$userStatsWeeklyTimeViewedTimeDays."</h3> <p>days </p><h3>".$userStatsWeeklyTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsWeeklyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}
										echo"</div>";
										echo "</li>";
									echo"</div>";
								
									echo "<div class='user-overview-stats-instance'>";
										echo "<li>";
										echo "<div class='user-overview-stats-instance-text'>";
										echo "<h4>Last month</h4>";
											if ($userStatsMonthlyCount == 1) {
												echo "<h3>".$userStatsMonthlyCount."</h3><p>play</p>";
											}else{
												echo "<h3>".$userStatsMonthlyCount."</h3><p>plays</p>";
											}
											
											if (empty($userStatsMonthlyTimeViewedTimeRowLength)){
												echo "<h1> / </h1><h3>0</h3><p> mins</p>";
											}else if ($userStatsMonthlyTimeViewedTimeRowLength == 10) {
												echo "";
											}else if (empty($userStatsMonthlyTimeViewedTimeMinutes) && empty($userStatsMonthlyTimeViewedTimeHours) && empty($userStatsMonthlyTimeViewedTimeDays)) {
												echo "<h1> / </h1><h3>0</h3><p> mins</p>";	
											}else if ($userStatsMonthlyTimeViewedTimeDays == 0 && $userStatsMonthlyTimeViewedTimeHours == 0 && $userStatsMonthlyTimeViewedTimeMinutes == 1) {
												echo "<h1> / </h1><h3>".$userStatsMonthlyTimeViewedTimeMinutes."</h3> <p>min</p>";
											}else if ($userStatsMonthlyTimeViewedTimeDays == 0 && $userStatsMonthlyTimeViewedTimeHours == 0) {
												echo "<h1> / </h1><h3>".$userStatsMonthlyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsMonthlyTimeViewedTimeDays == 0 && $userStatsMonthlyTimeViewedTimeHours == 1) {
												echo "<h1> / </h1><h3>".$userStatsMonthlyTimeViewedTimeHours."</h3> <p>hr </p><h3>".$userStatsMonthlyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsMonthlyTimeViewedTimeDays == 0 && $userStatsMonthlyTimeViewedTimeMinutes == 1) {
												echo "<h1> / </h1><h3>".$userStatsMonthlyTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsMonthlyTimeViewedTimeMinutes."</h3> <p>min</p>";
											}else if ($userStatsMonthlyTimeViewedTimeDays == 0) {
												echo "<h1> / </h1><h3>".$userStatsMonthlyTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsMonthlyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsMonthlyTimeViewedTimeDays == 1) {
												echo "<h1> / </h1><h3>".$userStatsMonthlyTimeViewedTimeDays."</h3> <p>day </p><h3>".$userStatsMonthlyTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsMonthlyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsMonthlyTimeViewedTimeDays == 1 && $userStatsMonthlyTimeViewedTimeHours == 1) {
												echo "<h1> / </h1><h3>".$userStatsMonthlyTimeViewedTimeDays."</h3> <p>day </p><h3>".$userStatsMonthlyTimeViewedTimeHours."</h3> <p>hr </p><h3>".$userStatsMonthlyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsMonthlyTimeViewedTimeDays == 1 && $userStatsMonthlyTimeViewedTimeHours == 1 && $userStatsMonthlyTimeViewedTimeMinutes == 1) {
												echo "<h1> / </h1><h3>".$userStatsMonthlyTimeViewedTimeDays."</h3> <p>day </p><h3>".$userStatsMonthlyTimeViewedTimeHours."</h3> <p>hr </p><h3>".$userStatsMonthlyTimeViewedTimeMinutes."</h3> <p>min</p>";
											}else{
												echo "<h1> / </h1><h3>".$userStatsMonthlyTimeViewedTimeDays."</h3> <p>days </p><h3>".$userStatsMonthlyTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsMonthlyTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}
										echo"</div>";
										echo "</li>";
									echo"</div>";
								
									echo "<div class='user-overview-stats-instance'>";
										echo "<li>";
										echo "<div class='user-overview-stats-instance-text'>";
										echo "<h4>All Time</h4>";
											if ($userStatsAlltimeCount == 1) {
												echo "<h3>".$userStatsAlltimeCount."</h3><p>play</p>";
											}else{
												echo "<h3>".$userStatsAlltimeCount."</h3><p>plays</p>";
											}
											
											if (empty($userStatsAlltimeTimeViewedTimeRowLength)){
												echo "<h1> / </h1><h3>0</h3><p> mins</p>";
											}else if ($userStatsAlltimeTimeViewedTimeRowLength == 10) {
												echo "";
											}else if (empty($userStatsAlltimeTimeViewedTimeMinutes) && empty($userStatsAlltimeTimeViewedTimeHours) && empty($userStatsAlltimeTimeViewedTimeDays)) {
												echo "<h1> / </h1><h3>0</h3><p> mins</p>";	
											}else if ($userStatsAlltimeTimeViewedTimeDays == 0 && $userStatsAlltimeTimeViewedTimeHours == 0 && $userStatsAlltimeTimeViewedTimeMinutes == 1) {
												echo "<h1> / </h1><h3>".$userStatsAlltimeTimeViewedTimeMinutes."</h3> <p>min</p>";
											}else if ($userStatsAlltimeTimeViewedTimeDays == 0 && $userStatsAlltimeTimeViewedTimeHours == 0) {
												echo "<h1> / </h1><h3>".$userStatsAlltimeTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsAlltimeTimeViewedTimeDays == 0 && $userStatsAlltimeTimeViewedTimeHours == 1) {
												echo "<h1> / </h1><h3>".$userStatsAlltimeTimeViewedTimeHours."</h3> <p>hr </p><h3>".$userStatsAlltimeTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsAlltimeTimeViewedTimeDays == 0 && $userStatsAlltimeTimeViewedTimeMinutes == 1) {
												echo "<h1> / </h1><h3>".$userStatsAlltimeTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsAlltimeTimeViewedTimeMinutes."</h3> <p>min</p>";
											}else if ($userStatsAlltimeTimeViewedTimeDays == 0) {
												echo "<h1> / </h1><h3>".$userStatsAlltimeTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsAlltimeTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsAlltimeTimeViewedTimeDays == 1) {
												echo "<h1> / </h1><h3>".$userStatsAlltimeTimeViewedTimeDays."</h3> <p>day </p><h3>".$userStatsAlltimeTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsAlltimeTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsAlltimeTimeViewedTimeDays == 1 && $userStatsAlltimeTimeViewedTimeHours == 1) {
												echo "<h1> / </h1><h3>".$userStatsAlltimeTimeViewedTimeDays."</h3> <p>day </p><h3>".$userStatsAlltimeTimeViewedTimeHours."</h3> <p>hr </p><h3>".$userStatsAlltimeTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}else if ($userStatsAlltimeTimeViewedTimeDays == 1 && $userStatsAlltimeTimeViewedTimeHours == 1 && $userStatsAlltimeTimeViewedTimeMinutes == 1) {
												echo "<h1> / </h1><h3>".$userStatsAlltimeTimeViewedTimeDays."</h3> <p>day </p><h3>".$userStatsAlltimeTimeViewedTimeHours."</h3> <p>hr </p><h3>".$userStatsAlltimeTimeViewedTimeMinutes."</h3> <p>min</p>";
											}else{
												echo "<h1> / </h1><h3>".$userStatsAlltimeTimeViewedTimeDays."</h3> <p>days </p><h3>".$userStatsAlltimeTimeViewedTimeHours."</h3> <p>hrs </p><h3>".$userStatsAlltimeTimeViewedTimeMinutes."</h3> <p>mins</p>";
											}
										echo "<li>";
										echo "<div class='user-overview-stats-instance-text'>";
										echo "<h4>Total Data Transferred</h4>";
										echo "<h3>".formatBytes($totalDataTransferred)."</h3>";
										echo"</div>";
										echo "</li>";
									echo"</div>";
								echo"</ul>";
							echo"</div>";
							
							
						echo "</div>";
					echo "</div>";	
					
				echo "</div>";		
			echo "</div>";	
			
			echo "<div class='container-fluid'>";
				echo "<div class='row-fluid'>";
						echo "<div class='span12'>";
							echo "<div class='wellbg'>";
								echo "<div class='wellheader'>";
									echo "<div class='dashboard-wellheader'>";
										echo"<h3>Platform Stats</h3>";
									echo"</div>";
								echo"</div>";
								
								$platformResults = $db->query ("SELECT xml,platform, COUNT(platform) as platform_count FROM processed WHERE user = '$user' GROUP BY platform ORDER BY platform ASC") or die ("Failed to access plexWatch database. Please check your settings.");
								 
								
								$platformImage = 0;
								while ($platformResultsRow = $platformResults->fetchArray()) {
								
								$platformXml = $platformResultsRow['xml'];
								$platformXmlField = simplexml_load_string($platformXml);
								
									
									if(strstr($platformXmlField->Player['platform'], 'Roku')) {
										$platformImage = "images/platforms/roku.png";
									}else if(strstr($platformXmlField->Player['platform'], 'Apple TV')) {
										$platformImage = "images/platforms/appletv.png";
									}else if(strstr($platformXmlField->Player['platform'], 'Firefox')) {
										$platformImage = "images/platforms/firefox.png";
									}else if(strstr($platformXmlField->Player['platform'], 'Chromecast')) {
										$platformImage = "images/platforms/chromecast.png";
									}else if(strstr($platformXmlField->Player['platform'], 'Chrome')) {
										$platformImage = "images/platforms/chrome.png";
									}else if(strstr($platformXmlField->Player['platform'], 'Android')) {
										$platformImage = "images/platforms/android.png";
									}else if(strstr($platformXmlField->Player['platform'], 'Nexus')) {
										$platformImage = "images/platforms/android.png";
									}else if(strstr($platformXmlField->Player['platform'], 'iPad')) {
										$platformImage = "images/platforms/ios.png";
									}else if(strstr($platformXmlField->Player['platform'], 'iPhone')) {
										$platformImage = "images/platforms/ios.png";
									}else if(strstr($platformXmlField->Player['platform'], 'iOS')) {
										$platformImage = "images/platforms/ios.png";
									}else if(strstr($platformXmlField->Player['platform'], 'Plex Home Theater')) {
										$platformImage = "images/platforms/pht.png";
									}else if(strstr($platformXmlField->Player['platform'], 'Linux/RPi-XBMC')) {
										$platformImage = "images/platforms/xbmc.png";
									}else if(strstr($platformXmlField->Player['platform'], 'Safari')) {
										$platformImage = "images/platforms/safari.png";
									}else if(strstr($platformXmlField->Player['platform'], 'Internet Explorer')) {
										$platformImage = "images/platforms/ie.png";
									}else if(strstr($platformXmlField->Player['platform'], 'Windows-XBMC')) {
										$platformImage = "images/platforms/xbmc.png";
									}else if(empty($platformXmlField->Player['platform'])) {
										if(strstr($platformXmlField->Player['title'], 'Apple')) {
											$platformImage = "images/platforms/atv.png";
										//Code below matches Samsung naming standard: [Display Technology: 2 Letters][Size: 2 digits][Generation: 1 letter][Model: 4 digits]
										}else if(preg_match("/TV [a-z][a-z]\d\d[a-z]/i",$platformXmlField->Player['title'])) {
											$platformImage = "images/platforms/samsung.png";	
										}else{
											$platformImage = "images/platforms/default.png";
										}
									}
									
									echo "<div class='user-platforms'>";
										echo "<ul>";
											echo "<div class='user-platforms-instance'>";
												echo "<li>";
												echo "<img class='user-platforms-instance-poster' src='".$platformImage."'></img>";

												if ($platformXmlField->Player['platform'] == "Chromecast") {
													echo "<div class='user-platforms-instance-name'>Plex/Web (Chrome) & Chromecast</div>";
												}else{
													echo "<div class='user-platforms-instance-name'>".$platformResultsRow['platform']."</div>";
												}

												
												echo "<div class='user-platforms-instance-playcount'><h3>".$platformResultsRow['platform_count']."</h3><p> plays</p></div>";
												echo "</li>";
											echo "</div>";
										echo "</ul>";
									echo "</div>";
								}
							echo "</div>";
						echo "</div>";	
				echo "</div>";		
			echo "</div>";	
	
			echo "<div class='container-fluid'>";	
				echo "<div class='row-fluid'>";
					echo "<div class='span12'>";
						echo "<div class='wellbg'>";
						
							echo "<div class='wellheader'>";
								echo "<div class='dashboard-wellheader'>";
									echo"<h3>Recently watched</h3>";
								echo"</div>";
							echo"</div>";
							
							echo "<div class='dashboard-recent-media-row'>";
								echo "<ul class='dashboard-recent-media'>";
						
									$recentlyWatchedResults = $db->query("SELECT title, user, platform, time, stopped, ip_address, xml, paused_counter FROM ".$plexWatchDbTable." WHERE user = '$user' ORDER BY time DESC LIMIT 10");
									// Run through each feed item
									while ($recentlyWatchedRow = $recentlyWatchedResults->fetchArray()) {

									$request_url = $recentlyWatchedRow['xml'];
									$recentXml = simplexml_load_string($request_url) ;                      
					
									if ($recentXml['type'] == "episode") {
										if ($plexWatch['myPlexAuthToken'] != '') {
											$myPlexAuthToken = $plexWatch['myPlexAuthToken'];
											$recentMetadata = "".$plexWatchPmsUrl."/library/metadata/".$recentXml['ratingKey']."?query=c&X-Plex-Token=".$myPlexAuthToken."";
										}else{
											$myPlexAuthToken = '';
											$recentMetadata = "".$plexWatchPmsUrl."/library/metadata/".$recentXml['ratingKey']."";
										}
										
										if ($recentThumbUrlRequest = @simplexml_load_file ($recentMetadata)) { 
											
											$recentThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$recentThumbUrlRequest->Video['parentThumb']."&width=136&height=280";                                        

											echo "<div class='dashboard-recent-media-instance'>";
											echo "<li>";
												
											if($recentThumbUrlRequest->Video['parentThumb']) {
													echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$recentXml['ratingKey']. "'><img src='includes/img.php?img=".urlencode($recentThumbUrl)."' class='poster-face'></img></a></div></div>";
												}else{
													echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$recentXml['ratingKey']. "'><img src='images/poster.png' class='poster-face'></img></a></div></div>";
											}
												
											echo "<div class=dashboard-recent-media-metacontainer>";
												$parentIndexPadded = sprintf("%01s", $recentXml['parentIndex']);
												$indexPadded = sprintf("%02s", $recentXml['index']);
												echo "<h3>Season ".$parentIndexPadded.", Episode ".$indexPadded."</h3>";
											echo "</div>";
											echo "</li>";
											echo "</div>";
										}
									}else if ($recentXml['type'] == "movie") {	
										if ($plexWatch['myPlexAuthToken'] != '') {
											$myPlexAuthToken = $plexWatch['myPlexAuthToken'];
											$recentMetadata = "".$plexWatchPmsUrl."/library/metadata/".$recentXml['ratingKey']."?query=c&X-Plex-Token=".$myPlexAuthToken."";
										}else{
											$myPlexAuthToken = '';
											$recentMetadata = "".$plexWatchPmsUrl."/library/metadata/".$recentXml['ratingKey']."";
										}
										
										if ($recentThumbUrlRequest = @simplexml_load_file ($recentMetadata)) {          

											$recentThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$recentThumbUrlRequest->Video['thumb']."&width=136&height=280";                                        

											echo "<div class='dashboard-recent-media-instance'>";
											echo "<li>";
											
											if($recentThumbUrlRequest->Video['thumb']) {
												echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$recentXml['ratingKey']. "'><img src='includes/img.php?img=".urlencode($recentThumbUrl)."' class='poster-face'></img></a></div></div>";
											}else{
												echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$recentXml['ratingKey']. "'><img src='images/poster.png' class='poster-face'></img></a></div></div>";
											}
											
											echo "<div class=dashboard-recent-media-metacontainer>";
											$parentIndexPadded = sprintf("%01s", $recentXml['parentIndex']);
											$indexPadded = sprintf("%02s", $recentXml['index']);
											echo "<h3>".$recentXml['title']." (".$recentXml['year'].")</h3>";

											echo "</div>";
											echo "</li>";
											echo "</div>";
										}	
									}else if ($recentXml['type'] == "clip") {	
										if ($plexWatch['myPlexAuthToken'] != '') {
											$myPlexAuthToken = $plexWatch['myPlexAuthToken'];
											$recentMetadata = "".$plexWatchPmsUrl."/library/metadata/".$recentXml['ratingKey']."?query=c&X-Plex-Token=".$myPlexAuthToken."";
										}else{
											$myPlexAuthToken = '';
											$recentMetadata = "".$plexWatchPmsUrl."/library/metadata/".$recentXml['ratingKey']."";
										}
										if ($recentThumbUrlRequest = @simplexml_load_file ($recentMetadata)) {          

											$recentThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$recentThumbUrlRequest->Video['thumb']."&width=136&height=280";
											
											echo "<div class='dashboard-recent-media-instance'>";
											echo "<li>";
											echo "<div class='poster'><div class='poster-face'><a href='" .$recentXml['ratingKey']. "'><img src='images/poster.png' class='poster-face'></img></a></div></div>";
											
											echo "<div class=dashboard-recent-media-metacontainer>";
											$parentIndexPadded = sprintf("%01s", $recentXml['parentIndex']);
											$indexPadded = sprintf("%02s", $recentXml['index']);
											echo "<h3>".$recentXml['title']." (".$recentXml['year'].")</h3>";

											echo "</div>";
											echo "</li>";
											echo "</div>";
										}	
									}else{}
									}
								echo "</ul>";
							echo "</div>";
							
						echo"</div>";	
					echo "</div>";
				echo "</div>";
			echo "</div>";
			
		echo "</div>";
			
		echo "<div class='tab-pane' id='userAddresses'>";
		
			$userIpAddressesQuery = $db->query("SELECT time,ip_address,platform,xml, COUNT(ip_address) as play_count FROM processed WHERE user = '$user' GROUP BY ip_address ORDER BY time DESC");

			echo "<div class='container-fluid'>";	
				echo "<div class='row-fluid'>";
					echo "<div class='span12'>";
						echo "<div class='wellbg'>";
							
							echo "<div class='wellheader'>";
							
								echo "<div class='dashboard-wellheader'>";
										echo"<h3>Public IP Addresses for <strong>".$user."</strong></h3>";
								echo"</div>";
							
							echo"</div>";
								
							echo "<table id='tableUserIpAddresses' class='display'>";
								echo "<thead>";
									echo "<tr>";
										echo "<th align='center'><i class='icon-sort icon-white'></i> Last seen</th>";
										echo "<th align='center'><i class='icon-sort icon-white'></i> IP Address</th>";
										echo "<th align='left'><i class='icon-sort icon-white'></i> Play Count</th>";
										echo "<th align='left'><i class='icon-sort icon-white'></i> Platform (Last Seen)</th>";
										echo "<th align='left'><i class='icon-sort icon-white'></i> Location</th>";
										
									echo "</tr>";
								echo "</thead>";
								echo "<tbody>";
								
								while ($userIpAddresses = $userIpAddressesQuery->fetchArray()) {
								
										if (!empty($userIpAddresses['ip_address'])) {
													
											if (strpos($userIpAddresses['ip_address'], "192.168" ) === 0) {
													
											}else if (strpos($userIpAddresses['ip_address'], "10." ) === 0) {
		
											}else if (strpos($userIpAddresses['ip_address'], "172.16" ) === 0) {	//need a solution to check for 17-31
											
											}else{ 
												
												$userIpAddressesUrl = "http://www.geoplugin.net/xml.gp?ip=".$userIpAddresses['ip_address']."";
												$userIpAddressesData = simplexml_load_file($userIpAddressesUrl) or die ("<div class=\"alert alert-warning \">Cannot access http://www.geoplugin.net.</div>");

												echo "<tr>";
													echo "<td align='center'>".date($plexWatch['dateFormat'],$userIpAddresses['time'])."</td>";
													echo "<td align='center'>".$userIpAddresses['ip_address']."</td>";
													echo "<td align='left'>".$userIpAddresses['play_count']."</td>";

													$userIpAddressesXml = simplexml_load_string($userIpAddresses['xml']); 
													
													if ($userIpAddressesXml->Player['platform'] == "Chromecast") {
														echo "<td align='left'>".$userIpAddressesXml->Player['platform']."</td>";
													}else{
														echo "<td align='left'>".$userIpAddresses['platform']."</td>";
													}

													
													if (empty($userIpAddressesData->geoplugin_city)) {
														echo "<td align='left'>n/a</td>";
													}else{
														echo "<td align='left'><a href='https://maps.google.com/maps?q=".$userIpAddressesData->geoplugin_city.", ".$userIpAddressesData->geoplugin_region."'><i class='icon-map-marker icon-white'></i> ".$userIpAddressesData->geoplugin_city.", ".$userIpAddressesData->geoplugin_region."</a></td>";
														
													}
													
												echo "</tr>";
											}
												
										}
										
									}	
										
								echo "</tbody>";
							echo "</table>";
								
							
						echo "</div>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		
		echo "</div>";
		echo "<div class='tab-pane' id='userHistory'>";	
		
			echo "<div class='container-fluid'>";	
				echo "<div class='row-fluid'>";
					echo "<div class='span12'>";
						echo "<div class='wellbg'>";
							echo "<div class='wellheader'>";

								echo "<div class='dashboard-wellheader'>";
										echo"<h3>Watching History for <strong>".$user."</strong></h3>";
									echo"</div>";
								echo"</div>";
								
								if ($numRows < 1) {

								echo "No Results.";

								} else {
								
								echo "<table id='tableUserHistory' class='display'>";
									echo "<thead>";
										echo "<tr>";
											echo "<th align='center'><i class='icon-sort icon-white'></i> Date</th>";
											echo "<th align='left'><i class='icon-sort icon-white'></i> Platform</th>";
											echo "<th align='left'><i class='icon-sort icon-white'></i> IP Address</th>";
											echo "<th align='left'><i class='icon-sort icon-white'></i> Title</th>";
											echo "<th align='center'><i class='icon-sort icon-white'></i> Stream Info</th>";
											echo "<th align='center'><i class='icon-sort icon-white'></i> Started</th>";
											echo "<th align='center'><i class='icon-sort icon-white'></i> Paused</th>";
											echo "<th align='center'><i class='icon-sort icon-white'></i> Stopped</th>";
											echo "<th align='center'><i class='icon-sort icon-white'></i> Duration</th>";
											echo "<th align='center'><i class='icon-sort icon-white'></i> Data</th>";
											echo "<th align='center'><i class='icon-sort icon-white'></i> Completed</th>";
										echo "</tr>";
									echo "</thead>";
									echo "<tbody>";
									$rowCount = 0;
									while ($row = $results->fetchArray()) {
										$rowCount++;
										$request_url = $row['xml'];
										$xmlfield = simplexml_load_string($request_url) ; 
										$ratingKey = $xmlfield['ratingKey'];
										$type = $xmlfield['type'];
										$duration = $xmlfield['duration'];
										$viewOffset = $xmlfield['viewOffset'];
										$platform = $xmlfield->Player['platform'];
										$size = $xmlfield->Media->Part['size'];
										$size_total = (1 * $size);
										
										echo "<tr>";
											if (empty($row['stopped'])) {
												echo "<td class='currentlyWatching' align='center'>Currently watching...</td>";
											}else{
												echo "<td align='center'>".date($plexWatch['dateFormat'],$row['time'])."</td>";
											}
											
											if ($platform == "Chromecast") {
												echo "<td align='left'>".$platform."</td>";
											}else{
												echo "<td align='left'>".$row['platform']."</td>";
											}

											if (empty($row['ip_address'])) {
												echo "<td align='left'>n/a</td>";

											}else{

												echo "<td align='left'>".$row['ip_address']."</td>";
											}
											
											
											
											if ($type=="movie") {
											echo "<td class='title' align='left'><a href='info.php?id=".$ratingKey."'>".$row['title']."</a></td>";
											}else if ($type=="episode") {
											echo "<td class='title' align='left'><a href='info.php?id=".$ratingKey."'>".$row['title']."</a></td>";
											}else if (!array_key_exists('',$type)) {
											echo "<td class='title' align='left'><a href='".$ratingKey."'>".$row['title']."</a></td>";
											}else{

											}


											echo "<td align='center'><a href='#streamDetailsModal".$rowCount."' data-toggle='modal'><span class='badge badge-inverse'><i class='icon-info icon-white'></i></span></a></td>";
							
							

							echo "<div id='streamDetailsModal".$rowCount."' class='modal hide fade' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>";
							?>
								<div class="modal-header">	
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon icon-remove"></i></button>		
									<h3 id="myModalLabel"><i class="icon-info-sign icon-white"></i> Stream Info: <strong><?php echo $row['title']; ?></strong></h3>
								</div>
								
								<div class="modal-body">
									<?php
									
									if (array_key_exists('TranscodeSession',$xmlfield)) {

									?>
										<div class="span4">
											<h4>Stream Details</h4>
											<ul>
											<h5>Video</h5>
											<li>Stream Type: <strong><?php echo $xmlfield->TranscodeSession['videoDecision']; ?></strong></li>
											<li>Video Resolution: <strong><?php echo $xmlfield->TranscodeSession['height']; ?>p</strong></li>
											<li>Video Codec: <strong><?php echo $xmlfield->TranscodeSession['videoCodec']; ?></strong></li>
											<li>Video Width: <strong><?php echo $xmlfield->TranscodeSession['width']; ?></strong></li>
											<li>Video Height: <strong><?php echo $xmlfield->TranscodeSession['height']; ?></strong></li>
											</ul>
											<ul>
											<h5>Audio</h5>
											<li>Stream Type: <strong><?php echo $xmlfield->TranscodeSession['audioDecision']; ?></strong></li>
											<?php if ($xmlfield->TranscodeSession['audioCodec'] == "dca") { ?>
												<li>Audio Codec: <strong>dts</strong></li>
											<?php }else{ ?>
												<li>Audio Codec: <strong><?php echo $xmlfield->TranscodeSession['audioCodec']; ?></strong></li>
											<?php } ?>
											<li>Audio Channels: <strong><?php echo $xmlfield->TranscodeSession['audioChannels']; ?></strong></li>
											</ul>
										</div>
										<div class="span4">
											<h4>Media Source Details</h4>
											<li>Container: <strong><?php echo $xmlfield->Media['container']; ?></strong></li>
											<li>Resolution: <strong><?php echo $xmlfield->Media['videoResolution']; ?>p</strong></li>
											<li>Bitrate: <strong><?php echo $xmlfield->Media['bitrate']; ?> kbps</strong></li>
											<li>Size: <strong><?php echo formatBytes($size_total); ?></strong></li>
										</div>
										<div class="span4">	
											<h4>Video Source Details</h4>
											<ul>
												<li>Width: <strong><?php echo $xmlfield->Media['width']; ?></strong></li>
												<li>Height: <strong><?php echo $xmlfield->Media['height']; ?></strong></li>
												<li>Aspect Ratio: <strong><?php echo $xmlfield->Media['aspectRatio']; ?></strong></li>											
												<li>Video Frame Rate: <strong><?php echo $xmlfield->Media['videoFrameRate']; ?></strong></li>
												<li>Video Codec: <strong><?php echo $xmlfield->Media['videoCodec']; ?></strong></li>
											</ul>
											<ul> </ul>
											<h4>Audio Source Details</h4>
											<ul>
												<?php if ($xmlfield->Media['audioCodec'] == "dca") { ?>
													<li>Audio Codec: <strong>dts</strong></li>
												<?php }else{ ?>
													<li>Audio Codec: <strong><?php echo $xmlfield->Media['audioCodec']; ?></strong></li>
												<?php } ?>
												<li>Audio Channels: <strong><?php echo $xmlfield->Media['audioChannels']; ?></strong></li>
											</ul>
										</div>
										
										
									
									<?php }else{ ?>

										<div class="span4">
											<h4>Stream Details</strong></h4>
											<ul>
												<h5>Video</h5>
												<li>Stream Type: <strong>Direct Play</strong></li>
												<li>Video Resolution: <strong><?php echo $xmlfield->Media['videoResolution']; ?>p</strong></li>
												<li>Video Codec: <strong><?php echo $xmlfield->Media['videoCodec']; ?></strong></li>
											</ul>
											<ul>
												<h5>Audio</h5>
												<li>Stream Type: <strong>Direct Play</strong></li>
												<li>Video Width: <strong><?php echo $xmlfield->Media['width']; ?></strong></li>
												<li>Video Height: <strong><?php echo $xmlfield->Media['height']; ?>p</strong></li>
												<?php if ($xmlfield->Media['audioCodec'] == "dca") { ?>
														<li>Audio Codec: <strong>dts</strong></li>
													<?php }else{ ?>
														<li>Audio Codec: <strong><?php echo $xmlfield->Media['audioCodec']; ?></strong></li>
													<?php } ?>
												<li>Audio Channels: <strong><?php echo $xmlfield->Media['audioChannels']; ?></strong></li>
											</ul>
										</div>
										<div class="span4">
											<h4>Media Source Details</h4>
											<li>Container: <strong><?php echo $xmlfield->Media['container']; ?></strong></li>
											<li>Resolution: <strong><?php echo $xmlfield->Media['videoResolution']; ?>p</strong></li>
											<li>Bitrate: <strong><?php echo $xmlfield->Media['bitrate']; ?> kbps</strong></li>
											<li>Size: <strong><?php echo formatBytes($size_total); ?></strong></li>
										</div>
										<div class="span4">	
											<h4>Video Source Details</h4>
											<ul>
												<li>Width: <strong><?php echo $xmlfield->Media['width']; ?></strong></li>
												<li>Height: <strong><?php echo $xmlfield->Media['height']; ?></strong></li>
												<li>Aspect Ratio: <strong><?php echo $xmlfield->Media['aspectRatio']; ?></strong></li>											
												<li>Video Frame Rate: <strong><?php echo $xmlfield->Media['videoFrameRate']; ?></strong></li>
												<li>Video Codec: <strong><?php echo $xmlfield->Media['videoCodec']; ?></strong></li>
											</ul>
											<ul> </ul>
											<h4>Audio Source Details</h4>
											<ul>
												<?php if ($xmlfield->Media['audioCodec'] == "dca") { ?>
													<li>Audio Codec: <strong>dts</strong></li>
												<?php }else{ ?>
													<li>Audio Codec: <strong><?php echo $xmlfield->Media['audioCodec']; ?></strong></li>
												<?php } ?>
												<li>Audio Channels: <strong><?php echo $xmlfield->Media['audioChannels']; ?></strong></li>
											</ul>
										</div>
									<?php } ?>
									
										
										
										
								</div>
										  
								<div class="modal-footer">
								</div>

							</div>
							<?php

															
											echo "<td align='center'>".date($plexWatch['timeFormat'],$row['time'])."</td>";
											
											$paused_duration = round(abs($row['paused_counter']) / 60,1);
											echo "<td align='center'>".$paused_duration." min</td>";
											
											$stopped_time = date($plexWatch['timeFormat'],$row['stopped']);
											
											if (empty($row['stopped'])) {								
												echo "<td align='center'>n/a</td>";
											}else{
												echo "<td align='center'>".$stopped_time."</td>";
											}

											$to_time = strtotime(date("m/d/Y g:i a",$row['stopped']));
											$from_time = strtotime(date("m/d/Y g:i a",$row['time']));
											$paused_time = strtotime(date("m/d/Y g:i a",$row['paused_counter']));
											
											$viewed_time = round(abs($to_time - $from_time - $paused_time) / 60,0);
											$viewed_time_length = strlen($viewed_time);
											
											
											
											if ($viewed_time_length == 8) {
												echo "<td align='center'>n/a</td>";
											}else{
												echo "<td align='center'>".$viewed_time. " min</td>";
											}
											
											$percentComplete = ($duration == 0 ? 0 : sprintf("%2d", ($viewOffset / $duration) * 100));
												if ($percentComplete >= 90) {	
												  $percentComplete = 100;    
												}
											$size = $xmlfield->Media->Part['size'];	
											$dataTransferred = ($percentComplete / 100 * ($size));
											
											echo "<td align='center'>".formatBytes($dataTransferred)."</td>";
											echo "<td align='center'><span class='badge badge-warning'>".$percentComplete."%</span></td>";
										echo "</tr>";   
									}
								}
									echo "</tbody>";
								echo "</table>";
							
						echo "</div>";
					echo "</div>";

?>
<!-- had to use a fixed width because percentage didn't work until window was resized -->
<div class='wellbg'><strong>History</strong><br><figure style='width: 900px; height: 200px;' id='userChartFinal'></figure></div>
<?php
				echo "</div>";
			echo "</div>";			
		echo "</div>";
		


	echo "</div>";


		
	?>

	
	
		<footer>
		
		
		</footer>
		
    
    
    <!-- javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-2.0.3.js"></script>
	<script src="js/bootstrap.js"></script>
	<script src="js/jquery.dataTables.js"></script>
	<script src="js/jquery.dataTables.plugin.date_sorting.js"></script>
	<script src="js/jquery.dataTables.plugin.bootstrap_pagination.js"></script>
	<script src="js/d3.v3.js"></script> 
	<script src="js/xcharts.min.js"></script> 	
	<script>
		$(document).ready(function() {
			var oTable = $('#tableUserHistory').dataTable( {
				"bPaginate": true,
				"bLengthChange": true,
				"bFilter": true,
				"bSort": true,
				"bInfo": true,
				"bAutoWidth": true,	
				"aaSorting": [[ 0, "desc" ]],			
				"bStateSave": false,
				"bSortClasses": false,
				"sPaginationType": "bootstrap",
				"aoColumnDefs": [
			      { "sType": "us_date", "aTargets": [ 0 ] }
			    ]	
			} );
		} );
	</script>
	<script>
		$(document).ready(function() {
			var oTable = $('#tableUserIpAddresses').dataTable( {
				"bPaginate": true,
				"bLengthChange": true,
				"bFilter": true,
				"bSort": true,
				"bInfo": true,
				"bAutoWidth": true,	
				"aaSorting": [[ 0, "desc" ]],			
				"bStateSave": false,
				"bSortClasses": false,
				"sPaginationType": "bootstrap",
				"aoColumnDefs": [
			      { "sType": "us_date", "aTargets": [ 0 ] }
			    ]		
			} );
		} );
	</script>
	


		<script>
	var tt = document.createElement('div'),
	  leftOffset = -(~~$('html').css('padding-left').replace('px', '') + ~~$('body').css('margin-left').replace('px', '')),
	  topOffset = -35;
	tt.className = 'ex-tooltip';
	document.body.appendChild(tt);

	var data = {
	  "xScale": "ordinal",
	  "yScale": "linear",
	  "main": [
		{
		  "className": ".playcount",
		  "data": [
			<?php echo $userPlayFinal ?>
		  ]
		}
	  ]
	};
	var opts = {
	  "dataFormatX": function (x) { return d3.time.format('%Y-%m-%d').parse(x); },
	  "tickFormatX": function (x) { return d3.time.format('%b %e')(x); },
	  "paddingLeft": ('35'),
	  "paddingRight": ('35'),
	  "paddingTop": ('10'),
	  "tickHintY": ('5'),
	  "mouseover": function (d, i) {
		var pos = $(this).offset();
		$(tt).text(+ d.y + ' play(s)')
		  .css({top: topOffset + pos.top, left: pos.left + leftOffset})
		  .show();
	  },
	  "mouseout": function (x) {
		$(tt).hide();
	  }
	};
	var myChart = new xChart('bar', data, '#userChartFinal', opts);
	</script>

	
	
	<script>
	$(document).ready(function() {
		$('#home').tooltip();
	});
	$(document).ready(function() {
		$('#history').tooltip();
	});
	$(document).ready(function() {
		$('#users').tooltip();
	});
	$(document).ready(function() {
		$('#charts').tooltip();
	});
	$(document).ready(function() {
		$('#settings').tooltip();
	});
	$(document).ready(function() {
		$('#stats').tooltip();
	});
	</script>
	
  </body>
</html>
