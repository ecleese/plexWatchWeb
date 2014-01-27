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
					<li class="active"><a href="history.php"><i class="icon-2x icon-calendar icon-white" data-toggle="tooltip" data-placement="bottom" title="History" id="history"></i></a></li>
					<li><a href="stats.php"><i class="icon-2x icon-tasks icon-white" data-toggle="tooltip" data-placement="bottom" title="Stats" id="stats"></i></a></li>
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
						<h2><i class="icon-large icon-calendar icon-white"></i> History</h2>
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
				echo "<div class='wellbg'>";		
					
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
						
					}else{
						$myPlexAuthToken = '';
						
					}
										
					$db = dbconnect();

					if ($plexWatch['globalHistoryGrouping'] == "yes") {
						$plexWatchDbTable = "grouped";
						$numRows = $db->querySingle("SELECT COUNT(*) as count FROM $plexWatchDbTable ");
						$results = $db->query("SELECT title, user, platform, time, stopped, ip_address, xml, paused_counter FROM processed WHERE stopped IS NULL UNION ALL SELECT title, user, platform, time, stopped, ip_address, xml, paused_counter FROM $plexWatchDbTable ORDER BY time DESC") or die ("Failed to access plexWatch database. Please check your settings.");
							
						
					}else if ($plexWatch['globalHistoryGrouping'] == "no") {
						$plexWatchDbTable = "processed";
					
						$numRows = $db->querySingle("SELECT COUNT(*) as count FROM $plexWatchDbTable ");
						$results = $db->query("SELECT title, user, platform, time, stopped, ip_address, xml, paused_counter FROM $plexWatchDbTable ORDER BY time DESC") or die ("Failed to access plexWatch database. Please check settings.");
					}	
						
					
					
	function formatBytes($bytes, $precision = 2) { 
		$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
		$bytes = max($bytes, 0); 
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($units) - 1); 
		$bytes /= (1 << (10 * $pow)); 
		return round($bytes, $precision) . ' ' . $units[$pow]; 
	} 							
					
					if ($numRows < 1) {

					echo "No Results.";

					} else {
					
					echo "<table id='globalHistory' class='display'>";
						echo "<thead>";
							echo "<tr>";
								echo "<th align='left'><i class='icon-sort icon-white'></i> Date</th>";
								echo "<th align='center'><i class='icon-sort icon-white'></i> Started</th>";
								echo "<th align='left'><i class='icon-sort icon-white'></i> User </th>";
								echo "<th align='left'><i class='icon-sort icon-white'></i> Platform</th>";
								echo "<th align='left'><i class='icon-sort icon-white'></i> IP Address</th>";
								echo "<th align='left'><i class='icon-sort icon-white'></i> Title</th>";
								echo "<th align='center'><i class='icon-sort icon-white'></i> Stream Info</th>";
					//			echo "<th align='center'><i class='icon-sort icon-white'></i> Paused</th>";
					//			echo "<th align='center'><i class='icon-sort icon-white'></i> Stopped</th>";
					//			echo "<th align='center'><i class='icon-sort icon-white'></i> Duration</th>";
								echo "<th align='center'><i class='icon-sort icon-white'></i> Data</th>";
								echo "<th align='center'><i class='icon-sort icon-white'></i> Completed</th>";
								
							echo "</tr>";
						echo "</thead>";
						echo "<tbody>";

						$rowCount = 0;
						while ($row = $results->fetchArray()) {
						
						$rowCount++;
						echo "<tr>";
							if (empty($row['stopped'])) {
											echo "<td class='currentlyWatching' align='center'>Currently watching...</td>";
										}else{
											echo "<td align='center'>".date($plexWatch['dateFormat'],$row['time'])."</td>";
		
		
		
							}
							
							echo "<td align='center'>".date($plexWatch['timeFormat'],$row['time'])."</td>";
							
							echo "<td align='left'><a href='user.php?user=".$row['user']."'>".FriendlyName($row['user'],$row['platform'])."</td>";

							
							$xml = simplexml_load_string($row['xml']); 
							$platform = $xml->Player['platform'];
							if ($platform == "Chromecast") {
								echo "<td align='left'>".$platform."</td>";
							}else{
								echo "<td align='left'>".$row['platform']."</td>";
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


							$paused_duration = round(abs($row['paused_counter']) / 60,1);
							$stopped_time = date($plexWatch['timeFormat'],$row['stopped']);
							$to_time = strtotime(date("m/d/Y g:i a",$row['stopped']));
							$from_time = strtotime(date("m/d/Y g:i a",$row['time']));
							$paused_time = strtotime(date("m/d/Y g:i a",$row['paused_counter']));
							
							
							if (empty($row['stopped'])) {								
								$stopped_time = '<span class="currentlyWatching " align="center">Currently watching...</span>';
							}else{
								$stopped_time = $stopped_time;
							}
							
							$viewed_time = round(abs($to_time - $from_time - $paused_time) / 60,0);
							$viewed_time_length = strlen($viewed_time);
							
							if ($viewed_time_length == 8) {
								$viewed_time_length = '<span class="currentlyWatching " align="center">Currently watching...</span>';
							}else{
								$viewed_time_length = $viewed_time.' min';
							}
							
							$percentComplete = ($duration == 0 ? 0 : sprintf("%2d", ($viewOffset / $duration) * 100));
								if ($percentComplete >= 90) {	
								  $percentComplete = 100;    
								}

							$size = $xmlfield->Media->Part['size'];		
							$dataTransferred = ($percentComplete / 100 * ($size));		
							

							if ($type=="movie") {
								echo "<td class='title' align='left'><a href='info.php?id=".$ratingKey."'>".$row['title']."</a></td>";
							}else if ($type=="episode") {
								echo "<td class='title' align='left'><a href='info.php?id=".$ratingKey."'>".$row['title']."</a></td>";
							}else if (!array_key_exists('',$type)) {
								echo "<td class='title' align='left'><a href='".$ratingKey."'>".$row['title']."</a></td>";
							}else{

							}

							echo "<td align='center'><a href='#streamDetailsModal".$rowCount."' data-toggle='modal'><span class='badge badge-inverse'><i class='icon-info icon-white'></i></span></a></td>";
							
							

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
											<br/><h4>Time Information</h4>
											<ul>
											<li>Start Time: <strong><?php echo date($plexWatch['timeFormat'],$row['time']); ?></strong></li>
											<li>Stop Time: <strong><?php echo $stopped_time; ?></strong></li>
											<li>Minutes Paused: <strong><?php echo $paused_duration.' min'; ?></strong></li>
											<li>Minutes Watched: <strong><?php echo $viewed_time_length; ?></strong></li>
											</ul>
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
										<div class="span4">	
											<h4>Duration</h4>
											<ul>
												<li>Width: <strong><?php echo $xmlfield->Media['width']; ?></strong></li>
												<li>Height: <strong><?php echo $xmlfield->Media['height']; ?></strong></li>
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
											</ul>
											<ul>
												<h5>Audio</h5>
												<li>Stream Type: <strong>Direct Play</strong></li>
												<li>Video Width: <strong><?php echo $xmlfield->Media['width']; ?></strong></li>
												<li>Video Height: <strong><?php echo $xmlfield->Media['height']; ?>p</strong></li>
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
											<br/><h4>Time Information</h4>
											<ul>
											<li>Start Time: <strong><?php echo date($plexWatch['timeFormat'],$row['time']); ?></strong></li>
											<li>Stop Time: <strong><?php echo $stopped_time; ?></strong></li>
											<li>Minutes Paused: <strong><?php echo $paused_duration.' min'; ?></strong></li>
											<li>Minutes Watched: <strong><?php echo $viewed_time_length; ?></strong></li>
											</ul>
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
				//			echo "<td align='center'>".date($plexWatch['timeFormat'],$row['time'])."</td>";
							
							
				//			echo "<td align='center'>".$paused_duration." min</td>";
						
							
														
				//			if (empty($row['stopped'])) {								
				//				echo "<td align='center'>n/a</td>";
				//			}else{
				//				echo "<td align='center'>".$stopped_time."</td>";
				//			}
	
							
				//			if ($viewed_time_length == 8) {
				//				echo "<td align='center'>n/a</td>";
				//			}else{
				//				echo "<td align='center'>".$viewed_time. " min</td>";
				//			}
							
							

							echo "<td align='center'>".formatBytes($dataTransferred)."</td>";			
							echo "<td align='center'><span class='badge badge-warning'>".$percentComplete."%</span></td>";
							
						echo "</tr>";  

						}
					}
						echo "</tbody>";
					echo "</table>";

					?>
						
					

				</div>
			</div>
			
		</div>
	</div>	

			

		<footer>
		
		</footer>
		
    
    
    <!-- javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-2.0.3.js"></script>
	<script src="js/bootstrap.js"></script>
	<script src="js/jquery.dataTables.js"></script>
	<script src="js/jquery.dataTables.plugin.bootstrap_pagination.js"></script>
	<script src="js/jquery.dataTables.plugin.date_sorting.js"></script>
	<script src="js/d3.v3.js"></script> 
	
	<script type="text/javascript">
		$(document).ready(function() {
			$('#globalHistory').dataTable( {
				"bPaginate": true,
				"bLengthChange": true,
				"iDisplayLength": 25,
				"bFilter": true,
				"bSort": true,
				"bInfo": true,
				"bAutoWidth": true,	
				"aaSorting": [[ 0, "desc" ]],			
				"bStateSave": false,
				"bSortClasses": false,
				"sPaginationType": "bootstrap",
				"aoColumnDefs": [
			      { "sType": "us_date", "aTargets": [ 0 ] }
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
