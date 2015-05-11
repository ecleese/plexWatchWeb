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
				
				$guisettingsFile = "config/config.php";
				if (file_exists($guisettingsFile)) { 
					require_once(dirname(__FILE__) . '/config/config.php');
				}else{
					header("Location: settings.php");
				}
				
				$plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";
				
				if (!empty($plexWatch['myPlexAuthToken'])) {
					$myPlexAuthToken = $plexWatch['myPlexAuthToken'];
               if ($fileContents = file_get_contents("".$plexWatchPmsUrl."/status/sessions?X-Plex-Token=".$myPlexAuthToken."")) {
                  $statusSessions = simplexml_load_string($fileContents) or die ("Failed to access Plex Media Server. Please check your settings.");
               }

				}else{
					$myPlexAuthToken = '';
               if ($fileContents = file_get_contents("".$plexWatchPmsUrl."/status/sessions")) {
                  $statusSessions = simplexml_load_string($fileContents) or die ("Failed to access Plex Media Server. Please check your settings.");
               }

				}
					
				$db = dbconnect();
				
				date_default_timezone_set(@date_default_timezone_get());

				echo "<div class='span3'>";
					echo "<div class='wellbg'>";
						echo "<div class='wellheader'>";
							echo "<div class='dashboard-wellheader'>";
								echo "<h4>Top 10 (All Time)</h4>";
							echo "</div>";
						echo "</div>";
						echo "<div class='charts-wrapper'>";
							echo "<ul>";
							
							if ($plexWatch['chartsGrouping'] == "yes") {
								$plexWatchDbTable = "grouped";
							}else if ($plexWatch['chartsGrouping'] == "no") {
								$plexWatchDbTable = "processed";
							}
							$queryTop10 = $db->query("SELECT title,time,user,orig_title,orig_title_ep,episode,season,xml,datetime(time, 'unixepoch') AS time, COUNT(*) AS play_count FROM ".$plexWatchDbTable." GROUP BY title HAVING play_count > 0 ORDER BY play_count DESC,time DESC LIMIT 10") or die ("Failed to access plexWatch database. Please check your server and config.php settings.");
				
							// Run through each feed item
							$num_rows = 0;
							while ($top10 = $queryTop10->fetchArray()) {
								$num_rows++;
								
								$xml = simplexml_load_string($top10['xml']) ;  
								
									if ($xml['thumb'] != "")
									{
										$xmlMovieThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:32400".$xml['thumb']."&width=100&height=149";
									}
									else
									{
										$xmlMovieThumbUrl = "";
									}

									if ($xml['grandparentThumb'] != "")
									{
										$xmlEpisodeThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:32400".$xml['grandparentThumb']."&width=100&height=149"; 
									}
									else
									{
										$xmlEpisodeThumbUrl = "";
									}
								
						
								if ($xml['type'] == "movie") {
									echo "<div class='charts-instance-wrapper'>";
											
										echo "<div class='charts-instance-position-circle'><h1>".$num_rows."</h1></div>";	
										echo "<div class='charts-instance-poster'>";
											if ($xmlMovieThumbUrl != "")
											{
												echo "<img src='includes/img.php?img=".urlencode($xmlMovieThumbUrl)."'></img>";
											}
											else
											{
												echo "<img class=\"thumbnail-empty\" src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAARgAAAGkCAYAAADqhjqCAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpEOUQ2RUUxNjU5QzAxMUUzQTM4OUJBMUIyOTlDOTMwQSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpEOUQ2RUUxNzU5QzAxMUUzQTM4OUJBMUIyOTlDOTMwQSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjY1QTIyRTc1NTlCRjExRTNBMzg5QkExQjI5OUM5MzBBIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjY1QTIyRTc2NTlCRjExRTNBMzg5QkExQjI5OUM5MzBBIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+0MdqqgAADUhJREFUeNrs3WnI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'></img>";
											}
										echo "</div>";
										echo "<div class='charts-instance-position-title'>";
											echo "<li><h3><a href='info.php?id=".$xml['ratingKey']."'>".$top10['title']." (".$xml['year'].")</a></h3><h5> (".$top10['play_count']." views)<h5></li>";
										echo "</div>";
									echo "</div>";
								} else if ($xml['type'] == "episode") {
										echo "<div class='charts-instance-wrapper'>";
											
										echo "<div class='charts-instance-position-circle'><h1>".$num_rows."</h1></div>";	
										echo "<div class='charts-instance-poster'>";
											if ($xmlEpisodeThumbUrl != "")
											{
												echo "<img src='includes/img.php?img=".urlencode($xmlEpisodeThumbUrl)."'></img>";
											}
											else
											{
												echo "<img class=\"thumbnail-empty\" src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAARgAAAGkCAYAAADqhjqCAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpEOUQ2RUUxNjU5QzAxMUUzQTM4OUJBMUIyOTlDOTMwQSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpEOUQ2RUUxNzU5QzAxMUUzQTM4OUJBMUIyOTlDOTMwQSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjY1QTIyRTc1NTlCRjExRTNBMzg5QkExQjI5OUM5MzBBIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjY1QTIyRTc2NTlCRjExRTNBMzg5QkExQjI5OUM5MzBBIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+0MdqqgAADUhJREFUeNrs3WnI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'></img>";
											}
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
								echo "<h4>Top 10 Films (All Time)</h4>";
							echo "</div>";
						echo "</div>";
						echo "<div class='charts-wrapper'>";
							echo "<ul>";
							
							$queryTop10Movies = $db->query("SELECT title,time,user,orig_title,orig_title_ep,episode,season,xml,datetime(time, 'unixepoch') AS time, COUNT(*) AS play_count FROM ".$plexWatchDbTable." GROUP BY title HAVING play_count > 0 ORDER BY play_count DESC,time DESC");
				
							// Run through each feed item
							$top10Movies_Num_rows = 0;
							while ($top10Movies = $queryTop10Movies->fetchArray()) {

								$top10MoviesXml = simplexml_load_string($top10Movies['xml']) ;  
									
									if ($top10MoviesXml['thumb'] != "")
									{
										$top10MoviesXmlMovieThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:32400".$top10MoviesXml['thumb']."&width=100&height=149"; 
									}
									else
									{
										$top10MoviesXmlMovieThumbUrl = "";
									}
								
								if ($top10MoviesXml['type'] == "movie") {
									$top10Movies_Num_rows++;
									if ($top10Movies_Num_rows == 11) {
										break;
									}else{
										echo "<div class='charts-instance-wrapper'>";
												
											echo "<div class='charts-instance-position-circle'><h1>".$top10Movies_Num_rows."</h1></div>";	
											echo "<div class='charts-instance-poster'>";
												if ($top10MoviesXmlMovieThumbUrl != "")
												{
													echo "<img src='includes/img.php?img=".urlencode($top10MoviesXmlMovieThumbUrl)."'></img>";
												}
												else
												{
													echo "<img class=\"thumbnail-empty\" src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAARgAAAGkCAYAAADqhjqCAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpEOUQ2RUUxNjU5QzAxMUUzQTM4OUJBMUIyOTlDOTMwQSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpEOUQ2RUUxNzU5QzAxMUUzQTM4OUJBMUIyOTlDOTMwQSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjY1QTIyRTc1NTlCRjExRTNBMzg5QkExQjI5OUM5MzBBIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjY1QTIyRTc2NTlCRjExRTNBMzg5QkExQjI5OUM5MzBBIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+0MdqqgAADUhJREFUeNrs3WnI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'></img>";
												}
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
								echo "<h4>Top 10 TV Shows (All Time)</h4>";
							echo "</div>";
						echo "</div>";
						echo "<div class='charts-wrapper'>";
							echo "<ul>";
							
							$queryTop10Shows = $db->query("SELECT title,time,user,orig_title,orig_title_ep,episode,season,xml,datetime(time, 'unixepoch') AS time, COUNT(orig_title) AS play_count FROM ".$plexWatchDbTable." WHERE season != '' GROUP BY orig_title HAVING play_count > 0 ORDER BY play_count DESC,time DESC");
				
							// Run through each feed item
							$top10Shows_Num_rows = 0;
							while ($top10Shows = $queryTop10Shows->fetchArray()) {

								$top10ShowsXml = simplexml_load_string($top10Shows['xml']) ;  
								
									if ($top10ShowsXml['grandparentThumb'] != "")
									{
										$top10ShowsXmlShowThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:32400".$top10ShowsXml['grandparentThumb']."&width=100&height=149";
									}
									else
									{
										$top10ShowsXmlShowThumbUrl = "";
									}
								
								if ($top10ShowsXml['type'] == "episode") {
									$top10Shows_Num_rows++;
									if ($top10Shows_Num_rows == 11) {
										break;
									}else{
										echo "<div class='charts-instance-wrapper'>";
												
											echo "<div class='charts-instance-position-circle'><h1>".$top10Shows_Num_rows."</h1></div>";	
											echo "<div class='charts-instance-poster'>";
												if ($top10ShowsXmlShowThumbUrl != "")
												{
													echo "<img src='includes/img.php?img=".urlencode($top10ShowsXmlShowThumbUrl)."'></img>";
												}
												else
												{
													echo "<img class=\"thumbnail-empty\" src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAARgAAAGkCAYAAADqhjqCAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpEOUQ2RUUxNjU5QzAxMUUzQTM4OUJBMUIyOTlDOTMwQSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpEOUQ2RUUxNzU5QzAxMUUzQTM4OUJBMUIyOTlDOTMwQSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjY1QTIyRTc1NTlCRjExRTNBMzg5QkExQjI5OUM5MzBBIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjY1QTIyRTc2NTlCRjExRTNBMzg5QkExQjI5OUM5MzBBIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+0MdqqgAADUhJREFUeNrs3WnI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'></img>";
												}
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
								echo "<h4>Top 10 TV Episodes (All Time)</h4>";
							echo "</div>";
						echo "</div>";
						echo "<div class='charts-wrapper'>";
							echo "<ul>";
							
							$queryTop10Episodes = $db->query("SELECT title,time,user,orig_title,orig_title_ep,episode,season,xml,datetime(time, 'unixepoch') AS time, COUNT(*) AS play_count FROM ".$plexWatchDbTable." WHERE season != '' GROUP BY title HAVING play_count > 0 ORDER BY play_count DESC,time DESC");
				
							// Run through each feed item
							$top10Episodes_Num_rows = 0;
							while ($top10Episodes = $queryTop10Episodes->fetchArray()) {

								$top10EpisodesXml = simplexml_load_string($top10Episodes['xml']) ;  
								
									if ($top10EpisodesXml['parentThumb'] != "")
									{
										$top10EpisodesXmlEpisodeThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:32400".$top10EpisodesXml['parentThumb']."&width=100&height=149";
									}
									else if ($top10EpisodesXml['grandparentThumb'] != "")
									{
										$top10EpisodesXmlEpisodeThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:32400".$top10EpisodesXml['grandparentThumb']."&width=100&height=149";
									}
									else
									{
										$top10EpisodesXmlEpisodeThumbUrl = "";
									}
								
								if ($top10EpisodesXml['type'] == "episode") {
									$top10Episodes_Num_rows++;
									if ($top10Episodes_Num_rows == 11) {
										break;
									}else{
										echo "<div class='charts-instance-wrapper'>";
												
											echo "<div class='charts-instance-position-circle'><h1>".$top10Episodes_Num_rows."</h1></div>";	
											echo "<div class='charts-instance-poster'>";
												if ($top10EpisodesXmlEpisodeThumbUrl != "")
												{
													echo "<img src='includes/img.php?img=".urlencode($top10EpisodesXmlEpisodeThumbUrl)."'></img>";
												}
												else
												{
													echo "<img class=\"thumbnail-empty\" src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAARgAAAGkCAYAAADqhjqCAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpEOUQ2RUUxNjU5QzAxMUUzQTM4OUJBMUIyOTlDOTMwQSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpEOUQ2RUUxNzU5QzAxMUUzQTM4OUJBMUIyOTlDOTMwQSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjY1QTIyRTc1NTlCRjExRTNBMzg5QkExQjI5OUM5MzBBIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjY1QTIyRTc2NTlCRjExRTNBMzg5QkExQjI5OUM5MzBBIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+0MdqqgAADUhJREFUeNrs3WnI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'></img>";
												}
											echo "</div>";
											echo "<div class='charts-instance-position-title'>";
												echo "<li><h3><a href='info.php?id=".$top10EpisodesXml['ratingKey']."'>".$top10Episodes['orig_title']." - Season ".$top10Episodes['season'].", Episode ".$top10Episodes['episode']."</a></h3><h5> (".$top10Episodes['play_count']." views)</h5></li>";
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
