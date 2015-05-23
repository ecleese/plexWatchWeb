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

class elapsedTimeResult {
	public $minutes;
	public $hours;
	public $days;
	public $strLen;
	function setElapsedTime($elapsedTimeFetch) {
		$elapsedTime = 0;
		while ($elapsedTimeRow = $elapsedTimeFetch->fetchArray()) {
			$stopTime = strtotime(date("m/d/Y g:i a",$elapsedTimeRow['stopped']));
			$startTime = strtotime(date("m/d/Y g:i a",$elapsedTimeRow['time']));
			$minutesPaused = round(abs($elapsedTimeRow['paused_counter']), 1);
			$viewedTime = round(abs($stopTime - $startTime - $minutesPaused), 0);

			$this->strLen = strlen($viewedTime);
			$elapsedTime += $viewedTime;
			$this->days = floor($elapsedTime / 86400);
			$this->hours = floor(($elapsedTime % 86400 ) / 3600);
			$this->minutes = floor(($elapsedTime % 3600 ) / 60);
		}
	}
	public function outputHTML() {
		if (empty($this->strLen)) {
			echo "<h1> / </h1><h3>0</h3><p> mins</p>";
		} else if ($this->strLen == 10) {
			echo "";
		} else if (empty($this->minutes) && empty($this->hours) && empty($this->days)) {
			echo "<h1> / </h1><h3>0</h3><p> mins</p>";
		} else if ($this->days == 0 && $this->hours == 0 && $this->minutes == 1) {
			echo "<h1> / </h1><h3>".$this->minutes."</h3> <p>min</p>";
		} else if ($this->days == 0 && $this->hours == 0) {
			echo "<h1> / </h1><h3>".$this->minutes."</h3> <p>mins</p>";
		} else if ($this->days == 0 && $this->hours == 1) {
			echo "<h1> / </h1><h3>".$this->hours."</h3> <p>hr </p><h3>".$this->minutes."</h3> <p>mins</p>";
		} else if ($this->days == 0 && $this->minutes == 1) {
			echo "<h1> / </h1><h3>".$this->hours."</h3> <p>hrs </p><h3>".$this->minutes."</h3> <p>min</p>";
		} else if ($this->days == 0) {
			echo "<h1> / </h1><h3>".$this->hours."</h3> <p>hrs </p><h3>".$this->minutes."</h3> <p>mins</p>";
		} else if ($this->days == 1) {
			echo "<h1> / </h1><h3>".$this->days."</h3> <p>day </p><h3>".$this->hours."</h3> <p>hrs </p><h3>".$this->minutes."</h3> <p>mins</p>";
		} else if ($this->days == 1 && $this->hours == 1) {
			echo "<h1> / </h1><h3>".$this->days."</h3> <p>day </p><h3>".$this->hours."</h3> <p>hr </p><h3>".$this->minutes."</h3> <p>mins</p>";
		} else if ($this->days == 1 && $this->hours == 1 && $this->minutes == 1) {
			echo "<h1> / </h1><h3>".$this->days."</h3> <p>day </p><h3>".$this->hours."</h3> <p>hr </p><h3>".$this->minutes."</h3> <p>min</p>";
		} else {
			echo "<h1> / </h1><h3>".$this->days."</h3> <p>days </p><h3>".$this->hours."</h3> <p>hrs </p><h3>".$this->minutes."</h3> <p>mins</p>";
		}
	}
	function __construct($results) {
		$this->setElapsedTime($results);
	}
}

$plexWatchDbTable = dbTable('user');
$db = dbconnect();

$userStatsDailyCount = $db->querySingle("SELECT COUNT(*) FROM ".$plexWatchDbTable." WHERE datetime(stopped, 'unixepoch', 'localtime') >= date('now', 'localtime') AND user='$user' ");
$userStatsDailyTimeFetch = $db->query("SELECT time,stopped,paused_counter FROM ".$plexWatchDbTable." WHERE datetime(stopped, 'unixepoch', 'localtime') >= date('now', 'localtime') AND user='$user' ");
$dailyStats = new elapsedTimeResult($userStatsDailyTimeFetch);

