<?php
date_default_timezone_set(@date_default_timezone_get());

$guisettingsFile = dirname(__FILE__) . '/config/config.php';
if (file_exists($guisettingsFile)) {
	require_once($guisettingsFile);
} else {
	header('Location: settings.php');
	return;
}

$plexWatchPmsUrl = 'http://' . $plexWatch['pmsIp'] . ':' .
	$plexWatch['pmsHttpPort'];

if (!empty($plexWatch['myPlexAuthToken'])) {
	$myPlexAuthToken = '?X-Plex-Token=' . $plexWatch['myPlexAuthToken'];
} else {
	$myPlexAuthToken = '';
}

if ($fileContents = file_get_contents($plexWatchPmsUrl .
		'/status/sessions' . $myPlexAuthToken)) {
	$msg = 'Failed to access Plex Media Server. Please check your settings.';
	$statusSessions = simplexml_load_string($fileContents) or
		trigger_error($msg, E_USER_ERROR);
}

$database = dbconnect();
$plexWatchDbTable = dbTable('charts');
$columns = "title,time,orig_title,orig_title_ep,episode," .
	"season,xml,COUNT(*) AS play_count ";

function printTop10($query, $type = null) {
	global $database;
	$results = $database->query($query);
	if ($results === false) {
		$msg = 'There was a problem running "' . $query . '".';
		trigger_error($msg, E_USER_ERROR);
	}
	$imgSize = '&width=100&height=149';

	// Run through each feed item
	$num_rows = 0;
	while ($row = $results->fetchArray()) {
		$num_rows++;
		$xml = simplexml_load_string($row['xml']);
		if ($xml['type'] == 'movie') {
			$imgUrl = urlencode($xml['thumb'] . $imgSize);
			$title = $row['title'] . ' (' . $xml['year'] . ')';
			$key = $xml['ratingKey'];
		} else {
			$imgUrl = urlencode($xml['grandparentThumb'] . $imgSize);
			switch ($type) {
				case 'shows':
					$key = $xml['grandparentRatingKey'];
					$title = $row['orig_title'];
					break;
				case 'episodes':
					$imgUrl = urlencode($xml['parentThumb'] . $imgSize);
				default: // All time
					$title = $row['orig_title'] . ' - Season ' . $row['season'] . ', ' .
						'Episode ' . $row['episode'];
					$key = $xml['ratingKey'];
					break;
			}
		}

		echo '<div class="charts-instance-wrapper">';
			echo '<div class="charts-instance-position-circle">';
				echo '<h1>' . $num_rows . '</h1>';
			echo '</div>';
			echo '<div class="charts-instance-poster">';
				echo '<img src="includes/img.php?img='.$imgUrl.'">';
			echo '</div>';
			echo '<div class="charts-instance-position-title">';
				echo "<li>";
					echo '<h3>';
						echo '<a href="info.php?id='. $key .'">';
							echo $title;
						echo '</a>';
					echo '</h3>';
					echo '<h5>';
						echo '(' . $row['play_count'] . ' views)';
					echo '</h5>';
				echo '</li>';
			echo '</div>';
		echo '</div>';
	}
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>plexWatch</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">

		<!-- css -->
		<link href="css/plexwatch.css" rel="stylesheet">
		<link href="css/font-awesome.min.css" rel="stylesheet" >
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
					<a href="index.php"><div class="logo hidden-phone"></div></a>
					<ul class="nav">
						<li><a href="index.php"><i class="icon-2x icon-home icon-white" data-toggle="tooltip" data-placement="bottom" title="Home" id="home"></i></a></li>
						<li><a href="history.php"><i class="icon-2x icon-calendar icon-white" data-toggle="tooltip" data-placement="bottom" title="History" id="history"></i></a></li>
						<li><a href="stats.php"><i class="icon-2x icon-tasks icon-white" data-toggle="tooltip" data-placement="bottom" title="Stats" id="stats"></i></a></li>
						<li><a href="users.php"><i class="icon-2x icon-group icon-white" data-toggle="tooltip" data-placement="bottom" title="Users" id="users"></i></a></li>
						<li class="active"><a href="charts.php"><i class="icon-2x icon-bar-chart icon-white" data-toggle="tooltip" data-placement="bottom" title="Charts" id="charts"></i></a></li>
						<li><a href="settings.php"><i class="icon-2x icon-wrench icon-white" data-toggle="tooltip" data-placement="bottom" title="Settings" id="settings"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="container-fluid">
			<div class='row-fluid'>
				<div class='span12'>
				</div>
			</div>
		</div>
		<div class="container-fluid">
			<div class='row-fluid'>
				<div class="span12">
					<div class='wellheader'>
						<div class='dashboard-wellheader-no-chevron'>
							<h2><i class="icon-large icon-bar-chart icon-white"></i> Charts</h2>
						</div>
					</div>
				</div>
			</div>
			<div class='row-fluid'>
				<div class="span12">
					<?php
					echo '<div class="span3">';
						echo '<div class="wellbg">';
							echo '<div class="wellheader">';
								echo '<div class="dashboard-wellheader">';
									echo '<h4>Top 10 (All Time)</h4>';
								echo '</div>';
							echo '</div>';
							echo '<div class="charts-wrapper">';
								echo '<ul>';
									$top10Query = "SELECT $columns" .
										"FROM $plexWatchDbTable " .
										"GROUP BY title " .
										"HAVING play_count > 0 " .
										"ORDER BY play_count DESC, time DESC " .
										"LIMIT 10;";
									printTop10($top10Query);
								echo '</ul>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
					echo '<div class="span3">';
						echo '<div class="wellbg">';
							echo '<div class="wellheader">';
								echo '<div class="dashboard-wellheader">';
									echo '<h4>Top 10 Films (All Time)</h4>';
								echo '</div>';
							echo '</div>';
							echo '<div class="charts-wrapper">';
								echo '<ul>';
								$top10MovieQuery = "SELECT $columns" .
									"FROM $plexWatchDbTable " .
									"WHERE xml LIKE '%type=\"movie\"%'" .
									"GROUP BY title " .
									"HAVING play_count > 0 " .
									"ORDER BY play_count DESC, time DESC " .
									"LIMIT 10;";
								printTop10($top10MovieQuery);
								echo '</ul>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
					echo '<div class="span3">';
						echo '<div class="wellbg">';
							echo '<div class="wellheader">';
								echo '<div class="dashboard-wellheader">';
									echo '<h4>Top 10 TV Shows (All Time)</h4>';
								echo '</div>';
							echo '</div>';
							echo '<div class="charts-wrapper">';
								echo '<ul>';
									$top10ShowsQuery = "SELECT $columns" .
										"FROM $plexWatchDbTable " .
										"WHERE xml LIKE '%type=\"episode\"%'" .
										"GROUP BY orig_title " .
										"HAVING play_count > 0 " .
										"ORDER BY play_count DESC, time DESC " .
										"LIMIT 10;";
									printTop10($top10ShowsQuery, 'shows');
								echo '</ul>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
					echo '<div class="span3">';
						echo '<div class="wellbg">';
							echo '<div class="wellheader">';
								echo '<div class="dashboard-wellheader">';
									echo '<h4>Top 10 TV Episodes (All Time)</h4>';
								echo '</div>';
							echo '</div>';
							echo '<div class="charts-wrapper">';
								echo '<ul>';
									$top10EpisodesQuery = "SELECT $columns" .
										"FROM $plexWatchDbTable " .
										"WHERE xml LIKE '%type=\"episode\"%'" .
										"GROUP BY title " .
										"HAVING play_count > 0 " .
										"ORDER BY play_count DESC, time DESC " .
										"LIMIT 10;";
									printTop10($top10EpisodesQuery, 'episodes');
								echo '</ul>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
					?>
				</div>
			</div><!--/.fluid-row-->
			<footer>
			</footer>
		</div><!--/.fluid-container-->

		<!-- javascript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="js/jquery-2.0.3.js"></script>
		<script src="js/bootstrap.js"></script>
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