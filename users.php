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

	<div class="clear"></div>
		
	
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<div class='wellheader'>
					<div class="dashboard-wellheader-no-chevron">
						<h2><i class="icon-large icon-group icon-white"></i> Users</h2>
					</div>
				</div>

				<?php
						$guisettingsFile = "config/config.php";
						
						if (file_exists($guisettingsFile)) { 
							require_once(dirname(__FILE__) . '/config/config.php');
						}else{
							header("Location: settings.php");
						}
							
						if ($plexWatch['https'] == 'yes') {
							$plexWatchPmsUrl = "https://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpsPort']."";
						}else if ($plexWatch['https'] == 'no') {
							$plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";
						}	
						
						if ($plexWatch['myPlexAuthToken'] != '') {
								$myPlexAuthToken = $plexWatch['myPlexAuthToken'];
								
							}else{
								$myPlexAuthToken = '';
								
						}
						
							
						date_default_timezone_set(@date_default_timezone_get());

						$db = dbconnect();
						
						if ($plexWatch['userHistoryGrouping'] == "yes") {
							$plexWatchDbTable = "grouped";
						}else if ($plexWatch['userHistoryGrouping'] == "no") {
							$plexWatchDbTable = "processed";
						}
						
						$users = $db->query("SELECT COUNT(title) as plays, user, time, SUM(time) as timeTotal, SUM(stopped) as stoppedTotal, SUM(paused_counter) as paused_counterTotal, platform, ip_address, xml FROM ".$plexWatchDbTable." GROUP BY user ORDER BY user COLLATE NOCASE") or die ("Failed to access plexWatch database. Please check your settings.");
					
