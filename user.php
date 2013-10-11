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

	
  
	<div class="container">
		    			
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				
				<div class="logo"></div>
				<ul class="nav">
					
					<li><a href="index.php"><i class="icon-home icon-white"></i> Home</a></li>
					<li><a href="history.php"><i class="icon-calendar icon-white"></i> History</a></li>
					<li><a href="users.php"><i class="icon-user icon-white"></i> Users</a></li>
					<li><a href="charts.php"><i class="icon-list icon-white"></i> Charts</a></li>
					
				</ul>
			</div>
		</div>
    </div>
	<?php
	
	date_default_timezone_set(@date_default_timezone_get());
	
	require_once(dirname(__FILE__) . '/config.php');
	
	$user = $_GET['user'];

	$db = new SQLite3($plexWatch['plexWatchDb']);
	
	$numRows = $db->querySingle("SELECT COUNT(*) as count FROM processed ");
	$userInfo = $db->query("SELECT user,xml FROM processed WHERE user = '$user' ORDER BY time DESC LIMIT 1") or die ("Failed to access plexWatch database. Please check your server and config.php settings.");
	
	$userStatsDailyCount = $db->querySingle("SELECT COUNT(*) FROM processed WHERE datetime(stopped, 'unixepoch', 'localtime') >= date('now', 'localtime') AND user='$user' ");
	
	$userStatsDailyTimeFetch = $db->query("SELECT time,stopped,xml,paused_counter FROM processed WHERE datetime(stopped, 'unixepoch', 'localtime') >= date('now', 'localtime') AND user='$user' ");
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
	
	
	
	
	
	$userStatsWeeklyCount = $db->querySingle("SELECT COUNT(*) FROM processed WHERE datetime(stopped, 'unixepoch') >= datetime('now', '-7 days', 'localtime') AND user='$user' ");
	
	$userStatsWeeklyTimeFetch = $db->query("SELECT time,stopped,xml,paused_counter FROM processed WHERE datetime(stopped, 'unixepoch', 'localtime') >= datetime('now', '-7 days', 'localtime') AND user='$user' ");
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
	
	
	$userStatsMonthlyCount = $db->querySingle("SELECT COUNT(*) FROM processed WHERE datetime(stopped, 'unixepoch', 'localtime') >= datetime('now', '-30 days', 'localtime') AND user='$user' ");
	
	$userStatsMonthlyTimeFetch = $db->query("SELECT time,stopped,xml,paused_counter FROM processed WHERE datetime(stopped, 'unixepoch', 'localtime') >= datetime('now', '-30 days', 'localtime') AND user='$user' ");
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
	
	
	$userStatsAlltimeCount = $db->querySingle("SELECT COUNT(*) FROM processed WHERE user='$user' ");
	
	$userStatsAlltimeTimeFetch = $db->query("SELECT time,stopped,xml,paused_counter FROM processed WHERE user='$user' ");
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
	
	$results = $db->query("SELECT * FROM processed WHERE user = '$user' ORDER BY time DESC");
	

	echo "<div class='container-fluid'>";
		echo "<div class='row-fluid'>";
			echo "<div class='span12'>";
				echo "<div class='user-info-wrapper'>";
					while ($userInfoResults= $userInfo->fetchArray()) {
						$userInfoXml = $userInfoResults['xml'];
						$userInfoXmlField = simplexml_load_string($userInfoXml); 
						if (empty($userInfoXmlField->User['thumb'])) {
							echo "<div class='user-info-poster-face'><img src='images/gravatar-default-80x80.png'></></div>";
						}else{
							echo "<div class='user-info-poster-face'><img src='".$userInfoXmlField->User['thumb']."'></></div>";
						}
					}
					
					echo "<div class='user-info-username'>".$user."</div>";
					echo "<div class='user-info-nav'>";
						echo "<ul class='user-info-nav'>";
							echo "<li class='active'><a href='#profile' data-toggle='tab'>Profile</a></li>";
							echo "<li><a href='#history' data-toggle='tab'>History</a></li>";
							
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
									echo"<h3>User Stats</h3>";
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
											
											if ($userStatsDailyTimeViewedTimeRowLength == 10) {
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
											
											if ($userStatsWeeklyTimeViewedTimeRowLength == 10) {
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
											
											if ($userStatsMonthlyTimeViewedTimeRowLength == 10) {
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
											
											if ($userStatsAlltimeTimeViewedTimeRowLength == 10) {
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
									echo"<h3>Recently watched</h3>";
								echo"</div>";
							echo"</div>";
							
							echo "<div class='dashboard-recent-media-row'>";
					echo "<ul class='dashboard-recent-media'>";
						
						$recentlyWatchedResults = $db->query("SELECT * FROM processed WHERE user = '$user' ORDER BY time DESC LIMIT 10");
						// Run through each feed item
						while ($recentlyWatchedRow = $recentlyWatchedResults->fetchArray()) {
						
						$request_url = $recentlyWatchedRow['xml'];
						$recentXml = simplexml_load_string($request_url) ;                      
		
						if ($recentXml['type'] == "episode") {
							$recentMetadata = "http://".$plexWatch['pmsUrl'].":".$plexWatch['pmsPort']."/library/metadata/".$recentXml['ratingKey']."";
                            $recentThumbUrlRequest = simplexml_load_file ($recentMetadata);                                       
							$recentThumbUrl = "http://".$plexWatch['pmsUrl'].":".$plexWatch['pmsPort']."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsPort']."".$recentThumbUrlRequest->Video['parentThumb']."&width=136&height=280";                                        
							
								echo "<div class='dashboard-recent-media-instance'>";
								echo "<li>";
								echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$recentXml['ratingKey']. "'><img src='".$recentThumbUrl."' class='poster-face'></img></a></div></div>";
								
								echo "<div class=dashboard-recent-media-metacontainer>";
									$parentIndexPadded = sprintf("%01s", $recentXml['parentIndex']);
									$indexPadded = sprintf("%02s", $recentXml['index']);
									echo "<h3>Season ".$parentIndexPadded.", Episode ".$indexPadded."</h3>";
								echo "</div>";
								echo "</li>";
								echo "</div>";
						}else if ($recentXml['type'] == "movie") {	
							$recentMetadata = "http://".$plexWatch['pmsUrl'].":".$plexWatch['pmsPort']."/library/metadata/".$recentXml['ratingKey']."";
                            $recentThumbUrlRequest = simplexml_load_file ($recentMetadata);         
							$recentThumbUrl = "http://".$plexWatch['pmsUrl'].":".$plexWatch['pmsPort']."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsPort']."".$recentThumbUrlRequest->Video['thumb']."&width=136&height=280";                                        
							
								echo "<div class='dashboard-recent-media-instance'>";
								echo "<li>";
								echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$recentXml['ratingKey']. "'><img src='".$recentThumbUrl."' class='poster-face'></img></a></div></div>";
								
								echo "<div class=dashboard-recent-media-metacontainer>";
								$parentIndexPadded = sprintf("%01s", $recentXml['parentIndex']);
								$indexPadded = sprintf("%02s", $recentXml['index']);
								echo "<h3>".$recentXml['title']." (".$recentXml['year'].")</h3>";

								echo "</div>";
								echo "</li>";
								echo "</div>";
						}else{}
						}
					echo "</ul>";
				echo "</div>";
							
						echo"</div>";	
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
			
		echo "<div class='tab-pane' id='history'>";
		
		
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
							
							echo "<table id='user-history' class='display'>";
								echo "<thead>";
									echo "<tr>";
										echo "<th align='center'><i class='icon-calendar icon-white'></i> Date</th>";
										echo "<th align='left'><i class='icon-hdd icon-white'></i> Platform</th>";
										echo "<th align='left'><i class='icon-globe icon-white'></i> IP Address</th>";
										echo "<th align='left'>Title</th>";
										echo "<th align='center'><i class='icon-play icon-white'></i> Started</th>";
										echo "<th align='center'><i class='icon-pause icon-white'></i> Paused</th>";
										echo "<th align='center'><i class='icon-stop icon-white'></i> Stopped</th>";
										echo "<th align='center'><i class='icon-time icon-white'></i> Duration</th>";
										echo "<th align='center'>Completed</th>";
									echo "</tr>";
								echo "</thead>";
								echo "<tbody>";
								while ($row = $results->fetchArray()) {
								
								echo "<tr>";
									if (empty($row['stopped'])) {
										echo "<td class='currentlyWatching' align='center'>Currently watching...</td>";
									}else{
										echo "<td align='center'>".date("m/d/Y",$row['time'])."</td>";
									}
									echo "<td align='left'>".$row['platform']."</td>";
									if (empty($row['ip_address'])) {
										echo "<td align='left'>n/a</td>";

									}else{

										echo "<td align='left'>".$row['ip_address']."</td>";
									}
									$request_url = $row['xml'];
									$xmlfield = simplexml_load_string($request_url) ; 
									$ratingKey = $xmlfield['ratingKey'];
									$type = $xmlfield['type'];
									$duration = $xmlfield['duration'];
									$viewOffset = $xmlfield['viewOffset'];

									if ($type=="movie") {
									echo "<td align='left'><a href='info.php?id=".$ratingKey."'>".$row['title']."</a></td>";
									}else if ($type=="episode") {
									echo "<td align='left'><a href='info.php?id=".$ratingKey."'>".$row['title']."</a></td>";
									}else if (!array_key_exists('',$type)) {
									echo "<td align='left'><a href='".$ratingKey."'>".$row['title']."</a></td>";
									}else{

									}
													
									echo "<td align='center'>".date("g:i a",$row['time'])."</td>";
									
									$paused_time = round(abs($row['paused_counter']) / 60,1);
									echo "<td align='center'>".$paused_time." min</td>";
									
									$stopped_time = date("g:i a",$row['stopped']);
									
									if (empty($row['stopped'])) {								
										echo "<td align='center'>n/a</td>";
									}else{
										echo "<td align='center'>".$stopped_time."</td>";
									}

									$to_time = strtotime(date("m/d/Y g:i a",$row['stopped']));
									$from_time = strtotime(date("m/d/Y g:i a",$row['time']));
									
									$viewed_time = round(abs($to_time - $from_time - $paused_time) / 60,0);
									$viewed_time_length = strlen($viewed_time);
									
									
									
									if ($viewed_time_length == 8) {
										echo "<td align='center'>n/a</td>";
									}else{
										echo "<td align='center'>".$viewed_time. " min</td>";
									}
									
									$percentComplete = sprintf("%2d", ($viewOffset / $duration) * 100);
										if ($percentComplete >= 90) {	
										  $percentComplete = 100;    
										}

									echo "<td align='center'><span class='badge badge-warning'>".$percentComplete."%</span></td>";
								echo "</tr>";   
							}
							}
								echo "</tbody>";
							echo "</table>";

							
								
						echo "</div>";
					echo "</div>";
					
				echo "</div>";
			echo "</div>";			
		echo "</div>";
		
		echo "<div class='tab-pane' id='charts'>";
			echo "";
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
	<script src="js/jquery.dataTables.plugin.bootstrap_pagination.js"></script>
	
	<script>
		$(document).ready(function() {
			var oTable = $('#user-history').dataTable( {
				"bPaginate": true,
				"bLengthChange": true,
				"bFilter": true,
				"bSort": true,
				"bInfo": true,
				"bAutoWidth": true,
				"aaSorting": [[ 0, "desc" ]],
				"bStateSave": true,
				"bSortClasses": false,
				"sPaginationType": "bootstrap"	
			} );
		} );
	</script>
	

  </body>
</html>
