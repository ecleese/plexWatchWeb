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
					
					<li class="active"><a href="/plexWatch"><i class="icon-home icon-white"></i> Home</a></li>
					<li><a href="history.php"><i class="icon-calendar icon-white"></i> History</a></li>
					<li><a href="users.php"><i class="icon-user icon-white"></i> Users</a></li>
					<li><a href="charts.php"><i class="icon-list icon-white"></i> Charts</a></li>
					
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
		
			<div class='span6'>
			<?php
			include_once('config.php');
			
			$statusSessions = simplexml_load_file("http://".$plexWatch['pmsUrl'].":32400/status/sessions");
			
			
			echo "<div class='wellbg'>";
				echo "<div class='wellheader'>";
					echo "<div class='dashboard-wellheader'>";
						echo "<h3>Current Activity <strong>".$statusSessions['size']."</strong> user(s)</h3>";
					echo "</div>";
				echo "</div>";
							
				// Run through each feed item
				foreach ($statusSessions->Video as $sessions) {
													
				$sessionsthumbltrim1 = ltrim($sessions['grandparentThumb'], "/library/metadata/");
				$sessionsthumbmeta = substr($sessionsthumbltrim1, 5, 19);
				$sessionsthumb = ltrim($sessionsthumbmeta, "/thumb/");                        
										
				if ($sessions['type'] == "episode") {
					
					$sessionsArtUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http%3A%2F%2F127.0.0.1%3A32400%2Flibrary%2Fmetadata%2F" .$sessions['grandparentRatingKey']. "%2Fart%3Ft%3D" .$sessionsthumb. "&width=330&height=160";                                        
					$sessionsThumbUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http%3A%2F%2F127.0.0.1%3A32400%2Flibrary%2Fmetadata%2F" .$sessions['grandparentRatingKey']. "%2Fthumb%3Ft%3D" .$sessionsthumb. "&width=136&height=280";                                        
					
					echo "<div class='instance'>";
						
						echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$sessions['grandparentRatingKey']. "'><img src='".$sessionsThumbUrl."' class='poster-face'></img></a></div></div>";
						echo "<div class='dashboard-activity-metadata-wrapper'>";
							if (empty($sessions->User['title'])) {
								if ($sessions->Player['state'] == "playing") {
									echo "<div class='dashboard-activity-metadata-user'>";
									echo "<a href='user.php?user=Local'>Local</a> is watching ";
									echo "</div>";
								}elseif ($sessions->Player['state'] == "paused") {	 
									echo "<div class='dashboard-activity-metadata-user'>";
									echo "<a href='user.php?user=Local'>Local</a> has paused ";
									echo "</div>";
								}
															
							}else{
															
								if ($sessions->Player['state'] == "playing") {
									echo "<div class='dashboard-activity-metadata-user'>";
									echo "<a href='user.php?user=".$sessions->User['title']."'>".$sessions->User['title']."</a> is watching ";
									echo "</div>";
								}elseif ($sessions->Player['state'] == "paused") {	 
									echo "<div class='dashboard-activity-metadata-user'>";
									echo "<a href='user.php?user=".$sessions->User['title']."'>".$sessions->User['title']."</a> has paused ";
									echo "</div>";
								}
							}
													
							echo "<div class='dashboard-activity-metadata-title'>"; 
							echo "<h2>".$sessions['grandparentTitle']." - \"".$sessions['title']."\"</h2>";
							echo "</div>";
							
							echo "<div class='dashboard-activity-metadata-progress-minutes'>";
																
								$percentComplete = sprintf("%2d", ($sessions['viewOffset'] / $sessions['duration']) * 100);
								if ($percentComplete >= 90) {	
									$percentComplete = 100;    
								}
																	
								echo "<div class='progress progress-warning'><div class='bar' style='width: ".$percentComplete."%'>".$percentComplete."%</div></div>";
												
								echo "<div class='progress-minutes'>";
									$viewOffset = $sessions['viewOffset'];
									$offsetMinutes = $viewOffset / 1000 / 60;
									$offsetRounded = floor($offsetMinutes);
									echo "".$offsetRounded."";
																		
									$duration = $sessions['duration'];
									$durationMinutes = $duration / 1000 / 60;
									$durationRounded = floor($durationMinutes);
									echo " / ".$durationRounded." min";
								echo "</div>";
												
																	
							echo "</div>";
																
							echo "<div class='platform'>";
								echo "<br>";
								echo "Watching on <strong>".$sessions->Player['title']. "</strong> ";
							echo "</div>";
							
						echo "</div>";
						
					echo "</div>";
				
					}elseif ($sessions['type'] == "movie") {
						
						$sessionsthumburl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http%3A%2F%2F127.0.0.1%3A32400%2Flibrary%2Fmetadata%2F" .$sessions['ratingKey']. "%2Fthumb%3Ft%3D" .$sessionsthumb. "&width=136&height=280";                                        
						echo "<div class='instance'>";
							echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$sessions['ratingKey']. "'><img src='".$sessionsthumburl."' class='poster-face'></img></a></div></div>";
							echo "<div class='dashboard-activity-metadata-wrapper'>";
							if (empty($sessions->User['title'])) {
								
								if ($sessions->Player['state'] == "playing") {
									echo "<a href='user.php?user=Local'>Local</a> is <strong>watching</strong> ";
								}elseif ($sessions->Player['state'] == "paused") {	 
									echo "<a href='user.php?user=Local'>Local</a> has <strong>paused</strong> ";
								}
															
							}else{
															
								if ($sessions->Player['state'] == "playing") {
									echo "<a href='user.php?user=".$sessions->User['title']."'>".$sessions->User['title']."</a> is watching ";
								}elseif ($sessions->Player['state'] == "paused") {	 
									echo "<a href='user.php?user=".$sessions->User['title']."'>".$sessions->User['title']."</a> has paused ";
								}
							}
													
							echo "<div class='dashboard-activity-metadata-title'>"; 
							echo "<h2>".$sessions['title']."</h2>";
							echo "</div>";
							
							echo "<div class='dashboard-activity-metadata-progress-wrapper'>";
																
								$percentComplete = sprintf("%2d", ($sessions['viewOffset'] / $sessions['duration']) * 100);
								if ($percentComplete >= 90) {	
									$percentComplete = 100;    
								}
																	
								echo "<div class='progress progress-warning'><div class='bar' style='width: ".$percentComplete."%'>".$percentComplete."%</div></div>";
												
								echo "<div class='.dashboard-activity-metadata-progress-minutes '>";
									$viewOffset = $sessions['viewOffset'];
									$offsetMinutes = $viewOffset / 1000 / 60;
									$offsetRounded = floor($offsetMinutes);
									echo "".$offsetRounded."";
																		
									$duration = $sessions['duration'];
									$durationMinutes = $duration / 1000 / 60;
									$durationRounded = floor($durationMinutes);
									echo " / ".$durationRounded." min";
								echo "</div>";
												
																	
							echo "</div>";
																
							echo "<div class='platform'>";
								echo "<br>";
								echo "Watching on <strong>".$sessions->Player['title']. "</strong> ";
							echo "</div>";
							
						echo "</div>";
						
					echo "</div>";
			
						
					}else{
					
					}
									   
				
		}	
				echo "</div>";		
			echo "</div>";	
			echo "<div class='span6'>";
					echo "<div class='wellbg'>";
						echo "<div class='wellheader'>";
						echo "<div class='dashboard-wellheader'>";
							echo "<h3>Plex Status</h3>";
							
						echo "</div>";
						echo "</div>";
						
						echo "<div class='dashboard-status-wrapper'>";
							// Let's check Plex Media Server ports 32400, 32443
							$pmsHttp = fsockopen($plexWatch['pmsUrl'], 32400);
							$pmsHttps = fsockopen($plexWatch['pmsUrl'], 32443);
							$myplexUrl = fsockopen ('my.plexapp.com', 443);

							if ($pmsHttp) {
								$statusPmsHttp .= "<h5>Plex Media Server (HTTP):  <span class='label label-success'>Online</span></h5><br>";
							}

							else {
								$statusPmsHttp .= "<h5>Plex Media Server (HTTP):  <span class='label label-important'>Offline</span></h5><br>";
							}

							if ($pmsHttps) {
								$statusPmsHttps .= "<h5>Plex Media Server (HTTPS):  <span class='label label-success'>Online</span></h5><br>";
							}
							else {
								$statusPmsHttps .= "<h5>Plex Media Server (HTTPS):  <span class='label label-important'>Offline</span></h5><br>";
							}
							
							if ($myplexUrl) {
								$statusMyplex .= "<h5>myPlex: (<a href='https://my.plexapp.com'>my.plexapp.com</a>):  <span class='label label-success'>Online</span></h5><br>";
							}
							else {
								$statusMyplex .= "<h5>myPlex: (<a href='https://my.plexapp.com'>my.plexapp.com</a>):  <span class='label label-important'>Offline</span></h5><br>";
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
		echo "</div>";
		echo "<div class='row-fluid'>";
		
		date_default_timezone_set('America/New_York');

		$db = new SQLite3($plexWatch['plexWatchDb']);

		
		$recentResults = $db->query("SELECT item_id,time,datetime(time, 'unixepoch', 'localtime') AS datetime FROM recently_added GROUP BY item_id ORDER BY time DESC LIMIT 10");
		
		
			echo "<div class='wellbg'>";
				echo "<div class='wellheader'>";
					echo "<div class='dashboard-wellheader'>";
					echo "<h3>Recently Added</h3>";
					echo "</div>";
				echo "</div>";
				echo "<div class='dashboard-recent-media-row'>";
				echo "<ul class='dashboard-recent-media'>";
					// Run through each feed item
				while ($recent = $recentResults->fetchArray()) {
				
					$recentXml = simplexml_load_file("http://".$plexWatch['pmsUrl'].":32400".$recent['item_id']."");
					
					$recentThumbLtrim = ltrim($recentXml->Video['grandparentThumb'], "/library/metadata/");
					$recentThumbMeta = substr($recentThumbLtrim, 5, 19);
					$recentThumb = ltrim($recentThumbMeta, "/thumb/");                        
				
					
					
								
					if ($recentXml->Video['type'] == "episode") {
						
						$recentArtUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http%3A%2F%2F127.0.0.1%3A32400%2Flibrary%2Fmetadata%2F" .$recentXml->Video['grandparentRatingKey']. "%2Fart%3Ft%3D" .$recentThumb. "&width=320&height=160";                                        
						$recentThumbUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http%3A%2F%2F127.0.0.1%3A32400%2Flibrary%2Fmetadata%2F" .$recentXml->Video['grandparentRatingKey']. "%2Fthumb%3Ft%3D" .$recentThumb. "&width=136&height=280";                                        
						
							echo "<div class='dashboard-recent-media-instance'>";
							echo "<li>";
							echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$recentXml->Video['ratingKey']. "'><img src='".$recentThumbUrl."' class='poster-face'></img></a></div></div>";
							
							echo "<div class=dashboard-recent-media-metacontainer>";
							$parentIndexPadded = sprintf("%01s", $recentXml->Video['parentIndex']);
							$indexPadded = sprintf("%02s", $recentXml->Video['index']);
							echo "<h3>Season ".$parentIndexPadded.", Episode ".$indexPadded."</h3>";
							
							
							$recentTime = $recent['time'];
							$timeNow = time();
							$age = time() - strtotime($recentTime);
							include_once('includes/timeago.php');
							echo "<h4>Added ".TimeAgo($recentTime)."</h4>";
							
							echo "</div>";
							echo "</li>";
							echo "</div>";
					}else if ($recentXml->Video['type'] == "movie") {				
					
						$recentArtUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http%3A%2F%2F127.0.0.1%3A32400%2Flibrary%2Fmetadata%2F" .$recentXml->Video['ratingKey']. "%2Fart%3Ft%3D" .$recentThumb. "&width=320&height=160";                                        
						$recentThumbUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http%3A%2F%2F127.0.0.1%3A32400%2Flibrary%2Fmetadata%2F" .$recentXml->Video['ratingKey']. "%2Fthumb%3Ft%3D" .$recentThumb. "&width=136&height=280";                                        
						
							echo "<div class='dashboard-recent-media-instance'>";
							echo "<li>";
							echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$recentXml->Video['ratingKey']. "'><img src='".$recentThumbUrl."' class='poster-face'></img></a></div></div>";
							
							echo "<div class=dashboard-recent-media-metacontainer>";
							$parentIndexPadded = sprintf("%01s", $recentXml->Video['parentIndex']);
							$indexPadded = sprintf("%02s", $recentXml->Video['index']);
							echo "<h3>".$recentXml->Video['title']." (".$recentXml->Video['year'].")</h3>";
							
							
							$recentTime = $recent['time'];
							$timeNow = time();
							$age = time() - strtotime($recentTime);
							include_once('includes/timeago.php');
							echo "<h4>Added ".TimeAgo($recentTime)."</h4>";
							
							echo "</div>";
							echo "</li>";
							echo "</div>";
					}else{
					}
				}
				echo "</ul>";
				echo "</div>";
			echo "</div>";
			
	echo "</div>";		
		?>	
		</div><!--/.fluid-row-->			
			
			

		<footer>
		
		</footer>
		
    </div><!--/.fluid-container-->
    
    <!-- javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-2.0.3.js"></script>
	<script src="js/bootstrap.js"></script>
	



  </body>
</html>
