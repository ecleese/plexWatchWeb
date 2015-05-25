<?php
date_default_timezone_set(@date_default_timezone_get());

$guisettingsFile = dirname(__FILE__) . '/config/config.php';
if (file_exists($guisettingsFile)) {
	require_once($guisettingsFile);
} else {
	header("Location: settings.php");
	return; // End execution of the current script.
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

		<!-- Le styles -->
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
		<div class="container">
			<div class="navbar navbar-fixed-top">
				<div class="navbar-inner">
					<a href="index.php"><div class="logo hidden-phone"></div></a>
					<ul class="nav">
						<li><a href="index.php"><i class="icon-2x icon-home icon-white" data-toggle="tooltip" data-placement="bottom" title="Home" id="home"></i></a></li>
						<li><a href="history.php"><i class="icon-2x icon-calendar icon-white" data-toggle="tooltip" data-placement="bottom" title="History" id="history"></i></a></li>
						<li class="active"><a href="stats.php"><i class="icon-2x icon-tasks icon-white" data-toggle="tooltip" data-placement="bottom" title="Stats" id="stats"></i></a></li>
						<li><a href="users.php"><i class="icon-2x icon-group icon-white" data-toggle="tooltip" data-placement="bottom" title="Users" id="users"></i></a></li>
						<li><a href="charts.php"><i class="icon-2x icon-bar-chart icon-white" data-toggle="tooltip" data-placement="bottom" title="Charts" id="charts"></i></a></li>
						<li><a href="settings.php"><i class="icon-2x icon-wrench icon-white" data-toggle="tooltip" data-placement="bottom" title="Settings" id="settings"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span12">
					<div class='wellheader'>
						<div class="dashboard-wellheader-no-chevron">
							<h2><i class="icon-large icon-tasks icon-white"></i> Statistics</h2>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		echo '<div class="container-fluid">';
			echo '<div class="row-fluid">';
				echo '<div class="span12">';
					echo '<div class="row-fluid">';
						echo '<div class="span6">';
							echo '<div class="wellbg">';
								echo '<div class="history-charts-header">';
									echo '<strong>Hourly Plays </strong>(Last 24 Hours)<br>';
								echo '</div>';
								echo '<div class="history-charts-instance-chart" id="playChartHourly"></div>';
							echo '</div>';
						echo '</div>';
						echo '<div class="span6">';
							echo '<div class="wellbg">';
								echo '<div class="history-charts-header">';
									echo '<strong>Max Hourly Plays</strong><br>';
								echo '</div>';
								echo '<div class="history-charts-instance-chart" id="playChartMaxHourly"></div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
					echo '<div class="row-fluid">';
						echo '<div class="span6">';
							echo '<div class="wellbg">';
								echo '<div class="history-charts-header">';
									echo '<strong>Daily Plays</strong><br>';
								echo '</div>';
								echo '<div class="history-charts-instance-chart" id="playChartDaily"></div>';
							echo '</div>';
						echo '</div>';
						echo '<div class="span6">';
							echo '<div class="wellbg">';
								echo '<div class="history-charts-header">';
									echo '<strong>Monthly Plays</strong><br>';
								echo '</div>';
								echo '<div class="history-charts-instance-chart" id="playChartMonthly">';
							echo '</div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';

			$db = dbconnect();
			$plexDBDie = 'Failed to access plexWatch database. Please check settings.';
			$plexWatchDbTable = dbTable();

			$query = "SELECT " .
					"strftime('%Y-%m-%d %H', datetime(time, 'unixepoch', 'localtime')) as date, " .
					"COUNT(title) as count ".
				"FROM $plexWatchDbTable " .
				"WHERE datetime(time, 'unixepoch', 'localtime') >= " .
					"datetime('now', '-24 hours', 'localtime') " .
				"GROUP BY strftime('%Y-%m-%d %H', datetime(time, 'unixepoch', 'localtime')) " .
				"ORDER BY date ASC;";
			$hourlyPlays = $db->query($query) or trigger_error($plexDBDie, E_USER_ERROR);
			$hourlyPlayData = [];
			while ($row = $hourlyPlays->fetchArray()) {
				$hourlyPlayData[] = ["x"=>$row['date'], "y"=>$row['count']];
			}

			$query = "SELECT " .
					"strftime('%Y-%m-%d %H', datetime(time, 'unixepoch', 'localtime')) as date, " .
					"COUNT(title) as count " .
				"FROM $plexWatchDbTable " .
				"GROUP BY strftime('%Y-%m-%d %H', datetime(time, 'unixepoch', 'localtime')) " .
				"ORDER BY count(*) desc " .
				"LIMIT 25;";
			$maxhourlyPlays = $db->query($query) or trigger_error($plexDBDie, E_USER_ERROR);
			$maxhourlyPlayData = [];
			while ($row = $maxhourlyPlays->fetchArray()) {
				$maxhourlyPlayData[] = ["x"=>$row['date'], "y"=>$row['count']];
			}

			$query = "SELECT " .
					"date(time, 'unixepoch','localtime') as date, " .
					"COUNT(title) as count " .
				"FROM $plexWatchDbTable " .
				"GROUP BY date " .
				"ORDER BY time DESC " .
				"LIMIT 30;";
			$dailyPlays = $db->query($query) or trigger_error($plexDBDie, E_USER_ERROR);
			$dailyPlayData = [];
			while ($row = $dailyPlays->fetchArray()) {
				$dailyPlayData[] = ["x"=>$row['date'], "y"=>$row['count']];
			}

			$query = "SELECT " .
					"strftime('%Y-%m', datetime(time, 'unixepoch', 'localtime')) as date, " .
					"COUNT(title) as count " .
				"FROM $plexWatchDbTable " .
				"WHERE datetime(time, 'unixepoch', 'localtime') >= " .
					"datetime('now', '-12 months', 'localtime') " .
				"GROUP BY strftime('%Y-%m', datetime(time, 'unixepoch', 'localtime')) " .
				"ORDER BY date DESC " .
				"LIMIT 13;";
			$monthlyPlays = $db->query($query) or trigger_error($plexDBDie, E_USER_ERROR);
			$monthlyPlayData = [];
			while ($row = $monthlyPlays->fetchArray()) {
				$monthlyPlayData[] = ["x"=>$row['date'], "y"=>$row['count']];
			}
			?>
		</div>
		<footer></footer>

		<!-- javascript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="js/jquery-2.0.3.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/jquery.dataTables.js"></script>
		<script src="js/jquery.dataTables.plugin.bootstrap_pagination.js"></script>
		<script src="js/jquery.dataTables.plugin.date_sorting.js"></script>
		<script src="js/d3.v3.js"></script>
		<script src="js/xcharts.min.js"></script>
		<script src="js/moment-with-locale.js"></script>
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
		<script>
			var tt = document.createElement('div'),
				leftOffset = -(~~$('html').css('padding-left').replace('px', '') + ~~$('body').css('margin-left').replace('px', '')),
				topOffset = -35;
			tt.className = 'ex-tooltip';
			document.body.appendChild(tt);

			function ttText(d, i, fmt, obj) {
				var pos = $(obj).offset();
				$(tt).text(moment(d.x).format(fmt) + ': ' +
						d.y + ' play' + (d.y > 1 ? 's' : ''))
					.css({top: topOffset + pos.top, left: pos.left + leftOffset})
					.show();
			}

			var hourlyData = {
				"xScale": "ordinal",
				"yScale": "linear",
				"main": [{
					"className": ".playChartHourly",
					"data": JSON.parse('<?php echo json_encode($hourlyPlayData); ?>')
				}]
			};
			var hourlyOpts = {
				"dataFormatX": function (x) {
					return d3.time.format('%Y-%m-%d %H').parse(x);
				},
				"tickFormatX": function (x) {
					return moment(x).format('<?php echo $plexWatch['timeFormat']; ?>');
				},
				"paddingLeft": ('35'),
				"paddingRight": ('35'),
				"paddingTop": ('10'),
				"tickHintY": ('5'),
				"mouseover": function(d, i) {
					ttText(d, i, '<?php echo $plexWatch['timeFormat']; ?>', this);
				},
				"mouseout": function (x) {
					$(tt).hide();
				}
			};
			var myChart = new xChart('line-dotted', hourlyData, '#playChartHourly', hourlyOpts);

			var maxHourlyData = {
				"xScale": "ordinal",
				"yScale": "linear",
				"main": [{
					"className": ".maxplayChartHourly",
					"data": JSON.parse('<?php echo json_encode($maxhourlyPlayData); ?>')
				}]
			};
			var maxHourlyOpts = {
				"dataFormatX": function (x) {
					return d3.time.format('%Y-%m-%d %H').parse(x);
				},
				"tickFormatX": function (x) {
					return moment(x).format('MMM D');
				},
				"paddingLeft": ('35'),
				"paddingRight": ('35'),
				"paddingTop": ('10'),
				"tickHintY": ('5'),
				"mouseover": function(d, i) {
					ttText(d, i, '<?php echo $plexWatch['timeFormat']; ?>', this);
				},
				"mouseout": function (x) {
					$(tt).hide();
				}
			};
			var myChart = new xChart('bar', maxHourlyData, '#playChartMaxHourly', maxHourlyOpts);

			var dailyPlayData = {
				"xScale": "ordinal",
				"yScale": "linear",
				"main": [{
					"className": ".playcount",
					"data": JSON.parse('<?php echo json_encode($dailyPlayData); ?>')
				}]
			};
			var dailyPlayOpts = {
				"dataFormatX": function (x) {
					return d3.time.format('%Y-%m-%d').parse(x);
				},
				"tickFormatX": function (x) {
					return moment(x).format('MMM D');
				},
				"paddingLeft": ('35'),
				"paddingRight": ('35'),
				"paddingTop": ('10'),
				"tickHintY": ('5'),
				"mouseover": function(d, i) {
					ttText(d, i, 'MMM D', this);
				},
				"mouseout": function (x) {
					$(tt).hide();
				}
			};
			var myChart = new xChart('bar', dailyPlayData, '#playChartDaily', dailyPlayOpts);

			var monthlyData = {
				"xScale": "ordinal",
				"yScale": "linear",
				"main": [{
					"className": ".playcount",
					"data": JSON.parse('<?php echo json_encode($monthlyPlayData); ?>')
				}]
			};
			var monthlyOpts = {
				"dataFormatX": function (x) {
					return d3.time.format('%Y-%m').parse(x);
				},
				"tickFormatX": function (x) {
					return moment(x).format('MMM YYYY');
				},
				"paddingLeft": ('35'),
				"paddingRight": ('35'),
				"paddingTop": ('10'),
				"tickHintY": ('5'),
				"mouseover": function(d, i) {
					ttText(d, i, 'MMM', this);
				},
				"mouseout": function (x) {
					$(tt).hide();
				}
			};
			var myChart = new xChart('line-dotted', monthlyData, '#playChartMonthly', monthlyOpts);
		</script>
	</body>
</html>