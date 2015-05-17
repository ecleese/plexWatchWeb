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
		date_default_timezone_set(@date_default_timezone_get());

		echo "<div class='container-fluid'>";
			echo "<div class='row-fluid'>";
				echo "<div class='span12'>";
						echo "<div class='row-fluid'>";
							echo "<div class='span6'><div class='wellbg'><div class='history-charts-header'><strong>Hourly Plays </strong>(Last 24 Hours)<br></div><div class='history-charts-instance-chart' id='playChartHourly'></div></div></div>";
							echo "<div class='span6'><div class='wellbg'><div class='history-charts-header'><strong>Max Hourly Plays</strong><br></div><div class='history-charts-instance-chart' id='playChartMaxHourly'></div></div></div>";
						echo "</div>";
						echo "<div class='row-fluid'>";
							echo "<div class='span6'><div class='wellbg'><div class='history-charts-header'><strong>Daily Plays</strong><br></div><div class='history-charts-instance-chart'  id='playChartDaily'></div></div></div>";
							echo "<div class='span6'><div class='wellbg'><div class='history-charts-header'><strong>Monthly Plays</strong><br></div><div class='history-charts-instance-chart' id='playChartMonthly'></div></div></div>";
						echo "</div>";
				echo "</div>";
				$guisettingsFile = "config/config.php";
				if (file_exists($guisettingsFile)) {
					require_once(dirname(__FILE__) . '/config/config.php');
				} else {
					header("Location: settings.php");
				}

				$plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";

				if (!empty($plexWatch['myPlexAuthToken'])) {
					$myPlexAuthToken = $plexWatch['myPlexAuthToken'];
				} else {
					$myPlexAuthToken = '';
				}

				$db = dbconnect();
				$plexDBDie = "Failed to access plexWatch database. Please check settings.";

				$plexWatchDbTable = dbTable();
				$numRows = $db->querySingle("SELECT COUNT(*) as count FROM $plexWatchDbTable ");
				if ($plexWatchDbTable == "grouped") {
					$results = $db->query("SELECT title, user, platform, time, stopped, ip_address, xml, paused_counter FROM processed WHERE stopped IS NULL UNION ALL SELECT title, user, platform, time, stopped, ip_address, xml, paused_counter FROM $plexWatchDbTable ORDER BY time DESC") or die ($plexDBDie);
				} else if ($plexWatchDbTable == "processed") {
					$results = $db->query("SELECT title, user, platform, time, stopped, ip_address, xml, paused_counter FROM $plexWatchDbTable ORDER BY time DESC") or die ($plexDBDie);
				}

				$hourlyPlays = $db->query("SELECT strftime('%Y-%m-%d %H', datetime(time, 'unixepoch', 'localtime')) as date, COUNT(title) as count FROM $plexWatchDbTable WHERE datetime(time, 'unixepoch', 'localtime') >= datetime('now', '-24 hours', 'localtime') GROUP BY strftime('%Y-%m-%d %H', datetime(time, 'unixepoch', 'localtime')) ORDER BY date ASC;") or die ($plexDBDie);
				$hourlyPlaysNum = 0;
				$hourlyPlayFinal = '';
				while ($hourlyPlay = $hourlyPlays->fetchArray()) {
					$hourlyPlaysNum++;
					$hourlyPlayDate[$hourlyPlaysNum] = $hourlyPlay['date'];
					$hourlyPlayCount[$hourlyPlaysNum] = $hourlyPlay['count'];
					$hourlyPlayTotal = "{ \"x\": \"".$hourlyPlayDate[$hourlyPlaysNum]."\", \"y\": ".$hourlyPlayCount[$hourlyPlaysNum]." }, ";
					$hourlyPlayFinal .= $hourlyPlayTotal;
				}

				$maxhourlyPlays = $db->query("SELECT strftime('%Y-%m-%d %H', datetime(time, 'unixepoch', 'localtime')) as date, COUNT(title) as count FROM $plexWatchDbTable GROUP BY strftime('%Y-%m-%d %H', datetime(time, 'unixepoch', 'localtime')) ORDER BY count(*) desc limit 25;") or die ($plexDBDie);
				$maxhourlyPlaysNum = 0;
				$maxhourlyPlayFinal = '';
				while ($maxhourlyPlay = $maxhourlyPlays->fetchArray()) {
					$maxhourlyPlaysNum++;
					$maxhourlyPlayDate[$maxhourlyPlaysNum] = $maxhourlyPlay['date'];
					$maxhourlyPlayCount[$maxhourlyPlaysNum] = $maxhourlyPlay['count'];
					$maxhourlyPlayTotal = "{ \"x\": \"".$maxhourlyPlayDate[$maxhourlyPlaysNum]."\", \"y\": ".$maxhourlyPlayCount[$maxhourlyPlaysNum]." }, ";
					$maxhourlyPlayFinal .= $maxhourlyPlayTotal;
				}

				$dailyPlays = $db->query("SELECT date(time, 'unixepoch','localtime') as date, count(title) as count FROM $plexWatchDbTable GROUP BY date ORDER BY time DESC LIMIT 30") or die ($plexDBDie);
				$dailyPlaysNum = 0;
				$dailyPlayFinal = '';
				while ($dailyPlay = $dailyPlays->fetchArray()) {
					$dailyPlaysNum++;
					$dailyPlayDate[$dailyPlaysNum] = $dailyPlay['date'];
					$dailyPlayCount[$dailyPlaysNum] = $dailyPlay['count'];
					$dailyPlayTotal = "{ \"x\": \"".$dailyPlayDate[$dailyPlaysNum]."\", \"y\": ".$dailyPlayCount[$dailyPlaysNum]." }, ";
					$dailyPlayFinal .= $dailyPlayTotal;
				}

				$monthlyPlays = $db->query("SELECT strftime('%Y-%m', datetime(time, 'unixepoch', 'localtime')) as date, COUNT(title) as count FROM $plexWatchDbTable WHERE datetime(time, 'unixepoch', 'localtime') >= datetime('now', '-12 months', 'localtime') GROUP BY strftime('%Y-%m', datetime(time, 'unixepoch', 'localtime'))  ORDER BY date DESC LIMIT 13;") or die ($plexDBDie);
				$monthlyPlaysNum = 0;
				$monthlyPlayFinal = '';
				while ($monthlyPlay = $monthlyPlays->fetchArray()) {
					$monthlyPlaysNum++;
					$monthlyPlayDate[$monthlyPlaysNum] = $monthlyPlay['date'];
					$monthlyPlayCount[$monthlyPlaysNum] = $monthlyPlay['count'];
					$monthlyPlayTotal = "{ \"x\": \"".$monthlyPlayDate[$monthlyPlaysNum]."\", \"y\": ".$monthlyPlayCount[$monthlyPlaysNum]." }, ";
					$monthlyPlayFinal .= $monthlyPlayTotal;
				}
				?>
			</div>
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

			var data = {
				"xScale": "ordinal",
				"yScale": "linear",
				"main": [{
					"className": ".playChartHourly",
					"data": [<?php echo $hourlyPlayFinal ?>]
				}]
			};
			var opts = {
				"dataFormatX": function (x) { return d3.time.format('%Y-%m-%d %H').parse(x); },
				"tickFormatX": function (x) { return d3.time.format('%-I:00 %p')(x); },
				"paddingLeft": ('35'),
				"paddingRight": ('35'),
				"paddingTop": ('10'),
				"tickHintY": ('5'),
				"mouseover": function (d, i) {
					var pos = $(this).offset();
					$(tt).text(d3.time.format('%-I:00 %p')(d.x) + ': ' + d.y + ' play(s)')
						.css({top: topOffset + pos.top, left: pos.left + leftOffset})
						.show();
				},
				"mouseout": function (x) {
					$(tt).hide();
				}
			};
			var myChart = new xChart('line-dotted', data, '#playChartHourly', opts);
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
				"main": [{
					"className": ".maxplayChartHourly",
					"data": [<?php echo $maxhourlyPlayFinal ?>]
				}]
			};
			var opts = {
				"dataFormatX": function (x) { return d3.time.format('%Y-%m-%d %H').parse(x); },
				"tickFormatX": function (x) { return d3.time.format('%b %e')(x); },
				"paddingLeft": ('35'),
				"paddingRight": ('35'),
				"paddingTop": ('10'),
				"tickHintY": ('5'),
				"mouseover": function (d, i) {
					var pos = $(this).offset();
					$(tt).text(d3.time.format('%-I:00 %p')(d.x) + ': ' + d.y + ' play(s)')
						.css({top: topOffset + pos.top, left: pos.left + leftOffset})
						.show();
				},
				"mouseout": function (x) {
					$(tt).hide();
				}
			};
			var myChart = new xChart('bar', data, '#playChartMaxHourly', opts);
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
				"main": [{
					"className": ".playcount",
					"data": [<?php echo $dailyPlayFinal ?>]
				}]
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
					$(tt).text(d3.time.format('%b %e')(d.x) + ': ' + d.y + ' play(s)')
						.css({top: topOffset + pos.top, left: pos.left + leftOffset})
						.show();
				},
				"mouseout": function (x) {
					$(tt).hide();
				}
			};
			var myChart = new xChart('bar', data, '#playChartDaily', opts);
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
				"main": [{
					"className": ".playcount",
					"data": [<?php echo $monthlyPlayFinal ?>]
				}]
			};
			var opts = {
				"dataFormatX": function (x) { return d3.time.format('%Y-%m').parse(x); },
				"tickFormatX": function (x) { return d3.time.format('%b %Y')(x); },
				"paddingLeft": ('35'),
				"paddingRight": ('35'),
				"paddingTop": ('10'),
				"tickHintY": ('5'),
				"mouseover": function (d, i) {
					var pos = $(this).offset();
					$(tt).text(d3.time.format('%b')(d.x) + ': ' + d.y + ' play(s)')
						.css({top: topOffset + pos.top, left: pos.left + leftOffset})
						.show();
				},
				"mouseout": function (x) {
					$(tt).hide();
				}
			};
			var myChart = new xChart('line-dotted', data, '#playChartMonthly', opts);
		</script>
	</body>
</html>
