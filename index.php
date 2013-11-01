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
				<a href="index.php"><div class="logo"></div></a>
				<ul class="nav">
					
					<li class="active"><a href="index.php"><i class="icon-2x icon-home icon-white" data-toggle="tooltip" data-placement="bottom" title="Home" id="home"></i></a></li>
					<li><a href="history.php"><i class="icon-2x icon-calendar icon-white" data-toggle="tooltip" data-placement="bottom" title="History" id="history"></i></a></li>
					<li><a href="users.php"><i class="icon-2x icon-group icon-white" data-toggle="tooltip" data-placement="bottom" title="Users" id="users"></i></a></li>
					<li><a href="charts.php"><i class="icon-2x icon-bar-chart icon-white" data-toggle="tooltip" data-placement="bottom" title="Charts" id="charts"></i></a></li>
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
		<div class='row-fluid'>
			<div class='span12'>
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
				
				if (!empty($plexWatch['myPlexAuthToken'])) {
					$myPlexAuthToken = $plexWatch['myPlexAuthToken'];
					$statusSessions = simplexml_load_file("".$plexWatchPmsUrl."/status/sessions?query=c&X-Plex-Token=".$myPlexAuthToken."") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");

				}else{
					$myPlexAuthToken = '';
					$statusSessions = simplexml_load_file("".$plexWatchPmsUrl."/status/sessions") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");

				}	
				
				
					echo "<div class='wellbg'>";
						echo "<div class='wellheader'>";
						echo "<div class='dashboard-wellheader'>";
							echo "<h3>Plex Status</h3>";
						echo "</div>";
					echo "</div>";
						
					echo "<div class='dashboard-status-wrapper'>";
							// Let's check Plex Media Server ports
							$pmsHttp = fsockopen($plexWatch['pmsIp'], $plexWatch['pmsHttpPort']);
							$pmsHttps = fsockopen($plexWatch['pmsIp'], $plexWatch['pmsHttpsPort']);
							$myplexUrl = fsockopen ('my.plexapp.com', 443);

							if ($pmsHttp) {
								$statusPmsHttp = "<h5>Plex Media Server (HTTP):  <span class='label label-warning'>Online</span></h5><br>";
							}

							else {
								$statusPmsHttp = "<h5>Plex Media Server (HTTP):  <span class='label label-important'>Offline</span></h5><br>";
							}

							if ($pmsHttps) {
								$statusPmsHttps = "<h5>Plex Media Server (HTTPS):  <span class='label label-warning'>Online</span></h5><br>";
							}
							else {
								$statusPmsHttps = "<h5>Plex Media Server (HTTPS):  <span class='label label-important'>Offline</span></h5><br>";
							}
							
							if ($myplexUrl) {
								$statusMyplex = "<h5>myPlex: (<a href='https://my.plexapp.com'>my.plexapp.com</a>):  <span class='label label-warning'>Online</span></h5><br>";
							}
							else {
								$statusMyplex = "<h5>myPlex: (<a href='https://my.plexapp.com'>my.plexapp.com</a>):  <span class='label label-important'>Offline</span></h5><br>";
							}
							
							echo "<div class='dashboard-status-instance'>";
								echo("$statusPmsHttp");
							echo "</div>";
							echo "<div class='dashboard-status-instance'>";
								echo("$statusPmsHttps");
							echo "</div>";
							echo "<div class='dashboard-status-instance'>";
								echo("$statusMyplex");
							echo "</div>";
						
					echo "</div>";
			echo "</div>";
		echo "</div>";
		echo "<div class='row-fluid'>";	
			echo "<div class='span12'>";
				echo "<div class='wellbg'>";
					echo "<div class='wellheader'>";
						echo "<div class='dashboard-wellheader'>";
							echo "<div id='currentActivityHeader'>";
								require("includes/current_activity_header.php");
							echo "</div>";
						echo "</div>";
					echo "</div>";
					echo "<div id='currentActivity'>";
						require("includes/current_activity.php");
					echo "</div>";	
				echo "</div>";			
			echo "</div>";
		echo "</div>";				
					
		echo "</div>";

		/* recently added rows -- dynamic */
		echo "<div class='row-fluid'>";
			echo "<div class='wellbg'>";
				echo "<div class='wellheader'>";
					echo "<div class='dashboard-wellheader'>";
					echo "<h3>Recently Added</h3>";
					echo "</div>";
				echo "</div>";
				echo "<div id='recentlyAdded'></div>";		
		echo "</div>";		
		?>
			
			

		<footer>
		
		</footer>
		
    </div><!--/.fluid-container-->
    
    <!-- javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-2.0.3.js"></script>
	<script src="js/bootstrap.js"></script>
	<script>
		
		function currentActivityHeader() {
			$('#currentActivityHeader').load('includes/current_activity_header.php');
		}
		setInterval('currentActivityHeader()', 15000);
	
	</script>
	<script>
		
		function currentActivity() {
			$('#currentActivity').load('includes/current_activity.php');
		}
		setInterval('currentActivity()', 15000);

	</script>
	<script>
		function recentlyAdded() {
			var widthVal= $('body').find(".container-fluid").width();
			$('#recentlyAdded').load('includes/recently_added.php?width=' + widthVal);
		}

		$(document).ready(function () {
			recentlyAdded()
			$(window).resize(function() {
				recentlyAdded()
			});
		});
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
	</script>
	
	



  </body>
</html>