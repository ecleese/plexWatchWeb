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
				<a href="index.php"><div class="logo hidden-phone"></div></a>
				<ul class="nav">
					
					<li class="active"><a href="index.php"><i class="icon-2x icon-home icon-white" data-toggle="tooltip" data-placement="bottom" title="Home" id="home"></i></a></li>
					<li><a href="history.php"><i class="icon-2x icon-calendar icon-white" data-toggle="tooltip" data-placement="bottom" title="History" id="history"></i></a></li>
					<li><a href="stats.php"><i class="icon-2x icon-tasks icon-white" data-toggle="tooltip" data-placement="bottom" title="Stats" id="stats"></i></a></li>
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
					$fileContents = '';
					if ($fileContents = file_get_contents("".$plexWatchPmsUrl."/status/sessions?query=c&X-Plex-Token=".$myPlexAuthToken."")) {
 	             		$statusSessions = simplexml_load_string($fileContents) or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");
                 	}
					$sections = simplexml_load_file("".$plexWatchPmsUrl."/library/sections?query=c&X-Plex-Token=".$myPlexAuthToken."") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");
				}else{
					$myPlexAuthToken = '';
					if ($fileContents = file_get_contents("".$plexWatchPmsUrl."/status/sessions?query=c&X-Plex-Token=".$myPlexAuthToken."")) {
 	             		$statusSessions = simplexml_load_string($fileContents) or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");
                 	}
					$sections = simplexml_load_file("".$plexWatchPmsUrl."/library/sections") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");	
				}	
				
				
					echo "<div class='wellbg'>";
						echo "<div class='wellheader'>";
						echo "<div class='dashboard-wellheader'>";
							echo "<h3>Statistics</h3>";
						echo "</div>";
					echo "</div>";
						
					echo "<div class='stats'>";
					
						echo "<ul>";
					
							foreach ($sections->children() as $section) {
							
								if (!empty($plexWatch['myPlexAuthToken'])) {
									if ($section['type'] == "movie") {
										$sectionDetails = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?type=1&sort=addedAt:desc&X-Plex-Container-Start=0&X-Plex-Container-Size=1&X-Plex-Token=".$myPlexAuthToken."") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");
										
										echo "<li>";
												echo "<h1>".$sectionDetails['totalSize']."</h1><h5>".$section['title']."</h5>";
										echo "</li>";
									}else if ($section['type'] == "show") {
										$sectionDetails = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?type=2&sort=addedAt:desc&X-Plex-Container-Start=0&X-Plex-Container-Size=1&X-Plex-Token=".$myPlexAuthToken."") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");
										$tvEpisodeCount = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?type=4&X-Plex-Container-Start=0&X-Plex-Container-Size=1&X-Plex-Token=".$myPlexAuthToken."") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");
					
										echo "<li>";
												echo "<h1>".$sectionDetails['totalSize']."</h1><h5>".$section['title']."</h5>";
										echo "</li>";
										echo "<li>";
												echo "<h1>".$tvEpisodeCount['totalSize']."</h1><h5>TV Episodes</h5>";
										echo "</li>";
									}
								}else{
									if ($section['type'] == "movie") {
										$sectionDetails = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?type=1&sort=addedAt:desc&X-Plex-Container-Start=0&X-Plex-Container-Size=1") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");
										
										echo "<li>";
												echo "<h1>".$sectionDetails['totalSize']."</h1><h5>".$section['title']."</h5>";
										echo "</li>";
									}else if ($section['type'] == "show") {
										$sectionDetails = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?type=2&sort=addedAt:desc&X-Plex-Container-Start=0&X-Plex-Container-Size=1") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");
										$tvEpisodeCount = simplexml_load_file("".$plexWatchPmsUrl."/library/sections/".$section['key']."/all?type=4&X-Plex-Container-Start=0&X-Plex-Container-Size=1") or die ("<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>");
					
										echo "<li>";
												echo "<h1>".$sectionDetails['totalSize']."</h1><h5>".$section['title']."</h5>";
										echo "</li>";
										echo "<li>";
												echo "<h1>".$tvEpisodeCount['totalSize']."</h1><h5>TV Episodes</h5>";
										echo "</li>";
									}
								}
							}
							
							date_default_timezone_set(@date_default_timezone_get());
							$db = dbconnect();
							$users = $db->querySingle("SELECT count(DISTINCT user) as users FROM processed") or die ("Failed to access plexWatch database. Please check your settings.");
							
							echo "<li>";
									echo "<h1>".$users."</h1><h5>Users</h5>";
							echo "</li>";
								
							
						echo "</ul>";		
							
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
	$(document).ready(function() {
		$('#stats').tooltip();
	});
	</script>


  </body>
</html>