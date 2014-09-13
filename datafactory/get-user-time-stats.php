<?php

date_default_timezone_set(@date_default_timezone_get());

if (isset($_POST['user'])) {
    $user = $_POST['user'];
} else {
    error_log('PlexWatchWeb :: POST parameter "id" not found.');
    echo "id field is required.";
    exit;
}

$guisettingsFile = "../config/config.php";
if (file_exists($guisettingsFile)) { 
    require_once('../config/config.php');
} else {
    error_log('PlexWatchWeb :: Config file not found.');
    echo "Config file not found";
    exit;
}

if ($plexWatch['https'] == 'yes') {
        $plexWatchPmsUrl = "https://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpsPort']."";
}else if ($plexWatch['https'] == 'no') {
        $plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";
}

$plexWatchDbTable = "";
if ($plexWatch['userHistoryGrouping'] == "yes") {
        $plexWatchDbTable = "grouped";
} else if ($plexWatch['userHistoryGrouping'] == "no") {
        $plexWatchDbTable = "processed";
}

$db = dbconnect();

	$userStatsDailyCount = $db->querySingle("SELECT COUNT(*) FROM ".$plexWatchDbTable." WHERE datetime(stopped, 'unixepoch', 'localtime') >= date('now', 'localtime') AND user='$user' ");
	
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
	
	$userStatsAlltimeTimeFetch = $db->query("SELECT time,stopped,paused_counter FROM ".$plexWatchDbTable." WHERE user='$user' ");
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
                    echo"</div>";	
                    echo "</li>";
            echo"</div>";
    echo"</ul>";