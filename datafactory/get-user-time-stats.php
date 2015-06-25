<?php
require_once(dirname(__FILE__) . '/../includes/functions.php');

if (!isset($_POST['user'])) {
	echo "User field is required.";
	trigger_error('PlexWatchWeb :: POST parameter "user" not found.', E_USER_ERROR);
}

class elapsedTimeResult {
	public $minutes;
	public $hours;
	public $days;
	public $strLen;
	public $plays;
	function setElapsedTime($elapsedTimeFetch) {
		$elapsedTime = 0;
		while ($elapsedTimeRow = $elapsedTimeFetch->fetch(PDO::FETCH_ASSOC)) {
			$stopTime = strtotime(date("m/d/Y g:i a",$elapsedTimeRow['stopped']));
			$startTime = strtotime(date("m/d/Y g:i a",$elapsedTimeRow['time']));
			$minutesPaused = round(abs($elapsedTimeRow['paused_counter']), 1);
			$viewedTime = round(abs($stopTime - $startTime - $minutesPaused), 0);

			$this->strLen = strlen($viewedTime);
			$elapsedTime += $viewedTime;
			$this->days = floor($elapsedTime / 86400);
			$this->hours = floor(($elapsedTime % 86400 ) / 3600);
			$this->minutes = floor(($elapsedTime % 3600 ) / 60);
			$this->plays++;
		}
	}
	public function outputHTML() {
		if (empty($this->strLen)) {
			echo '<h1> / </h1><h3>0</h3><p> mins</p>';
		} else if ($this->strLen == 10) {
			echo '';
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
		$this->plays = 0;
		$this->setElapsedTime($results);
	}
}

$plexWatchDbTable = dbTable('user');
$database = dbconnect();
$columns = 'time, stopped, paused_counter';
$params = array(':user'=>$_POST['user']);

$query = "SELECT $columns " .
	"FROM $plexWatchDbTable " .
	"WHERE datetime(stopped, 'unixepoch', 'localtime') >= date('now', 'localtime') " .
	"AND user = :user";
$results = getResults($database, $query, $params);
$dailyStats = new elapsedTimeResult($results);

$query = "SELECT $columns " .
	"FROM $plexWatchDbTable " .
	"WHERE datetime(stopped, 'unixepoch', 'localtime') >= datetime('now', '-7 days', 'localtime') " .
	"AND user = :user";
$results = getResults($database, $query, $params);
$weeklyStats = new elapsedTimeResult($results);

$query = "SELECT $columns " .
	"FROM $plexWatchDbTable " .
	"WHERE datetime(stopped, 'unixepoch', 'localtime') >= datetime('now', '-30 days', 'localtime') " .
	"AND user = :user";
$results = getResults($database, $query, $params);
$monthlyStats = new elapsedTimeResult($results);

$query = "SELECT $columns " .
	"FROM $plexWatchDbTable " .
	"WHERE user = :user";
$results = getResults($database, $query, $params);
$allTimeStats = new elapsedTimeResult($results);

echo"<ul>";
	echo "<div class='user-overview-stats-instance'>";
		echo "<li>";
			echo "<div class='user-overview-stats-instance-text'>";
				echo "<h4>Today</h4>";
				if ($dailyStats->plays == 1) {
					echo "<h3>".$dailyStats->plays."</h3><p>play</p>";
				} else {
					echo "<h3>".$dailyStats->plays."</h3><p>plays</p>";
				}
				$dailyStats->outputHTML();
			echo"</div>";
		echo "</li>";
	echo"</div>";
	echo "<div class='user-overview-stats-instance'>";
		echo "<li>";
			echo "<div class='user-overview-stats-instance-text'>";
				echo "<h4>Last week</h4>";
				if ($weeklyStats->plays == 1) {
					echo "<h3>".$weeklyStats->plays."</h3><p>play</p>";
				} else {
					echo "<h3>".$weeklyStats->plays."</h3><p>plays</p>";
				}
				$weeklyStats->outputHTML();
			echo"</div>";
		echo "</li>";
	echo"</div>";
	echo "<div class='user-overview-stats-instance'>";
		echo "<li>";
			echo "<div class='user-overview-stats-instance-text'>";
				echo "<h4>Last month</h4>";
				if ($monthlyStats->plays == 1) {
					echo "<h3>".$monthlyStats->plays."</h3><p>play</p>";
				} else {
					echo "<h3>".$monthlyStats->plays."</h3><p>plays</p>";
				}
				$monthlyStats->outputHTML();
			echo"</div>";
		echo "</li>";
	echo"</div>";
	echo "<div class='user-overview-stats-instance'>";
		echo "<li>";
			echo "<div class='user-overview-stats-instance-text'>";
				echo "<h4>All Time</h4>";
				if ($allTimeStats->plays == 1) {
					echo "<h3>".$allTimeStats->plays."</h3><p>play</p>";
				} else {
					echo "<h3>".$allTimeStats->plays."</h3><p>plays</p>";
				}
				$allTimeStats->outputHTML();
			echo"</div>";
		echo "</li>";
	echo"</div>";
echo"</ul>";
?>