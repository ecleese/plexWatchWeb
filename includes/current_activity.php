<?php

require_once(dirname(__FILE__) . '/../config.php');

			
$statusSessions = simplexml_load_file("http://".$plexWatch['pmsUrl'].":32400/status/sessions");		
if ($statusSessions['size'] == '0') {
	echo "<h5><strong>Nothing is currently being watched.</strong></h5><br>";
}else{
// Run through each feed item
				foreach ($statusSessions->Video as $sessions) {                       
										
				if ($sessions['type'] == "episode") {
                                     
					$sessionsThumbUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$sessions['thumb']."&width=300&height=169";                                        
					
					echo "<div class='instance'>";
						
						echo "<div class='dashboard-activity-button-info'><button type='button' class='btn btn-warning' data-toggle='collapse' data-target='#infoDetails-".$sessions['ratingKey']."'><i class='icon-info-sign icon-white'></i></button></div>";
						echo "<div class='poster'><div class='dashboard-activity-poster-face'><a href='info.php?id=" .$sessions['ratingKey']. "'><img src='".$sessionsThumbUrl."'></img></a></div>";
							
									
							
							echo "<div class='dashboard-activity-metadata-wrapper'>";
							
											
							
								echo "<div class='dashboard-activity-instance-overlay'>";
								
									
								
									echo "<div class='dashboard-activity-metadata-progress-minutes'>";
																		
										$percentComplete = sprintf("%2d", ($sessions['viewOffset'] / $sessions['duration']) * 100);
										if ($percentComplete >= 90) {	
											$percentComplete = 100;    
										}
																			
										echo "<div class='progress progress-warning'><div class='bar' style='width: ".$percentComplete."%'>".$percentComplete."%</div></div>";												
																			
									echo "</div>";

									echo "<div class='dashboard-activity-metadata-title'>"; 
										echo "".$sessions['grandparentTitle']." - \"".$sessions['title']."\"";
									echo "</div>";
								
									echo "<div class='platform'>";
										echo "".$sessions->Player['title']. "";
									echo "</div>";
							
									if (empty($sessions->User['title'])) {
										if ($sessions->Player['state'] == "playing") {
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a>";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a>";
											echo "</div>";
										}
																	
									}else{
																	
										if ($sessions->Player['state'] == "playing") {
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".$sessions->User['title']."</a>";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".$sessions->User['title']."</a>";
											echo "</div>";
										}
									}
								
											
								
								echo "</div>";
										echo "<div id='infoDetails-".$sessions['ratingKey']."' class='collapse out'>";
											
											echo "<div class='dashboard-activity-info-details-overlay'>";
												echo "<div class='dashboard-activity-info-details-content'>";
												
												echo "<br>";
												
												if ($sessions->Player['state'] == "playing") {
													echo "State: <strong>Playing</strong>";
												}else if ($sessions->Player['state'] == "paused") {
													echo "State: <strong>Paused</strong>";
												}else{
												}
												
												echo "<br>";
												
												if (!array_key_exists('TranscodeSession',$sessions)) {
													echo "Stream: <strong>Direct Stream</strong>";
												}else{ 
													echo "Stream: <strong>Transcoded Stream</strong>";
												}
												
												echo "<br>";

												if (!array_key_exists('TranscodeSession',$sessions)) {
													echo "Video: <strong>".$sessions->Media['videoCodec']." (".$sessions->Media['width']."x".$sessions->Media['height']."p)</strong>";
												}else if ($sessions->TranscodeSession['videoDecision'] == "transcode") {
													echo "Video: <strong>".$sessions->TranscodeSession['videoCodec']." (".$sessions->TranscodeSession['width']."x".$sessions->TranscodeSession['height']."p)</strong>";
												}else if ($sessions->TranscodeSession['videoDecision'] == "copy") {
													echo "Video: <strong>".$sessions->TranscodeSession['videoCodec']." (".$sessions->TranscodeSession['width']."x".$sessions->TranscodeSession['height']."p)</strong>";
												}else{
												}

												echo "<br>";
												
												if (!array_key_exists('TranscodeSession',$sessions)) {
													if ($sessions->Media['audioCodec'] == "dca") {
														echo "Audio: <strong>DTS (".$sessions->Media['audioChannels']."ch)</strong>";
													}else if ($sessions->Media['audioCodec'] == "ac3") {
														echo "Audio: <strong>Dolby Digital (".$sessions->Media['audioChannels']."ch)</strong>";
													}else{
														echo "Audio: <strong>".$sessions->Media['audioCodec']." (".$sessions->Media['audioChannels']."ch)</strong>";
													}
												}else if ($sessions->TranscodeSession['audioDecision'] == "transcode") {
													echo "Audio: <strong>".$sessions->TranscodeSession['audioCodec']." (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
												}else if ($sessions->TranscodeSession['audioDecision'] == "copy") {
													echo "Audio: <strong>".$sessions->TranscodeSession['audioCodec']." (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
												}else{
													
												}

												echo "</div>";
											echo "</div>";
										
										echo "</div>";	
							echo "</div>";
						echo "</div>";
					echo "</div>";
				
					}elseif ($sessions['type'] == "movie") {
						
					$sessionsThumbUrl = "http://".$plexWatch['pmsUrl'].":32400/photo/:/transcode?url=http://127.0.0.1:32400".$sessions['art']."&width=300&height=169";                                        
					echo "<div class='instance'>";
						
						echo "<div class='dashboard-activity-button-info'><button type='button' class='btn btn-warning' data-toggle='collapse' data-target='#infoDetails-".$sessions['ratingKey']."'><i class='icon-info-sign icon-white'></i></button></div>";
						echo "<div class='poster'><div class='dashboard-activity-poster-face'><a href='info.php?id=" .$sessions['ratingKey']. "'><img src='".$sessionsThumbUrl."' ></img></a></div>";

							echo "<div class='dashboard-activity-metadata-wrapper'>";

								echo "<div class='dashboard-activity-instance-overlay'>";
								
									echo "<div class='dashboard-activity-metadata-progress-minutes'>";
																		
										$percentComplete = sprintf("%2d", ($sessions['viewOffset'] / $sessions['duration']) * 100);
										if ($percentComplete >= 90) {	
											$percentComplete = 100;    
										}
																			
										echo "<div class='progress progress-warning'><div class='bar' style='width: ".$percentComplete."%'>".$percentComplete."%</div></div>";												
																			
									echo "</div>";

									echo "<div class='dashboard-activity-metadata-title'>"; 
										echo "".$sessions['title']."";
									echo "</div>";
								
									echo "<div class='platform'>";
										echo "".$sessions->Player['title']. "";
									echo "</div>";
							
									if (empty($sessions->User['title'])) {
										if ($sessions->Player['state'] == "playing") {
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a>";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a>";
											echo "</div>";
										}
																	
									}else{
																	
										if ($sessions->Player['state'] == "playing") {
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".$sessions->User['title']."</a>";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".$sessions->User['title']."</a>";
											echo "</div>";
										}
									}
									echo "</div>";
									echo "<div id='infoDetails-".$sessions['ratingKey']."' class='collapse out'>";
											
											echo "<div class='dashboard-activity-info-details-overlay'>";
												echo "<div class='dashboard-activity-info-details-content'>";
												
												echo "<br>";
												
												if ($sessions->Player['state'] == "playing") {
													echo "State: <strong>Playing</strong>";
												}else if ($sessions->Player['state'] == "paused") {
													echo "State: <strong>Paused</strong>";
												}else{
												}
												
												echo "<br>";
												
												if (!array_key_exists('TranscodeSession',$sessions)) {
													echo "Stream: <strong>Direct Stream</strong>";
												}else{ 
													echo "Stream: <strong>Transcoded Stream</strong>";
												}
												
												echo "<br>";

												if (!array_key_exists('TranscodeSession',$sessions)) {
													echo "Video: <strong>".$sessions->Media['videoCodec']." (".$sessions->Media['width']."x".$sessions->Media['height']."p)</strong>";
												}else if ($sessions->TranscodeSession['videoDecision'] == "transcode") {
													echo "Video: <strong>".$sessions->TranscodeSession['videoCodec']." (".$sessions->TranscodeSession['width']."x".$sessions->TranscodeSession['height']."p)</strong>";
												}else if ($sessions->TranscodeSession['videoDecision'] == "copy") {
													echo "Video: <strong>".$sessions->TranscodeSession['videoCodec']." (".$sessions->TranscodeSession['width']."x".$sessions->TranscodeSession['height']."p)</strong>";
												}else{
												}

												echo "<br>";
												
												if (!array_key_exists('TranscodeSession',$sessions)) {
													if ($sessions->Media['audioCodec'] == "dca") {
														echo "Audio: <strong>DTS (".$sessions->Media['audioChannels']."ch)</strong>";
													}else if ($sessions->Media['audioCodec'] == "ac3") {
														echo "Audio: <strong>Dolby Digital (".$sessions->Media['audioChannels']."ch)</strong>";
													}else{
														echo "Audio: <strong>".$sessions->Media['audioCodec']." (".$sessions->Media['audioChannels']."ch)</strong>";
													}
												}else if ($sessions->TranscodeSession['audioDecision'] == "transcode") {
													echo "Audio: <strong>".$sessions->TranscodeSession['audioCodec']." (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
												}else if ($sessions->TranscodeSession['audioDecision'] == "copy") {
													echo "Audio: <strong>".$sessions->TranscodeSession['audioCodec']." (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
												}else{
													
												}

												echo "</div>";
											echo "</div>";
										
										echo "</div>";	
								
								
							echo "</div>";
						echo "</div>";
					echo "</div>";
			
						
					}else{
					
					}
				}	
}			
?>