$userStatsWeeklyCount = $db->querySingle("SELECT COUNT(*) FROM ".$plexWatchDbTable." WHERE datetime(stopped, 'unixepoch') >= datetime('now', '-7 days', 'localtime') AND user='$user' ");
$userStatsWeeklyTimeFetch = $db->query("SELECT time,stopped,paused_counter FROM ".$plexWatchDbTable." WHERE datetime(stopped, 'unixepoch', 'localtime') >= datetime('now', '-7 days', 'localtime') AND user='$user' ");
$weeklyStats = new elapsedTimeResult($userStatsWeeklyTimeFetch);

$userStatsMonthlyCount = $db->querySingle("SELECT COUNT(*) FROM ".$plexWatchDbTable." WHERE datetime(stopped, 'unixepoch', 'localtime') >= datetime('now', '-30 days', 'localtime') AND user='$user' ");
$userStatsMonthlyTimeFetch = $db->query("SELECT time,stopped,paused_counter FROM ".$plexWatchDbTable." WHERE datetime(stopped, 'unixepoch', 'localtime') >= datetime('now', '-30 days', 'localtime') AND user='$user' ");
$monthlyStats = new elapsedTimeResult($userStatsMonthlyTimeFetch);

$userStatsAlltimeCount = $db->querySingle("SELECT COUNT(*) FROM ".$plexWatchDbTable." WHERE user='$user' ");
$userStatsAlltimeTimeFetch = $db->query("SELECT time,stopped,paused_counter FROM ".$plexWatchDbTable." WHERE user='$user' ");
$allTimeStats = new elapsedTimeResult($userStatsAlltimeTimeFetch);

echo"<ul>";
	echo "<div class='user-overview-stats-instance'>";
		echo "<li>";
			echo "<div class='user-overview-stats-instance-text'>";
				echo "<h4>Today</h4>";
				if ($userStatsDailyCount == 1) {
					echo "<h3>".$userStatsDailyCount."</h3><p>play</p>";
				} else {
					echo "<h3>".$userStatsDailyCount."</h3><p>plays</p>";
				}
				$dailyStats->outputHTML();
			echo"</div>";
		echo "</li>";
	echo"</div>";
	echo "<div class='user-overview-stats-instance'>";
		echo "<li>";
			echo "<div class='user-overview-stats-instance-text'>";
				echo "<h4>Last week</h4>";
				if ($userStatsWeeklyCount == 1) {
					echo "<h3>".$userStatsWeeklyCount."</h3><p>play</p>";
				} else {
					echo "<h3>".$userStatsWeeklyCount."</h3><p>plays</p>";
				}
				$weeklyStats->outputHTML();
			echo"</div>";
		echo "</li>";
	echo"</div>";
	echo "<div class='user-overview-stats-instance'>";
		echo "<li>";
			echo "<div class='user-overview-stats-instance-text'>";
				echo "<h4>Last month</h4>";
				if ($userStatsMonthlyCount == 1) {
					echo "<h3>".$userStatsMonthlyCount."</h3><p>play</p>";
				} else {
					echo "<h3>".$userStatsMonthlyCount."</h3><p>plays</p>";
				}
				$monthlyStats->outputHTML();
			echo"</div>";
		echo "</li>";
	echo"</div>";
	echo "<div class='user-overview-stats-instance'>";
		echo "<li>";
			echo "<div class='user-overview-stats-instance-text'>";
				echo "<h4>All Time</h4>";
				if ($userStatsAlltimeCount == 1) {
					echo "<h3>".$userStatsAlltimeCount."</h3><p>play</p>";
				} else {
					echo "<h3>".$userStatsAlltimeCount."</h3><p>plays</p>";
				}
				$allTimeStats->outputHTML();
			echo"</div>";
		echo "</li>";
	echo"</div>";
echo"</ul>";
?>