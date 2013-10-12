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
					<li><a href="charts.php"><i class="icon-list icon-white"></i> Charts</a></li>
					
				</ul>
			</div>
		</div>
    </div>
	

	
	<?php
		require_once(dirname(__FILE__) . '/config.php');
		date_default_timezone_set(@date_default_timezone_get());
		
		$id = $_GET['id'];
					
		$infoUrl = "http://".$plexWatch['pmsUrl'].":32400/library/metadata/".$id."";
		$xml = simplexml_load_file($infoUrl) or die ("Feed Not Found"); 
			
		if ($xml->Video['type'] == "episode") {
						
			$xmlArtUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$xml->Video['art']."&width=1920&height=1080";                                       
			$xmlThumbUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$xml->Video['parentThumb']."&width=256&height=352";                                        
						
				echo "<div class='container-fluid'>";
					
						
						echo "<div class='art-face' style='background-image:url(".$xmlArtUrl.")'>";
							
							echo "<div class='summary-wrapper'>";
								echo "<div class='summary-overlay'>";
									echo "<div class='row-fluid'>";
									
										echo "<div class='span9'>";
											echo "<div class='summary-content-poster hidden-phone hidden-tablet'>";
												echo "<img src='".$xmlThumbUrl."'></img>";
											echo "</div>";
												echo "<div class='summary-content'>";
													echo "<div class='summary-content-title'><h1>".$xml->Video['grandparentTitle']." (Season ".$xml->Video['parentIndex'].", Episode ".$xml->Video['index'].") \"".$xml->Video['title']."\"</h1></div>";
													echo "<div class='summary-content-details-wrapper'>";
														echo "<div class='summary-content-director'>Directed by <strong>".$xml->Video->Director['tag']."</strong></div>";
														
														$duration = $xml->Video['duration'];
														$durationMinutes = $duration / 1000 / 60;
														$durationRounded = floor($durationMinutes);
														
														echo "<div class='summary-content-duration'>Runtime <strong>".$durationRounded." mins</strong></div>";
														echo "<div class='summary-content-content-rating'>Rated <strong>".$xml->Video['contentRating']."</strong></div>";
													echo "</div>";
													echo "<div class='summary-content-summary'><p>".$xml->Video['summary']."</p></div>";
												echo "</div>";
											echo "</div>";
											
											echo "<div class='span3'>";
												echo "<div class='summary-content-people-wrapper hidden-phone hidden-tablet'>";	
													$writerCount = 0;
													foreach($xml->Video->Writer as $xmlWriters) {
														$writers[] = "" .$xmlWriters['tag']. "";
														if (++$writerCount == 4) break;
													}
													echo "<div class='summary-content-writers'><h6><strong>Written by</strong></h6><ul><li>";
														echo implode('<li>', $writers);
													echo "</li></div></ul>";
												echo "</div>";
											echo "</div>";
										echo "</div>";
										
									echo "</div>";
								echo "</div>";
							echo "</div>";
							
						echo "</div>";
						
					
				echo "</div>";	
					
	echo "<div class='container-fluid'>";
		echo "<div class='row-fluid'>";	
			echo "<div class='span12'>";
			echo "</div>";
		echo "</div>";
		echo "<div class='row-fluid'>";
			echo "<div class='span12'>";
				echo "<div class='wellbg'>";
					echo "<div class='wellheader'>";
					
						$db = new SQLite3($plexWatch['plexWatchDb']);
						$title = $db->querySingle("SELECT title FROM processed WHERE session_id LIKE '%/metadata/".$id."\_%' ESCAPE '\'  ");
						
						echo "<div class='dashboard-wellheader'>";
								echo"<h3>Watching history for <strong>".$title."</strong></h3>";
							echo"</div>";
						echo"</div>";
						
						$numRows = $db->querySingle("SELECT COUNT(*) as count FROM processed ");
						$results = $db->query("SELECT * FROM processed WHERE session_id LIKE '%/metadata/".$id."\_%' ESCAPE '\' ORDER BY time DESC");
						
						if ($numRows < 1) {

						echo "No Results.";

						} else {
						
						echo "<table id='history' class='display'>";
							echo "<thead>";
								echo "<tr>";
									echo "<th align='center'><i class='icon-calendar icon-white'></i> Date</th>";
									echo "<th align='left'><i class='icon-user icon-white'></i> User</th>";
									echo "<th align='left'><i class='icon-hdd icon-white'></i> Platform</th>";
									echo "<th align='left'><i class='icon-globe icon-white'></i> IP Address</th>";
									
									echo "<th align='center'><i class='icon-play icon-white'></i> Started</th>";
									echo "<th align='center'><i class='icon-pause icon-white'></i> Paused</th>";
									echo "<th align='center'><i class='icon-stop icon-white'></i> Stopped</th>";
									echo "<th align='center'><i class='icon-time icon-white'></i> Duration</th>";
									echo "<th align='center'>Completed</th>";
								echo "</tr>";
							echo "</thead>";
							echo "<tbody>";
							while ($row = $results->fetchArray()) {
							
								echo "<tr>";
									echo "<td align='center'>".date("m/d/Y",$row['time'])."</td>";
									echo "<td align='left'><a href='user.php?user=".$row['user']."'>".$row['user']."</td>";
									echo "<td align='left'>".$row['platform']."</td>";

									if (empty($row['ip_address'])) {
										echo "<td align='left'>n/a</td>";

									}else{

										echo "<td align='left'>".$row['ip_address']."</td>";
									}
									$request_url = $row['xml'];
									$xmlfield = simplexml_load_string($request_url) ; 
									$ratingKey = $xmlfield['ratingKey'];
									$type = $xmlfield['type'];
									$duration = $xmlfield['duration'];
									$viewOffset = $xmlfield['viewOffset'];

									
													
									echo "<td align='center'>".date("g:i a",$row['time'])."</td>";
									
									$paused_time = round(abs($row['paused_counter']) / 60,1);
									echo "<td align='center'>".$paused_time." min</td>";
									
									$stopped_time = date("g:i a",$row['stopped']);
									
									if ($stopped_time == '7:00 pm') {								//need to find out why it's always this value and write an alternate method.
										echo "<td align='center'>n/a</td>";
									}else{
										echo "<td align='center'>".$stopped_time."</td>";
									}

									$to_time = strtotime(date("m/d/Y g:i a",$row['stopped']));
									$from_time = strtotime(date("m/d/Y g:i a",$row['time']));
									
									$viewed_time = round(abs($to_time - $from_time - $paused_time) / 60,0);
									$viewed_time_length = strlen($viewed_time);
									
									
									
									if ($viewed_time_length == 8) {
										echo "<td align='center'>n/a</td>";
									}else{
										echo "<td align='center'>".$viewed_time. " min</td>";
									}
									
									$percentComplete = sprintf("%2d", ($viewOffset / $duration) * 100);
										if ($percentComplete >= 90) {	
										  $percentComplete = 100;    
										}

									echo "<td align='center'><span class='badge badge-warning'>".$percentComplete."%</span></td>";
								echo "</tr>";   
							}
						}
							echo "</tbody>";
						echo "</table>";
					
					echo "</div>";
				echo "</div>";	
			echo "</div>";
		echo "</div>";	

		
					}else if ($xml->Directory['type'] == "show") {
						
						$xmlArtUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$xml->Directory['art']."&width=1920&height=1080";                                       
						$xmlThumbUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$xml->Directory['thumb']."&width=256&height=352";                                        
						
					echo "<div class='container-fluid'>";
							echo "<div class='art-face' style='background-image:url(".$xmlArtUrl.")'>";
							
							echo "<div class='summary-wrapper'>";
								echo "<div class='summary-overlay'>";
									echo "<div class='row-fluid'>";
									
									echo "<div class='span12'>";
										echo "<div class='summary-content-poster hidden-phone hidden-tablet'>";
											echo "<img src='".$xmlThumbUrl."'></img>";
										echo "</div>";
											echo "<div class='summary-content'>";
												echo "<div class='summary-content-title'><h1>".$xml->Directory['title']."</h1></div>";
												echo "<div class='summary-content-details-wrapper'>";
													echo "<div class='summary-content-director'>Studio <strong>".$xml->Directory['studio']."</strong></div>";
													
													$duration = $xml->Directory['duration'];
													$durationMinutes = $duration / 1000 / 60;
													$durationRounded = floor($durationMinutes);
													
													echo "<div class='summary-content-duration'>Runtime <strong>".$durationRounded." mins</strong></div>";
													echo "<div class='summary-content-content-rating'>Rated <strong>".$xml->Directory['contentRating']."</strong></div>";
												echo "</div>";
												echo "<div class='summary-content-summary'><p>".$xml->Directory['summary']."</p></div>";
											echo "</div>";
										echo "</div>";
										
										
										
									echo "</div>";
									echo "</div>";
								echo "</div>";
							echo "</div>";
						echo "</div>";
						
					echo "</div>";
					
	echo "<div class='container-fluid'>";
		echo "<div class='row-fluid'>";	
			echo "<div class='span12'>";
			echo "</div>";
		echo "</div>";
		
		echo "<div class='row-fluid'>";
			echo "<div class='span12'>";
				echo "<div class='wellbg'>";
					
					echo "<div class='wellheader'>";

						$db = new SQLite3($plexWatch['plexWatchDb']);
						echo"<h3>The most watched episodes of <strong>".$xml->Directory['title']."</strong> are</h3>";	
					
					echo"</div>";
																																		
						$topWatchedResults = $db->query("SELECT title,time,user,orig_title,orig_title_ep,episode,season,xml,datetime(time, 'unixepoch') AS time, COUNT(*) AS play_count FROM processed WHERE orig_title LIKE \"".$xml->Directory['title']."\" GROUP BY title HAVING play_count > 0 ORDER BY play_count DESC,time DESC LIMIT 7");

						echo "<div class='info-top-watched-wrapper'>";
							echo "<ul class='info-top-watched-instance'>";
							// Run through each feed item
							$numRows = 0;
								
							while ($topWatchedResultsRow = $topWatchedResults->fetchArray()) {
							
								$topWatchedXmlUrl = $topWatchedResultsRow['xml'];
								$topWatchedXmlfield = simplexml_load_string($topWatchedXmlUrl) ;								   
								$topWatchedThumbUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$topWatchedXmlfield['thumb']."&width=205&height=115";                                        

								$numRows++;

									echo "<li>";
										echo "<div class='info-top-watched-instance-position-circle'><h1>".$numRows."</h1></div>";
										echo "<div class='info-top-watched-poster'>";
											echo "<div class='info-top-watched-poster-face'><a href='info.php?id=" .$topWatchedXmlfield['ratingKey']. "'><img src='".$topWatchedThumbUrl."' class='info-top-watched-poster-face'></img></a></div>";
											echo "<div class='info-top-watch-card-overlay'><div class='info-top-watched-season'>Season ".$topWatchedResultsRow['season'].", Episode ".$topWatchedResultsRow['episode']."</div><div class='info-top-watched-playcount'><strong>".$topWatchedResultsRow['play_count']."</strong> views</div></div>";
										echo "</div>";
										echo "<div class='info-top-watched-instance-text-wrapper'>";
											echo "<div class='info-top-watched-title'><a href='info.php?id=".$topWatchedXmlfield['ratingKey']."'> \" ".$topWatchedResultsRow['orig_title_ep']." \"</a></div>";
											
												
											
													
										echo "</div>";	
									echo "</li>";	
											
								  
							}
							echo "</ul>"; 
						echo "</div>"; 		
				echo "</div>";
			echo "</div>";	
		echo "</div>";
		
	echo "</div>";
					
					
					}else if ($xml->Directory['type'] == "season") {
						
						$parentInfoUrl = "http://".$plexWatch['pmsUrl'].":32400/library/metadata/".$xml->Directory['parentRatingKey']."";
						$parentXml = simplexml_load_file($parentInfoUrl) or die ("Feed Not Found");
						
						$xmlArtUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$xml->Directory['art']. "&width=1920&height=1080";                                       
						$xmlThumbUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$xml->Directory['thumb']. "&width=256&height=352";                                        
						
					echo "<div class='container-fluid'>";	
						
							echo "<div class='art-face' style='background-image:url(".$xmlArtUrl.")'>";
							
							echo "<div class='summary-wrapper'>";
								echo "<div class='summary-overlay'>";
									echo "<div class='row-fluid'>";
									
									echo "<div class='span9'>";
										echo "<div class='summary-content-poster hidden-phone hidden-tablet'>";
											echo "<img src='".$xmlThumbUrl."'></img>";
										echo "</div>";
											echo "<div class='summary-content'>";
												echo "<div class='summary-content-title'><h1>".$xml->Directory['parentTitle']." (".$xml->Directory['title'].")</h1></div>";
												echo "<div class='summary-content-details-wrapper'>";
													echo "<div class='summary-content-director'>Studio <strong>".$parentXml['studio']."</strong></div>";
													
													$duration = $parentXml->Directory['duration'];
													$durationMinutes = $duration / 1000 / 60;
													$durationRounded = floor($durationMinutes);
													
													echo "<div class='summary-content-duration'>Runtime <strong>".$durationRounded." mins</strong></div>";
													echo "<div class='summary-content-content-rating'>Rated <strong>".$parentXml->Directory['contentRating']."</strong></div>";
												echo "</div>";
												echo "<div class='summary-content-summary'><p>".$parentXml->Directory['summary']."</p></div>";
											echo "</div>";
										echo "</div>";
										
										echo "<div class='span3'>";
											
											
										echo "</div>";
										
									echo "</div>";
									echo "</div>";
								echo "</div>";
							echo "</div>";
						echo "</div>";
						
					echo "</div>";	
							
					}else if ($xml->Video['type'] == "movie") {				
						
						$xmlArtUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$xml->Video['art']."&width=1920&height=1080";                                        
						$xmlThumbUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$xml->Video['thumb']."&width=256&height=352";                                        
						
				echo "<div class='container-fluid'>";		
					echo "<div class='art-face' style='background-image:url(".$xmlArtUrl.")'>";
							
						echo "<div class='summary-wrapper'>";
							echo "<div class='summary-overlay'>";
								echo "<div class='row-fluid'>";
									echo "<div class='span9'>";	
										echo "<div class='summary-content-poster hidden-phone hidden-tablet'>";
											echo "<img src='".$xmlThumbUrl."'></img>";
										echo "</div>";	
											echo "<div class='summary-content'>";
												echo "<div class='summary-content-title'><h1>".$xml->Video['title']." (".$xml->Video['year'].")</h1></div>";
												
												$starRating = ceil ($xml->Video['rating'] / 2);
												
												echo "<div class='rateit hidden-phone hidden-tablet'  data-rateit-value='".$starRating."' data-rateit-ispreset='true' data-rateit-readonly='true'></div>";
												echo "<div class='summary-content-details-wrapper'>";
													echo "<div class='summary-content-director'>Directed by <strong>".$xml->Video->Director['tag']."</strong></div>";
													
													echo "<div class='summary-content-content-rating'>Rated <strong>".$xml->Video['contentRating']."</strong></div>";
													
													$duration = $xml->Video['duration'];
													$durationMinutes = $duration / 1000 / 60;
													$durationRounded = floor($durationMinutes);
													
													echo "<div class='summary-content-duration'>Runtime <strong>".$durationRounded." mins</strong></div>";
													
												echo "</div>";
												echo "<div class='summary-content-summary'><p>".$xml->Video['summary']."</p></div>";
											echo "</div>";
										echo "</div>";
										
										echo "<div class='span3'>";
											echo "<div class='summary-content-people-wrapper hidden-phone hidden-tablet'>";
												$genreCount = 0;
												foreach($xml->Video->Genre as $xmlGenres) {
													$genres[] = "" .$xmlGenres['tag']. "";
													if (++$genreCount == 4) break;
												}
												if ($genreCount != 0){
												echo "<div class='summary-content-actors'><h6><strong>Genres</strong></h6><ul><li>";
													echo implode('<li>', $genres);
												echo "</li></div></ul>";
												}
												
												$roleCount = 0;
												foreach($xml->Video->Role as $Roles) {
													$actors[] = "" .$Roles['tag']. "";
													if (++$roleCount == 4) break;
												}
												if ($roleCount != 0) {
												echo "<div class='summary-content-actors'><h6><strong>Starring</strong></h6><ul><li>";
													echo implode('<li>', $actors);
												echo "</li></div></ul>";
												}
												
												$writerCount = 0;
												foreach($xml->Video->Writer as $xmlWriters) {
													$writers[] = "" .$xmlWriters['tag']. "";
													if (++$writerCount == 4) break;
												}
												if ($writerCount != 0) {
												echo "<div class='summary-content-writers'><h6><strong>Written by</strong></h6><ul><li>";
													echo implode('<li>', $writers);
												echo "</li></div></ul>";
												}
											echo "</div>";
										echo "</div>";
										
									echo "</div>";
								echo "</div>";
							echo "</div>";
						echo "</div>";
					echo "</div>";	
				echo "</div>";	
					
	echo "<div class='container-fluid'>";
		echo "<div class='row-fluid'>";	
			echo "<div class='span12'>";
			echo "</div>";
		echo "</div>";
		echo "<div class='row-fluid'>";
			echo "<div class='span12'>";
				echo "<div class='wellbg'>";
					echo "<div class='wellheader'>";

						
						$db = new SQLite3($plexWatch['plexWatchDb']);

						
						$title = $db->querySingle("SELECT title FROM processed WHERE session_id LIKE '%/metadata/".$id."\_%' ESCAPE '\'  ");
						echo "<div class='dashboard-wellheader'>";
								echo"<h3>Watching history for <strong>".$xml->Video['title']."</strong></h3>";
							echo"</div>";
						echo"</div>";
						
						$numRows = $db->querySingle("SELECT COUNT(*) as count FROM processed ");
						
						$results = $db->query("SELECT * FROM processed WHERE session_id LIKE '%/metadata/".$id."\_%' ESCAPE '\' ORDER BY time DESC");
						
						
						if ($numRows < 1) {

						echo "No Results.";

						} else {
						
						echo "<table id='history' class='display'>";
							echo "<thead>";
								echo "<tr>";
									echo "<th align='center'><i class='icon-calendar icon-white'></i> Date</th>";
									echo "<th align='left'><i class='icon-user icon-white'></i> User</th>";
									echo "<th align='left'><i class='icon-hdd icon-white'></i> Platform</th>";
									echo "<th align='left'><i class='icon-globe icon-white'></i> IP Address</th>";
									
									echo "<th align='center'><i class='icon-play icon-white'></i> Started</th>";
									echo "<th align='center'><i class='icon-pause icon-white'></i> Paused</th>";
									echo "<th align='center'><i class='icon-stop icon-white'></i> Stopped</th>";
									echo "<th align='center'><i class='icon-time icon-white'></i> Duration</th>";
									echo "<th align='center'>Completed</th>";
								echo "</tr>";
							echo "</thead>";
							echo "<tbody>";
								while ($row = $results->fetchArray()) {
								
								echo "<tr>";
									echo "<td align='center'>".date("m/d/Y",$row['time'])."</td>";
									echo "<td align='left'><a href='user.php?user=".$row['user']."'>".$row['user']."</td>";
									echo "<td align='left'>".$row['platform']."</td>";

									if (empty($row['ip_address'])) {
										echo "<td align='left'>n/a</td>";

									}else{

										echo "<td align='left'>".$row['ip_address']."</td>";
									}
									$request_url = $row['xml'];
									$xmlfield = simplexml_load_string($request_url) ; 
									$ratingKey = $xmlfield['ratingKey'];
									$type = $xmlfield['type'];
									$duration = $xmlfield['duration'];
									$viewOffset = $xmlfield['viewOffset'];

									
													
									echo "<td align='center'>".date("g:i a",$row['time'])."</td>";
									
									$paused_time = round(abs($row['paused_counter']) / 60,1);
									echo "<td align='center'>".$paused_time." min</td>";
									
									$stopped_time = date("g:i a",$row['stopped']);
									
									if ($stopped_time == '7:00 pm') {								//need to find out why it's always this value and write an alternate method.
										echo "<td align='center'>n/a</td>";
									}else{
										echo "<td align='center'>".$stopped_time."</td>";
									}

									$to_time = strtotime(date("m/d/Y g:i a",$row['stopped']));
									$from_time = strtotime(date("m/d/Y g:i a",$row['time']));
									
									$viewed_time = round(abs($to_time - $from_time - $paused_time) / 60,0);
									$viewed_time_length = strlen($viewed_time);
									
									
									
									if ($viewed_time_length == 8) {
										echo "<td align='center'>n/a</td>";
									}else{
										echo "<td align='center'>".$viewed_time. " min</td>";
									}
									
									$percentComplete = sprintf("%2d", ($viewOffset / $duration) * 100);
										if ($percentComplete >= 90) {	
										  $percentComplete = 100;    
										}

									echo "<td align='center'><span class='badge badge-warning'>".$percentComplete."%</span></td>";
								echo "</tr>";   
								}
							}
							echo "</tbody>";
						echo "</table>";
						
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";	
		
		}else{
		}
										
	?>			
							
				

		

		<footer>
		
		</footer>
		
    
    
    <!-- javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-2.0.3.js"></script>
	<script src="js/bootstrap.js"></script>
	<script src="js/jquery.dataTables.js"></script>
	<script src="js/jquery.dataTables.plugin.bootstrap_pagination.js"></script>
	<script src="js/jquery.rateit.js"></script>
	
	<script>
	var p=$('#summary-content-summary p');
	var divh=$('#summary-content-summary').height();
		while ($(p).outerHeight()>divh) {
			$(p).text(function (index, text) {
			return text.replace(/\W*\s(\S)*$/, '...');
		});
	}
	</script>
	
	<script>
		$(document).ready(function() {
			var oTable = $('#history').dataTable( {
				"bPaginate": false,
				"bLengthChange": true,
				"bFilter": false,
				"bSort": false,
				"bInfo": true,
				"bAutoWidth": true,
				"aaSorting": [[ 0, "desc" ]],
				"bStateSave": true,
				"bSortClasses": false,
				"sPaginationType": "bootstrap"	
			} );
		} );
	</script>
	

  </body>
</html>