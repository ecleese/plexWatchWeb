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
	<link href="css/font-awesome.min.css" rel="stylesheet">
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
					<li><a href="charts.php"><i class="icon-2x icon-bar-chart icon-white" data-toggle="tooltip" data-placement="bottom" title="Charts" id="charts"></i></a></li>
					<li><a href="settings.php"><i class="icon-2x icon-wrench icon-white" data-toggle="tooltip" data-placement="bottom" title="Settings" id="settings"></i></a></li>
					
				</ul>
			</div>
		</div>
    </div>
	
    <div class="clear"></div>
	
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
			$id = intval($_GET['id']);
			$infoUrl = "".$plexWatchPmsUrl."/library/metadata/".$id."?X-Plex-Token=".$myPlexAuthToken."";
		}else{
			$myPlexAuthToken = '';		
			$id = intval($_GET['id']);
			$infoUrl = "".$plexWatchPmsUrl."/library/metadata/".$id."";
		}
		
		date_default_timezone_set(@date_default_timezone_get());
		
		
					
		
		$xml = simplexml_load_string(file_get_contents($infoUrl)) or die ("<div class='container-fluid'><div class='row-fluid'><div class='span10 offset1'><h3>This media is no longer available in the Plex Media Server database.</h3></div></div>");
		
			if ($xml->Video['type'] == "episode") {

					$xmlArtUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$xml->Video['art']."&width=1920&height=1080";                                       
					$xmlThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$xml->Video['parentThumb']."&width=256&height=352"; 
				
							
					echo "<div class='container-fluid'>";
						echo "<div class='row-fluid'>";
							echo "<div class='span12'>";
							
							if($xml->Video['art']) {
								echo "<div class='art-face' style='background-image:url(includes/img.php?img=".urlencode($xmlArtUrl).")'>";
							}else{
								echo "<div class='art-face'>";
							}
								
								echo "<div class='summary-wrapper'>";
									echo "<div class='summary-overlay'>";
										echo "<div class='row-fluid'>";
										
											echo "<div class='span9'>";
												echo "<div class='summary-content-poster hidden-phone hidden-tablet'>";
													
													if($xml->Video['parentThumb']) {
														echo "<img src='includes/img.php?img=".urlencode($xmlThumbUrl)."'></img>";
													}elseif($xml->Video['grandparentThumb']){
														echo "<img src='includes/img.php?img=".urlencode($xmlgThumbUrl)."'></img>";
													}else{
														echo "<img src='images/poster.png'></img>";
													}
													
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
														if ($xml->Video->Writer['tag']) {
														foreach($xml->Video->Writer as $xmlWriters) {
															$writers[] = "" .$xmlWriters['tag']. "";
															if (++$writerCount == 5) break;
														}
														echo "<div class='summary-content-writers'><h6><strong>Written by</strong></h6><ul><li>";
															echo implode('<li>', $writers);
															
													}else{
														echo "<div class='summary-content-writers'><h6><strong>Written by</strong></h6><ul>";
														echo "<li>n/a";
													}
													echo "</li></div></ul>";
													echo "</div>";
												echo "</div>";
											echo "</div>";
											
										echo "</div>";
									echo "</div>";
								echo "</div>";
								
							echo "</div>";
							
							echo "</div>";	
						echo "</div>";	
					echo "</div>";	
						
		echo "<div class='container-fluid'>";
			
			echo "<div class='clear'></div>";

			echo "<div class='row-fluid'>";
				echo "<div class='span12'>";
					echo "<div class='wellbg'>";
						echo "<div class='wellheader'>";
						
							$db = dbconnect();
							
							if ($plexWatch['globalHistoryGrouping'] == "yes") {
								$plexWatchDbTable = "grouped";
							}else if ($plexWatch['globalHistoryGrouping'] == "no") {
								$plexWatchDbTable = "processed";
							}
							
							$title = $db->querySingle("SELECT title FROM $plexWatchDbTable WHERE session_id LIKE '%/metadata/".$id."\_%' ESCAPE '\'  ");
							$numRows = $db->querySingle("SELECT COUNT(*) as count FROM $plexWatchDbTable WHERE session_id LIKE '%/metadata/".$id."\_%' ESCAPE '\' ORDER BY time DESC");
							$results = $db->query("SELECT title, user, platform, time, stopped, ip_address, xml, paused_counter FROM $plexWatchDbTable WHERE session_id LIKE '%/metadata/".$id."\_%' ESCAPE '\' ORDER BY time DESC");
							
							echo "<div class='dashboard-wellheader'>";
									echo"<h3>Watching history for <strong>".$xml->Video['title']."</strong> (".$numRows." Views)</h3>";
								echo"</div>";
							echo"</div>";

							if ($numRows < 1) {

							echo "No Results.";

							} else {
							
							echo "<table id='globalHistory' class='display'>";
								echo "<thead>";
									echo "<tr>";
										echo "<th align='left'><i class='icon-sort icon-white'></i> Date</th>";
										echo "<th align='left'><i class='icon-sort icon-white'></i> User</th>";
										echo "<th align='left'><i class='icon-sort icon-white'></i> Platform</th>";
										echo "<th align='left'><i class='icon-sort icon-white'></i> IP Address</th>";
										echo "<th align='center'><i class='icon-sort icon-white'></i> Started</th>";
										echo "<th align='center'><i class='icon-sort icon-white'></i> Paused</th>";
										echo "<th align='center'><i class='icon-sort icon-white'></i> Stopped</th>";
										echo "<th align='center'><i class='icon-sort icon-white'></i> Duration</th>";
										echo "<th align='center'><i class='icon-sort icon-white'></i> Completed</th>";
									echo "</tr>";
								echo "</thead>";
								echo "<tbody>";
								$rowCount = 0;
								while ($row = $results->fetchArray()) {
								$rowCount++;
									echo "<tr>";
										echo "<td data-order='".$row['time']."' align='left'>".$row['time']."</td>";
										echo "<td align='left'><a href='user.php?user=".$row['user']."'>".FriendlyName($row['user'],$row['platform'])."</td>";
										
										$rowXml = simplexml_load_string($row['xml']); 
										$platform = $rowXml->Player['platform'];
										if ($platform == "Chromecast") {
											echo "<td align='left'><a href='#streamDetailsModal".$rowCount."' data-toggle='modal'><span class='badge badge-inverse'><i class='icon-info icon-white'></i></span></a>&nbsp".$platform."</td>";
										}else{
											echo "<td align='left'><a href='#streamDetailsModal".$rowCount."' data-toggle='modal'><span class='badge badge-inverse'><i class='icon-info icon-white'></i></span></a>&nbsp".$row['platform']."</td>";
										}

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

										
										//echo "<td align='center'></td>";
							
							

							echo "<div id='streamDetailsModal".$rowCount."' class='modal hide fade' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>";
							?>
								<div class="modal-header">	
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon icon-remove"></i></button>		
									<h3 id="myModalLabel"><i class="icon-info-sign icon-white"></i> Stream Info: <strong><?php echo $row['title']; ?> (<?php echo FriendlyName($row['user'],$row['platform']); ?>)</strong></h3>
								</div>
								
								<div class="modal-body">
									<?php
									
									if (array_key_exists('TranscodeSession',$xmlfield)) {

									?>
										<div class="span4">
											<h4>Stream Details</h4>
											<ul>
											<h5>Video</h5>
											<li>Stream Type: <strong><?php echo $xmlfield->TranscodeSession['videoDecision']; ?></strong></li>
											<li>Video Resolution: <strong><?php echo $xmlfield->TranscodeSession['height']; ?>p</strong></li>
											<li>Video Codec: <strong><?php echo $xmlfield->TranscodeSession['videoCodec']; ?></strong></li>
											<li>Video Width: <strong><?php echo $xmlfield->TranscodeSession['width']; ?></strong></li>
											<li>Video Height: <strong><?php echo $xmlfield->TranscodeSession['height']; ?></strong></li>
											</ul>
											<ul>
											<h5>Audio</h5>
											<li>Stream Type: <strong><?php echo $xmlfield->TranscodeSession['audioDecision']; ?></strong></li>
											<?php if ($xmlfield->TranscodeSession['audioCodec'] == "dca") { ?>
												<li>Audio Codec: <strong>dts</strong></li>
											<?php }else{ ?>
												<li>Audio Codec: <strong><?php echo $xmlfield->TranscodeSession['audioCodec']; ?></strong></li>
											<?php } ?>
											<li>Audio Channels: <strong><?php echo $xmlfield->TranscodeSession['audioChannels']; ?></strong></li>
											</ul>
										</div>
										<div class="span4">
											<h4>Media Source Details</h4>
											<li>Container: <strong><?php echo $xmlfield->Media['container']; ?></strong></li>
											<li>Resolution: <strong><?php echo $xmlfield->Media['videoResolution']; ?>p</strong></li>
											<li>Bitrate: <strong><?php echo $xmlfield->Media['bitrate']; ?> kbps</strong></li>
										</div>
										<div class="span4">	
											<h4>Video Source Details</h4>
											<ul>
												<li>Width: <strong><?php echo $xmlfield->Media['width']; ?></strong></li>
												<li>Height: <strong><?php echo $xmlfield->Media['height']; ?></strong></li>
												<li>Aspect Ratio: <strong><?php echo $xmlfield->Media['aspectRatio']; ?></strong></li>											
												<li>Video Frame Rate: <strong><?php echo $xmlfield->Media['videoFrameRate']; ?></strong></li>
												<li>Video Codec: <strong><?php echo $xmlfield->Media['videoCodec']; ?></strong></li>
											</ul>
											<ul> </ul>
											<h4>Audio Source Details</h4>
											<ul>
												<?php if ($xmlfield->Media['audioCodec'] == "dca") { ?>
													<li>Audio Codec: <strong>dts</strong></li>
												<?php }else{ ?>
													<li>Audio Codec: <strong><?php echo $xmlfield->Media['audioCodec']; ?></strong></li>
												<?php } ?>
												<li>Audio Channels: <strong><?php echo $xmlfield->Media['audioChannels']; ?></strong></li>
											</ul>
										</div>
										
										
									
									<?php }else{ ?>

										<div class="span4">
											<h4>Stream Details</strong></h4>
											<ul>
												<h5>Video</h5>
												<li>Stream Type: <strong>Direct Play</strong></li>
												<li>Video Resolution: <strong><?php echo $xmlfield->Media['videoResolution']; ?>p</strong></li>
												<li>Video Codec: <strong><?php echo $xmlfield->Media['videoCodec']; ?></strong></li>
												<li>Video Width: <strong><?php echo $xmlfield->Media['width']; ?></strong></li>
												<li>Video Height: <strong><?php echo $xmlfield->Media['height']; ?></strong></li>
											</ul>
											<ul>
												<h5>Audio</h5>
												<li>Stream Type: <strong>Direct Play</strong></li>
												
												<?php if ($xmlfield->Media['audioCodec'] == "dca") { ?>
														<li>Audio Codec: <strong>dts</strong></li>
													<?php }else{ ?>
														<li>Audio Codec: <strong><?php echo $xmlfield->Media['audioCodec']; ?></strong></li>
													<?php } ?>
												<li>Audio Channels: <strong><?php echo $xmlfield->Media['audioChannels']; ?></strong></li>
											</ul>
										</div>
										<div class="span4">
											<h4>Media Source Details</h4>
											<li>Container: <strong><?php echo $xmlfield->Media['container']; ?></strong></li>
											<li>Resolution: <strong><?php echo $xmlfield->Media['videoResolution']; ?>p</strong></li>
											<li>Bitrate: <strong><?php echo $xmlfield->Media['bitrate']; ?> kbps</strong></li>
										</div>
										<div class="span4">	
											<h4>Video Source Details</h4>
											<ul>
												<li>Width: <strong><?php echo $xmlfield->Media['width']; ?></strong></li>
												<li>Height: <strong><?php echo $xmlfield->Media['height']; ?></strong></li>
												<li>Aspect Ratio: <strong><?php echo $xmlfield->Media['aspectRatio']; ?></strong></li>											
												<li>Video Frame Rate: <strong><?php echo $xmlfield->Media['videoFrameRate']; ?></strong></li>
												<li>Video Codec: <strong><?php echo $xmlfield->Media['videoCodec']; ?></strong></li>
											</ul>
											<ul> </ul>
											<h4>Audio Source Details</h4>
											<ul>
												<?php if ($xmlfield->Media['audioCodec'] == "dca") { ?>
													<li>Audio Codec: <strong>dts</strong></li>
												<?php }else{ ?>
													<li>Audio Codec: <strong><?php echo $xmlfield->Media['audioCodec']; ?></strong></li>
												<?php } ?>
												<li>Audio Channels: <strong><?php echo $xmlfield->Media['audioChannels']; ?></strong></li>
											</ul>
										</div>
									<?php } ?>
									
										
										
										
								</div>
										  
								<div class="modal-footer">
								</div>

							</div>

							<?php

														
										echo "<td align='center'>".$row['time']."</td>";
										
										$paused_duration = round(abs($row['paused_counter']) / 60,1);
										echo "<td align='center'>".$paused_duration." min</td>";
										
										$stopped_time = $row['stopped'];
										
										if (empty($row['stopped'])) {								
											echo "<td align='center'>n/a</td>";
										}else{
											echo "<td align='center'>".$stopped_time."</td>";
										}

										$viewed_time = round(abs($row['stopped'] - $row['time'] - $row['paused_counter']) / 60,0);
										$viewed_time_length = strlen($viewed_time);
										
										if ($viewed_time_length == 8) {
											echo "<td align='center'>n/a</td>";
										}else{
											echo "<td align='center'>".$viewed_time. " min</td>";
										}
										
										$percentComplete = ($duration == 0 ? 0 : sprintf("%2d", ($viewOffset / $duration) * 100));
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

								$xmlArtUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$xml->Directory['art']."&width=1920&height=1080";                                       
								$xmlThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$xml->Directory['thumb']."&width=256&height=352"; 
							
							
						echo "<div class='container-fluid'>";
								
								if($xml->Directory['art']) {
									echo "<div class='art-face' style='background-image:url(includes/img.php?img=".urlencode($xmlArtUrl).")'>";
								}else{
									echo "<div class='art-face'>";
								}
								
								echo "<div class='summary-wrapper'>";
									echo "<div class='summary-overlay'>";
										echo "<div class='row-fluid'>";
										
										echo "<div class='span12'>";
											echo "<div class='summary-content-poster hidden-phone hidden-tablet'>";
											
												if($xml->Directory['thumb']) {
													echo "<img src='includes/img.php?img=".urlencode($xmlThumbUrl)."'></img>";
												}else{
													echo "<img src='images/poster.png'></img>";
												}
												
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
			echo "<div class='clear'></div>";
			
			echo "<div class='row-fluid'>";
				echo "<div class='span12'>";
					echo "<div class='wellbg'>";
						
						echo "<div class='wellheader'>";

							$db = dbconnect();
							
							if ($plexWatch['globalHistoryGrouping'] == "yes") {
								$plexWatchDbTable = "grouped";
							}else if ($plexWatch['globalHistoryGrouping'] == "no") {
								$plexWatchDbTable = "processed";
							}
							
							echo"<h3>The most watched episodes of <strong>".$xml->Directory['title']."</strong> are</h3>";	
						
						echo"</div>";
																																			
							$topWatchedResults = $db->query("SELECT title,time,user,orig_title,orig_title_ep,episode,season,xml,datetime(time, 'unixepoch') AS time, COUNT(*) AS play_count FROM $plexWatchDbTable WHERE orig_title LIKE \"".$xml->Directory['title']."\" GROUP BY title HAVING play_count > 0 ORDER BY play_count DESC,time DESC LIMIT 7");

							echo "<div class='info-top-watched-wrapper'>";
								echo "<ul class='info-top-watched-instance'>";
								// Run through each feed item
								$numRows = 0;
									
								while ($topWatchedResultsRow = $topWatchedResults->fetchArray()) {
								
									$topWatchedXmlUrl = $topWatchedResultsRow['xml'];
									$topWatchedXmlfield = simplexml_load_string($topWatchedXmlUrl) ;					

										$topWatchedThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$topWatchedXmlfield['thumb']."&width=205&height=115"; 
									

									$numRows++;

										echo "<li>";
											echo "<div class='info-top-watched-instance-position-circle'><h1>".$numRows."</h1></div>";
											echo "<div class='info-top-watched-poster'>";
												echo "<div class='info-top-watched-poster-face'><a href='info.php?id=" .$topWatchedXmlfield['ratingKey']. "'><img src='includes/img.php?img=".urlencode($topWatchedThumbUrl)."' class='info-top-watched-poster-face'></img></a></div>";
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
							
							if (!empty($plexWatch['myPlexAuthToken'])) {
								$parentInfoUrl = "".$plexWatchPmsUrl."/library/metadata/".$xml->Directory['parentRatingKey']."?X-Plex-Token=".$myPlexAuthToken."";
							}else{
								$parentInfoUrl = "".$plexWatchPmsUrl."/library/metadata/".$xml->Directory['parentRatingKey']."";
							}
							$parentXml = simplexml_load_string(file_get_contents($parentInfoUrl)) or die ("Feed Not Found");

								$xmlArtUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$xml->Directory['art']. "&width=1920&height=1080";                                       
								$xmlThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$xml->Video['parentThumb']."&width=256&height=352";
								$xmlgThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$xml->Video['grandparentThumb']."&width=256&height=352";  
							
							
						echo "<div class='container-fluid'>";	
							
								if($xml->Directory['art']) {
										
									echo "<div class='art-face' style='background-image:url(includes/img.php?img=".urlencode($xmlArtUrl).")'>";
								}else{
									echo "<div class='art-face'>";
								}
								
								echo "<div class='summary-wrapper'>";
									echo "<div class='summary-overlay'>";
										echo "<div class='row-fluid'>";
										
										echo "<div class='span9'>";
											echo "<div class='summary-content-poster hidden-phone hidden-tablet'>";
											
												if($xml->Directory['thumb']) {
													echo "<img src='includes/img.php?img=".urlencode($xmlThumbUrl)."'></img>";
												}else{
													echo "<img src='images/poster.png'></img>";
												}
												
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
						
						echo "<div class='container-fluid'>";
				
						echo "<div class='clear'></div>";
			
			echo "<div class='row-fluid'>";
				echo "<div class='span12'>";
					echo "<div class='wellbg'>";
						echo "<div class='wellheader'>";
							echo "<div class='dashboard-wellheader'>";
								echo"<h3>".$xml->Directory['title']."</h3>";
							echo "</div>"; 
						echo "</div>"; 
								
								if (!empty($plexWatch['myPlexAuthToken'])) {
									$seasonEpisodesUrl = "".$plexWatchPmsUrl."/library/metadata/".$id."/children?X-Plex-Token=".$myPlexAuthToken."";
								}else{
									$seasonEpisodesUrl = "".$plexWatchPmsUrl."/library/metadata/".$id."/children";
								}	
								$seasonEpisodesXml = simplexml_load_string(file_get_contents($seasonEpisodesUrl)) or die ("Feed Not Found");

								echo "<div class='season-episodes-wrapper'>";
									echo "<ul class='season-episodes-instance'>";
										
									foreach ($seasonEpisodesXml->Video as $seasonEpisodes) {

											$seasonEpisodesThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$seasonEpisodes['thumb']."&width=205&height=115";
										
										
											echo "<li>";
												
												echo "<div class='season-episodes-poster'>";																
													echo "<div class='season-episodes-poster-face'><a href='info.php?id=" .$seasonEpisodes['ratingKey']. "'><img src='includes/img.php?img=".urlencode($seasonEpisodesThumbUrl)."' class='season-episodes-poster-face'></img></a></div>";
													echo "<div class='season-episodes-card-overlay'><div class='season-episodes-season'>Episode ".$seasonEpisodes['index']."</div></div>";
												echo "</div>";
												echo "<div class='season-episodes-instance-text-wrapper'>";
													echo "<div class='season-episodes-title'><a href='info.php?id=".$seasonEpisodes['ratingKey']."'>\"".$seasonEpisodes['title']." \"</a></div>";
												echo "</div>";	
											echo "</li>";	
													
										  
									}
									echo "</ul>"; 
								echo "</div>";
								
						 		
					echo "</div>";
				echo "</div>";	
			echo "</div>";
								
						}else if ($xml->Video['type'] == "movie") {				

								$xmlArtUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$xml->Video['art']."&width=1920&height=1080";                                        
								$xmlThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$xml->Video['thumb']."&width=256&height=352"; 
							
							
					echo "<div class='container-fluid'>";	
						echo "<div class='row-fluid'>";
							echo "<div class='span12'>";

								if($xml->Video['art']) {

											echo "<div class='art-face' style='background-image:url(includes/img.php?img=".urlencode($xmlArtUrl).")'>";
										}else{
											echo "<div class='art-face'>";
										};
										
									echo "<div class='summary-wrapper'>";
										echo "<div class='summary-overlay'>";
											echo "<div class='row-fluid'>";
												echo "<div class='span9'>";	
													echo "<div class='summary-content-poster hidden-phone hidden-tablet'>";
														if($xml->Video['thumb']) {
															echo "<img src='includes/img.php?img=".urlencode($xmlThumbUrl)."'></img>";
														}else{
															echo "<img src='images/poster.png'></img>";
														}
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
															if ($xml->Video->Genre['tag']) {
																foreach($xml->Video->Genre as $xmlGenres) {
																	$genres[] = "" .$xmlGenres['tag']. "";
																	if (++$genreCount == 5) break;
																}
																echo "<div class='summary-content-actors'><h6><strong>Genres</strong></h6><ul><li>";
																	echo implode('<li>', $genres);
															}else{
																echo "<div class='summary-content-actors'><h6><strong>Genres</strong></h6><ul><li>";
																echo "<li>n/a";
															}
															echo "</li></div></ul>";
															$roleCount = 0;
															if ($xml->Video->Role['tag']) {
																foreach($xml->Video->Role as $Roles) {
																	$actors[] = "" .$Roles['tag']. "";
																	if (++$roleCount == 5) break;
																}
																echo "<div class='summary-content-actors'><h6><strong>Starring</strong></h6><ul><li>";
																	echo implode('<li>', $actors);
															}else{
																echo "<div class='summary-content-actors'><h6><strong>Starring</strong></h6><ul>";
																echo "<li>n/a";
															}			
															echo "</li></div></ul>";
															/*$writerCount = 0;
															if ($xml->Video->Writer['tag']) {
																foreach($xml->Video->Writer as $xmlWriters) {
																	$writers[] = "" .$xmlWriters['tag']. "";
																	if (++$writerCount == 3) break;
																}
																echo "<div class='summary-content-writers'><h6><strong>Written by</strong></h6><ul><li>";
																	echo implode('<li>', $writers);
																	
															}else{
																echo "<div class='summary-content-writers'><h6><strong>Written by</strong></h6><ul>";
																echo "<li>n/a";
															}
															echo "</li></div></ul>";
															*/
														echo "</div>";
													echo "</div>";
													
												echo "</div>";
											echo "</div>";
										echo "</div>";
									echo "</div>";
								echo "</div>";	

							echo "</div>";		
						echo "</div>";
					echo "</div>";	
						
		echo "<div class='container-fluid'>";

			echo "<div class='clear'></div>";
			
			echo "<div class='row-fluid'>";
				echo "<div class='span12'>";
					echo "<div class='wellbg'>";
						echo "<div class='wellheader'>";

							
							$db = dbconnect();

							if ($plexWatch['globalHistoryGrouping'] == "yes") {
								$plexWatchDbTable = "grouped";
							}else if ($plexWatch['globalHistoryGrouping'] == "no") {
								$plexWatchDbTable = "processed";
							}
							
							$title = $db->querySingle("SELECT title FROM $plexWatchDbTable WHERE session_id LIKE '%/metadata/".$id."\_%' ESCAPE '\'  ");

							$numRows = $db->querySingle("SELECT COUNT(*) as views FROM $plexWatchDbTable WHERE session_id LIKE '%/metadata/".$id."\_%' ESCAPE '\' ORDER BY time DESC");
							
							$results = $db->query("SELECT *, strftime('%Y%m%d', datetime(time, 'unixepoch', 'localtime')) as date FROM $plexWatchDbTable WHERE session_id LIKE '%/metadata/".$id."\_%' ESCAPE '\' ORDER BY time DESC");
							
							echo "<div class='dashboard-wellheader'>";
									echo"<h3>Watching history for <strong>".$xml->Video['title']."</strong> (".$numRows." Views)</h3>";
								echo"</div>";
							echo"</div>";
							
							if ($numRows < 1) {

							echo "No Results.";

							} else {
							
							

							echo "<table id='globalHistory' class='display'>";
								echo "<thead>";
									echo "<tr>";
										echo "<th align='left'><i class='icon-sort icon-white'></i> Date</th>";
										echo "<th align='left'><i class='icon-sort icon-white'></i> User</th>";
										echo "<th align='left'><i class='icon-sort icon-white'></i> Platform</th>";
										echo "<th align='left'><i class='icon-sort icon-white'></i> IP Address</th>";
										echo "<th align='center'><i class='icon-sort icon-white'></i> Started</th>";
										echo "<th align='center'><i class='icon-sort icon-white'></i> Paused</th>";
										echo "<th align='center'><i class='icon-sort icon-white'></i> Stopped</th>";
										echo "<th align='center'><i class='icon-sort icon-white'></i> Duration</th>";
										echo "<th align='center'><i class='icon-sort icon-white'></i> Completed</th>";
									echo "</tr>";
								echo "</thead>";
								echo "<tbody>";
									$rowCount = 0;
									while ($row = $results->fetchArray()) {
									$rowCount++;
									echo "<tr>";
										echo "<td data-order='".$row['time']."' align='left'>".$row['time']."</td>";
										echo "<td align='left'><a href='user.php?user=".$row['user']."'>".FriendlyName($row['user'],$row['platform'])."</td>";
										
										$rowXml = simplexml_load_string($row['xml']); 
										$platform = $rowXml->Player['platform'];
										if ($platform == "Chromecast") {
											echo "<td align='left'><a href='#streamDetailsModal".$rowCount."' data-toggle='modal'><span class='badge badge-inverse'><i class='icon-info icon-white'></i></span></a>&nbsp".$platform."</td>";
										}else{
											echo "<td align='left'><a href='#streamDetailsModal".$rowCount."' data-toggle='modal'><span class='badge badge-inverse'><i class='icon-info icon-white'></i></span></a>&nbsp".$row['platform']."</td>";
										}

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

										//echo "<td align='center'></td>";
							
							

							echo "<div id='streamDetailsModal".$rowCount."' class='modal hide fade' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>";
							?>
								<div class="modal-header">	
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon icon-remove"></i></button>		
									<h3 id="myModalLabel"><i class="icon-info-sign icon-white"></i> Stream Info: <strong><?php echo $row['title']; ?> (<?php echo FriendlyName($row['user'],$row['platform']); ?>)</strong></h3>
								</div>
								
								<div class="modal-body">
									<?php
									
									if (array_key_exists('TranscodeSession',$xmlfield)) {

									?>
										<div class="span4">
											<h4>Stream Details</h4>
											<ul>
											<h5>Video</h5>
											<li>Stream Type: <strong><?php echo $xmlfield->TranscodeSession['videoDecision']; ?></strong></li>
											<li>Video Resolution: <strong><?php echo $xmlfield->TranscodeSession['height']; ?>p</strong></li>
											<li>Video Codec: <strong><?php echo $xmlfield->TranscodeSession['videoCodec']; ?></strong></li>
											<li>Video Width: <strong><?php echo $xmlfield->TranscodeSession['width']; ?></strong></li>
											<li>Video Height: <strong><?php echo $xmlfield->TranscodeSession['height']; ?></strong></li>
											</ul>
											<ul>
											<h5>Audio</h5>
											<li>Stream Type: <strong><?php echo $xmlfield->TranscodeSession['audioDecision']; ?></strong></li>
											<?php if ($xmlfield->TranscodeSession['audioCodec'] == "dca") { ?>
												<li>Audio Codec: <strong>dts</strong></li>
											<?php }else{ ?>
												<li>Audio Codec: <strong><?php echo $xmlfield->TranscodeSession['audioCodec']; ?></strong></li>
											<?php } ?>
											<li>Audio Channels: <strong><?php echo $xmlfield->TranscodeSession['audioChannels']; ?></strong></li>
											</ul>
										</div>
										<div class="span4">
											<h4>Media Source Details</h4>
											<li>Container: <strong><?php echo $xmlfield->Media['container']; ?></strong></li>
											<li>Resolution: <strong><?php echo $xmlfield->Media['videoResolution']; ?>p</strong></li>
											<li>Bitrate: <strong><?php echo $xmlfield->Media['bitrate']; ?> kbps</strong></li>
										</div>
										<div class="span4">	
											<h4>Video Source Details</h4>
											<ul>
												<li>Width: <strong><?php echo $xmlfield->Media['width']; ?></strong></li>
												<li>Height: <strong><?php echo $xmlfield->Media['height']; ?></strong></li>
												<li>Aspect Ratio: <strong><?php echo $xmlfield->Media['aspectRatio']; ?></strong></li>											
												<li>Video Frame Rate: <strong><?php echo $xmlfield->Media['videoFrameRate']; ?></strong></li>
												<li>Video Codec: <strong><?php echo $xmlfield->Media['videoCodec']; ?></strong></li>
											</ul>
											<ul> </ul>
											<h4>Audio Source Details</h4>
											<ul>
												<?php if ($xmlfield->Media['audioCodec'] == "dca") { ?>
													<li>Audio Codec: <strong>dts</strong></li>
												<?php }else{ ?>
													<li>Audio Codec: <strong><?php echo $xmlfield->Media['audioCodec']; ?></strong></li>
												<?php } ?>
												<li>Audio Channels: <strong><?php echo $xmlfield->Media['audioChannels']; ?></strong></li>
											</ul>
										</div>
										
										
									
									<?php }else{ ?>

										<div class="span4">
											<h4>Stream Details</strong></h4>
											<ul>
												<h5>Video</h5>
												<li>Stream Type: <strong>Direct Play</strong></li>
												<li>Video Resolution: <strong><?php echo $xmlfield->Media['videoResolution']; ?>p</strong></li>
												<li>Video Codec: <strong><?php echo $xmlfield->Media['videoCodec']; ?></strong></li>
												<li>Video Width: <strong><?php echo $xmlfield->Media['width']; ?></strong></li>
												<li>Video Height: <strong><?php echo $xmlfield->Media['height']; ?></strong></li>
											</ul>
											<ul>
												<h5>Audio</h5>
												<li>Stream Type: <strong>Direct Play</strong></li>
												
												<?php if ($xmlfield->Media['audioCodec'] == "dca") { ?>
														<li>Audio Codec: <strong>dts</strong></li>
													<?php }else{ ?>
														<li>Audio Codec: <strong><?php echo $xmlfield->Media['audioCodec']; ?></strong></li>
													<?php } ?>
												<li>Audio Channels: <strong><?php echo $xmlfield->Media['audioChannels']; ?></strong></li>
											</ul>
										</div>
										<div class="span4">
											<h4>Media Source Details</h4>
											<li>Container: <strong><?php echo $xmlfield->Media['container']; ?></strong></li>
											<li>Resolution: <strong><?php echo $xmlfield->Media['videoResolution']; ?>p</strong></li>
											<li>Bitrate: <strong><?php echo $xmlfield->Media['bitrate']; ?> kbps</strong></li>
										</div>
										<div class="span4">	
											<h4>Video Source Details</h4>
											<ul>
												<li>Width: <strong><?php echo $xmlfield->Media['width']; ?></strong></li>
												<li>Height: <strong><?php echo $xmlfield->Media['height']; ?></strong></li>
												<li>Aspect Ratio: <strong><?php echo $xmlfield->Media['aspectRatio']; ?></strong></li>											
												<li>Video Frame Rate: <strong><?php echo $xmlfield->Media['videoFrameRate']; ?></strong></li>
												<li>Video Codec: <strong><?php echo $xmlfield->Media['videoCodec']; ?></strong></li>
											</ul>
											<ul> </ul>
											<h4>Audio Source Details</h4>
											<ul>
												<?php if ($xmlfield->Media['audioCodec'] == "dca") { ?>
													<li>Audio Codec: <strong>dts</strong></li>
												<?php }else{ ?>
													<li>Audio Codec: <strong><?php echo $xmlfield->Media['audioCodec']; ?></strong></li>
												<?php } ?>
												<li>Audio Channels: <strong><?php echo $xmlfield->Media['audioChannels']; ?></strong></li>
											</ul>
										</div>
									<?php } ?>
									
										
										
										
								</div>
										  
								<div class="modal-footer">
								</div>

							</div>

							<?php
														
										echo "<td align='center'>".$row['time']."</td>";
										
										$paused_duration = round(abs($row['paused_counter']) / 60,1);
										echo "<td align='center'>".$paused_duration." min</td>";
										
										$stopped_time = $row['stopped'];
										
										if (empty($row['stopped'])) {								
											echo "<td align='center'>n/a</td>";
										}else{
											echo "<td align='center'>".$stopped_time."</td>";
										}

										$viewed_time = round(abs($row['stopped'] - $row['time'] - $row['paused_counter']) / 60,0);
										$viewed_time_length = strlen($viewed_time);
										
										
										
										if ($viewed_time_length == 8) {
											echo "<td align='center'>n/a</td>";
										}else{
											echo "<td align='center'>".$viewed_time. " min</td>";
										}
										
										$percentComplete = ($duration == 0 ? 0 : sprintf("%2d", ($viewOffset / $duration) * 100));
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
	<script src="js/jquery.dataTables.plugin.date_sorting.js"></script>
	<script src="js/jquery.dataTables.plugin.bootstrap_pagination.js"></script>
	<script src="js/jquery.rateit.js"></script>
        <script src="js/moment-with-locale.js"></script>
	
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
			var oTable = $('#globalHistory').dataTable( {
				"bPaginate": true,
				"bLengthChange": true,
				"bFilter": true,
				"bSort": true,
				"bInfo": true,
				"bAutoWidth": true,	
				"aaSorting": [[ 0, "desc" ]],			
				"bStateSave": false,
				"bSortClasses": false,
				"sPaginationType": "bootstrap",
				"aoColumns": [
                                    {
                                    "mData": function ( source, type, val ) {
                                        if (type === 'set') {
                                          source.date = val;
                                          // Store the computed dislay and filter values for efficiency
                                          source.date_display = val=="" ? "" : moment(val,"X").format('<?php echo $plexWatch['dateFormat'];?>');
                                          source.date_filter  = val=="" ? "" : val;
                                          return;
                                        }
                                        else if (type === 'display') {
                                          return source.date_display;
                                        }
                                        else if (type === 'filter') {
                                          return source.date_filter;
                                        }
                                        // 'sort', 'type' and undefined all just use the integer
                                        return source.date;
                                    }
                                    },
                                    null,
                                    null,
                                    null,
                                    { 
                                    "bUseRendered": false,
                                    "mRender": function ( data, type, row ) {
                                        return moment(data,"X").format('<?php echo $plexWatch['timeFormat'];?>');}
                                    },
                                    null,
                                    { 
                                    "bUseRendered": false,
                                    "mRender": function ( data, type, row ) {
                                        return moment(data,"X").format('<?php echo $plexWatch['timeFormat'];?>');}
                                    },
                                    null,
                                    null
				]
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
	
  </body>
</html>