$dailyPlays = $db->query("SELECT user, time,stopped,paused_counter, count(title) as count FROM $plexWatchDbTable WHERE datetime(stopped, 'unixepoch', 'localtime') >= date('now', '-24 hours', 'localtime') GROUP BY user ORDER BY count DESC LIMIT 30") or die ("Failed to access plexWatch database. Please check your settings.");
					$dailyPlaysNum = 0;
					$dailyPlayFinal = '';
					while ($dailyPlay = $dailyPlays->fetchArray()) {
						$dailyPlaysNum++;
						$dailyPlayUser[$dailyPlaysNum] = $dailyPlay['user'];
						$dailyPlayCount[$dailyPlaysNum] = $dailyPlay['count'];
						$dailyPlayTotal = "{ \"x\": \"".$dailyPlayUser[$dailyPlaysNum]."\", \"y\": ".$dailyPlayCount[$dailyPlaysNum]." }, ";
						$dailyPlayFinal .= $dailyPlayTotal;
					}
					
	
					$weeklyPlays = $db->query("SELECT user, count(title) as count FROM $plexWatchDbTable WHERE datetime(stopped, 'unixepoch', 'localtime') >= datetime('now', '-7 days', 'localtime') GROUP BY user ORDER BY count DESC LIMIT 30") or die ("Failed to access plexWatch database. Please check your settings.");
					$weeklyPlaysNum = 0;
					$weeklyPlayFinal = '';
					while ($weeklyPlay = $weeklyPlays->fetchArray()) {
						$weeklyPlaysNum++;
						$weeklyPlayUser[$weeklyPlaysNum] = $weeklyPlay['user'];
						$weeklyPlayCount[$weeklyPlaysNum] = $weeklyPlay['count'];
						$weeklyPlayTotal = "{ \"x\": \"".$weeklyPlayUser[$weeklyPlaysNum]."\", \"y\": ".		$weeklyPlayCount[$weeklyPlaysNum]." }, ";
						$weeklyPlayFinal .= $weeklyPlayTotal;
					}
							

					$monthlyPlays = $db->query("SELECT user, count(title) as count FROM $plexWatchDbTable WHERE datetime(stopped, 'unixepoch', 'localtime') >= datetime('now', '-30 days', 'localtime') GROUP BY user ORDER BY count DESC LIMIT 30") or die ("Failed to access plexWatch database. Please check your settings.");
					$monthlyPlaysNum = 0;
					$monthlyPlayFinal = '';
					while ($monthlyPlay = $monthlyPlays->fetchArray()) {
						$monthlyPlaysNum++;
						$monthlyPlayUser[$monthlyPlaysNum] = $monthlyPlay['user'];
						$monthlyPlayCount[$monthlyPlaysNum] = $monthlyPlay['count'];
						$monthlyPlayTotal = "{ \"x\": \"".$monthlyPlayUser[$monthlyPlaysNum]."\", \"y\": ".		$monthlyPlayCount[$monthlyPlaysNum]." }, ";
						$monthlyPlayFinal .= $monthlyPlayTotal;
					}
							
							echo "<div class='wellbg'>";
							
							echo "<table id='usersTable' class='display'>";
								echo "<thead>";
									echo "<tr>";
										echo "<th align='left'>User </th>";
										echo "<th align='left'>Last Seen </th>";
										echo "<th align='left'>Last Known IP </th>";
										echo "<th align='left'>Total Plays</th>";
										
									echo "</tr>";
								echo "</thead>";
								echo "<tbody>";
							
								// Run through each feed item
								while ($user = $users->fetchArray()) {
							
									$userXml = simplexml_load_string($user['xml']) ;                         

										echo "<tr>";
											echo "<td>";
											if (empty($userXml->User['thumb'])) {				
															echo "<div class='users-poster-face'><a href='user.php?user=".$user['user']."'><img src='images/gravatar-default-80x80.png' width='40px' height='40px'></></a> </div>";
														}else if (strstr($userXml->User['thumb'], "?d=404")) {
															echo "<div class='users-poster-face'><a href='user.php?user=".$user['user']."'><img src='images/gravatar-default-80x80.png' width='40px' height='40px'></></a> </div>";
														}else{
															echo "<div class='users-poster-face'><a href='user.php?user=".$user['user']."'><img src='".$userXml->User['thumb']."' width='40px' height='40px'></></a> </div>";
														}

											
												echo "<div class='users-name'><a href='user.php?user=".$user['user']."'> ".FriendlyName($user['user'],$user['platform'])."</a> </div>";
											echo "</td>";
														
											require_once(dirname(__FILE__) . '/includes/timeago.php');
											$lastSeenTime = $user['time'];

											echo "<td>".TimeAgo($lastSeenTime)."</td>";
														
											echo "<td>".$user['ip_address']."</td>";
													
											echo "<td>".$user['plays']."</td>";
					
											
																		
										echo "</tr>";   
												
								}
								
								echo "</tbody>";
								echo "</table>";
						
					
					?>
						</div>
				
			</div>
		</div><!--/.fluid-row-->			
			
	<div class='wellbg'><strong>Plays per User (Last 24 Hours)</strong><br><figure style='width: 98%; height: 200px;' id='playChartDaily'></figure></div>

	<div class='wellbg'><strong>Plays per User (Last 7 Days)</strong><br><figure style='width: 98%; height: 200px;' id='playChartWeekly'></figure></div>

	<div class='wellbg'><strong>Plays per User (Last 30 Days)</strong><br><figure style='width: 98%; height: 200px;' id='playChartMonthly'></figure></div>
			

		<footer>
		
		</footer>
		
    </div><!--/.fluid-container-->
    
    <!-- javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-2.0.3.js"></script>
	<script src="js/bootstrap.js"></script>
	<script src="js/jquery.dataTables.js"></script>
	<script src="js/jquery.dataTables.plugin.bootstrap_pagination.js"></script>
	<script src="js/d3.v3.js"></script> 
	<script src="js/xcharts.min.js"></script> 
	<script>
		$(document).ready(function() {
			var oTable = $('#usersTable').dataTable( {
				"bPaginate": false,
				"bLengthChange": true,
				"bFilter": false,
				"bSort": false,
				"bInfo": true,
				"bAutoWidth": true,
				"aaSorting": [[ 0, "asc" ]],
				"bStateSave": false,
				"bSortClasses": true,
				"sPaginationType": "bootstrap"	
			} );
		} );
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
			<?php echo $dailyPlayFinal ?>
		  ]
		}
	  ]
	};
	var opts = {
	  "paddingLeft": ('25'),
	  "paddingRight": ('35'),
	  "paddingTop": ('10'),
	  "tickHintY": ('10'),
	  "mouseover": function (d, i) {
		var pos = $(this).offset();
		//$(tt).text(d3.time.format('%b %e')(d.x) + ': ' + d.y + ' play(s)')
		$(tt).text(+ d.y + ' play(s)')
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
	  "main": [
		{
		  "className": ".playcount",
		  "data": [
			<?php echo $weeklyPlayFinal ?>
		  ]
		}
	  ]
	};
	var opts = {
	  "paddingLeft": ('25'),
	  "paddingRight": ('35'),
	  "paddingTop": ('10'),
	  "tickHintY": ('10'),
	  "mouseover": function (d, i) {
		var pos = $(this).offset();
		//$(tt).text(d3.time.format('%b %e')(d.x) + ': ' + d.y + ' play(s)')
		$(tt).text(+ d.y + ' play(s)')
		  .css({top: topOffset + pos.top, left: pos.left + leftOffset})
		  .show();
	  },
	  "mouseout": function (x) {
		$(tt).hide();
	  }
	};
	var myChart = new xChart('bar', data, '#playChartWeekly', opts);
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
			<?php echo $monthlyPlayFinal ?>
		  ]
		}
	  ]
	};
	var opts = {
	  "paddingLeft": ('25'),
	  "paddingRight": ('35'),
	  "paddingTop": ('10'),
	  "tickHintY": ('10'),
	  "mouseover": function (d, i) {
		var pos = $(this).offset();
		//$(tt).text(d3.time.format('%b %e')(d.x) + ': ' + d.y + ' play(s)')
		$(tt).text(+ d.y + ' play(s)')
		  .css({top: topOffset + pos.top, left: pos.left + leftOffset})
		  .show();
	  },
	  "mouseout": function (x) {
		$(tt).hide();
	  }
	};
	var myChart = new xChart('bar', data, '#playChartMonthly', opts);
	</script>

  </body>
</html>
