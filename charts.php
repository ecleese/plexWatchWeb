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
					
					<li><a href="index.php"><i class="icon-home icon-white"></i> Home</a></li>
					<li><a href="history.php"><i class="icon-calendar icon-white"></i> History</a></li>
					<li><a href="users.php"><i class="icon-user icon-white"></i> Users</a></li>
					<li class="active"><a href="charts.php"><i class="icon-list icon-white"></i> Charts</a></li>
					
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
			<div class='span12'>
			<?php
				require_once(dirname(__FILE__) . '/config.php');
				$db = new SQLite3($plexWatch['plexWatchDb']);
				date_default_timezone_set(@date_default_timezone_get());

				echo "<div class='span3'>";
					echo "<div class='wellbg'>";
						echo "<div class='wellheader'>";
							echo "<div class='dashboard-wellheader'>";
								echo "<h3>Top 10 (All Time)</h3>";
							echo "</div>";
						echo "</div>";
						echo "<div class='charts-wrapper'>";
							echo "<ul>";
							
							$queryTop10 = $db->query("SELECT title,time,user,orig_title,orig_title_ep,episode,season,xml,datetime(time, 'unixepoch') AS time, COUNT(*) AS play_count FROM processed GROUP BY title HAVING play_count > 0 ORDER BY play_count DESC,time DESC LIMIT 10");
				
							// Run through each feed item
							$num_rows = 0;
							while ($top10 = $queryTop10->fetchArray()) {
								$num_rows++;
								
								$xml = simplexml_load_string($top10['xml']) ;  
								$xmlMovieThumbUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$xml['thumb']."&width=100&height=149";                                        
								$xmlEpisodeThumbUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$xml['grandparentThumb']."&width=100&height=149";                                        
						
								if ($xml['type'] == "movie") {
									echo "<div class='charts-instance-wrapper'>";
											
										echo "<div class='charts-instance-position-circle'><h1>".$num_rows."</h1></div>";	
										echo "<div class='charts-instance-poster'>";
											echo "<img src='".$xmlMovieThumbUrl."'></img>";
										echo "</div>";
										echo "<div class='charts-instance-position-title'>";
											echo "<li><h3><a href='info.php?id=".$xml['ratingKey']."'>".$top10['title']." (".$xml['year'].")</a></h3><h5> (".$top10['play_count']." views)<h5></li>";
										echo "</div>";
									echo "</div>";
								} else if ($xml['type'] == "episode") {
										echo "<div class='charts-instance-wrapper'>";
											
										echo "<div class='charts-instance-position-circle'><h1>".$num_rows."</h1></div>";	
										echo "<div class='charts-instance-poster'>";
											echo "<img src='".$xmlEpisodeThumbUrl."'></img>";
										echo "</div>";
										echo "<div class='charts-instance-position-title'>";
											echo "<li><h3><a href='info.php?id=".$xml['ratingKey']."'>".$top10['orig_title']." - Season ".$top10['season'].", Episode".$top10['episode']."</a></h3><h5> (".$top10['play_count']." views)</h5></li>";
										echo "</div>";
									echo "</div>";
								}else{
								}
							}
							echo "</ul>";
						echo "</div>";
					echo "</div>";
				echo "</div>";
				
				echo "<div class='span3'>";
					echo "<div class='wellbg'>";
						echo "<div class='wellheader'>";
							echo "<div class='dashboard-wellheader'>";
								echo "<h3>Top 10 Films (All Time)</h3>";
							echo "</div>";
						echo "</div>";
						echo "<div class='charts-wrapper'>";
							echo "<ul>";
							
							$queryTop10Movies = $db->query("SELECT title,time,user,orig_title,orig_title_ep,episode,season,xml,datetime(time, 'unixepoch') AS time, COUNT(*) AS play_count FROM processed GROUP BY title HAVING play_count > 0 ORDER BY play_count DESC,time DESC");
				
							// Run through each feed item
							$top10Movies_Num_rows = 0;
							while ($top10Movies = $queryTop10Movies->fetchArray()) {

								$top10MoviesXml = simplexml_load_string($top10Movies['xml']) ;  
								$top10MoviesXmlMovieThumbUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$top10MoviesXml['thumb']."&width=100&height=149";                                        
								
								if ($top10MoviesXml['type'] == "movie") {
									$top10Movies_Num_rows++;
									if ($top10Movies_Num_rows == 11) {
										break;
									}else{
										echo "<div class='charts-instance-wrapper'>";
												
											echo "<div class='charts-instance-position-circle'><h1>".$top10Movies_Num_rows."</h1></div>";	
											echo "<div class='charts-instance-poster'>";
												echo "<img src='".$top10MoviesXmlMovieThumbUrl."'></img>";
											echo "</div>";
											echo "<div class='charts-instance-position-title'>";
												echo "<li><h3><a href='info.php?id=".$top10MoviesXml['ratingKey']."'>".$top10Movies['title']." (".$top10MoviesXml['year'].")</a></h3><h5> (".$top10Movies['play_count']." views)<h5></li>";
											echo "</div>";
										echo "</div>";
										
									}
								}else{
								}
							}
							echo "</ul>";
						echo "</div>";
					echo "</div>";
				echo "</div>";
				
				echo "<div class='span3'>";
					echo "<div class='wellbg'>";
						echo "<div class='wellheader'>";
							echo "<div class='dashboard-wellheader'>";
								echo "<h3>Top 10 TV Shows (All Time)</h3>";
							echo "</div>";
						echo "</div>";
						echo "<div class='charts-wrapper'>";
							echo "<ul>";
							
							$queryTop10Shows = $db->query("SELECT title,time,user,orig_title,orig_title_ep,episode,season,xml,datetime(time, 'unixepoch') AS time, COUNT(orig_title) AS play_count FROM processed GROUP BY orig_title HAVING play_count > 0 ORDER BY play_count DESC,time DESC");
				
							// Run through each feed item
							$top10Shows_Num_rows = 0;
							while ($top10Shows = $queryTop10Shows->fetchArray()) {

								$top10ShowsXml = simplexml_load_string($top10Shows['xml']) ;  
								$top10ShowsXmlShowThumbUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$top10ShowsXml['grandparentThumb']."&width=100&height=149";                                        
								
								if ($top10ShowsXml['type'] == "episode") {
									$top10Shows_Num_rows++;
									if ($top10Shows_Num_rows == 11) {
										break;
									}else{
										echo "<div class='charts-instance-wrapper'>";
												
											echo "<div class='charts-instance-position-circle'><h1>".$top10Shows_Num_rows."</h1></div>";	
											echo "<div class='charts-instance-poster'>";
												echo "<img src='".$top10ShowsXmlShowThumbUrl."'></img>";
											echo "</div>";
											echo "<div class='charts-instance-position-title'>";
												echo "<li><h3><a href='info.php?id=".$top10ShowsXml['grandparentRatingKey']."'>".$top10Shows['orig_title']."</a></h3><h5> (".$top10Shows['play_count']." views)</h5></li>";
											echo "</div>";
										echo "</div>";
										
									}
								}else{
								}
							}
							echo "</ul>";
						echo "</div>";
					echo "</div>";
				echo "</div>";
				
				echo "<div class='span3'>";
					echo "<div class='wellbg'>";
						echo "<div class='wellheader'>";
							echo "<div class='dashboard-wellheader'>";
								echo "<h3>Top 10 TV Episodes (All Time)</h3>";
							echo "</div>";
						echo "</div>";
						echo "<div class='charts-wrapper'>";
							echo "<ul>";
							
							$queryTop10Episodes = $db->query("SELECT title,time,user,orig_title,orig_title_ep,episode,season,xml,datetime(time, 'unixepoch') AS time, COUNT(*) AS play_count FROM processed GROUP BY title HAVING play_count > 0 ORDER BY play_count DESC,time DESC");
				
							// Run through each feed item
							$top10Episodes_Num_rows = 0;
							while ($top10Episodes = $queryTop10Episodes->fetchArray()) {

								$top10EpisodesXml = simplexml_load_string($top10Episodes['xml']) ;  

								$top10EpisodesXmlEpisodeThumbUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$top10EpisodesXml['parentThumb']."&width=100&height=149";                      
								
								if ($top10EpisodesXml['type'] == "episode") {
									$top10Episodes_Num_rows++;
									if ($top10Episodes_Num_rows == 11) {
										break;
									}else{
										echo "<div class='charts-instance-wrapper'>";
												
											echo "<div class='charts-instance-position-circle'><h1>".$top10Episodes_Num_rows."</h1></div>";	
											echo "<div class='charts-instance-poster'>";
												echo "<img src='".$top10EpisodesXmlEpisodeThumbUrl."'></img>";
											echo "</div>";
											echo "<div class='charts-instance-position-title'>";
												echo "<li><h3><a href='info.php?id=".$top10EpisodesXml['ratingKey']."'>".$top10Episodes['orig_title']." - Season ".$top10Episodes['season'].", Episode".$top10Episodes['episode']."</a></h3><h5> (".$top10Episodes['play_count']." views)</h5></li>";
											echo "</div>";
										echo "</div>";
										
									}
								}else{
								}
							}
							echo "</ul>";
						echo "</div>";
					echo "</div>";
				echo "</div>";
				
				
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
	

  </body>
</html>
