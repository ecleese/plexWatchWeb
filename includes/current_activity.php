<?php

require_once(dirname(__FILE__) . '/../config/config.php');

if ($plexWatch['https'] == 'yes') {
	$plexWatchPmsUrl = "https://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpsPort']."";
}else if ($plexWatch['https'] == 'no') {
	$plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";
}

if (!empty($plexWatch['myPlexAuthToken'])) {
	$myPlexAuthToken = $plexWatch['myPlexAuthToken'];			
	$statusSessions = simplexml_load_file("".$plexWatchPmsUrl."/status/sessions?query=c&X-Plex-Token=".$plexWatch['myPlexAuthToken']."") or die ('<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>');	
}else{
	$myPlexAuthToken = '';			
	$statusSessions = simplexml_load_file("".$plexWatchPmsUrl."/status/sessions") or die ('<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>');	
}


	
	if ($statusSessions['size'] == '0') {
		echo "<h5><strong>Nothing is currently being watched.</strong></h5><br>";
	}else{
		// Run through each feed item
			foreach ($statusSessions->Video as $sessions) {                       
				
				if (isset($sessions['librarySectionID'])) {
					if ($sessions['type'] == "episode") {
                    
					
						$sessionsThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$sessions['thumb']."&width=300&height=169";
					
					
					echo "<div class='instance'>";
						
						echo "<div class='dashboard-activity-button-info'><button type='button' class='btn btn-warning' data-toggle='collapse' data-target='#infoDetails-".$sessions->Player['machineIdentifier']."'><i class='icon-info-sign icon-white'></i></button></div>";
						echo "<div class='poster'><div class='dashboard-activity-poster-face'><a href='info.php?id=" .$sessions['ratingKey']. "'><img src='includes/img.php?img=".urlencode($sessionsThumbUrl)."'></img></a></div>";
							
									
							
							echo "<div class='dashboard-activity-metadata-wrapper'>";
							
											
							
								echo "<div class='dashboard-activity-instance-overlay'>";
								
									
								
									echo "<div class='dashboard-activity-metadata-progress-minutes'>";
																		
										$percentComplete = ($sessions['duration'] == 0 ? 0 : sprintf("%2d", ($sessions['viewOffset'] / $sessions['duration']) * 100));
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
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a>";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a>";
											echo "</div>";
										}
									}
								
											
								
								echo "</div>";
										echo "<div id=\"infoDetails-".$sessions->Player['machineIdentifier']."\" class=\"collapse in\">";
											
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
													echo "Stream: <strong>Direct Play</strong>";
												}else{ 
													echo "Stream: <strong>Transcoding</strong>";
												}
												
												echo "<br>";

												if (!array_key_exists('TranscodeSession',$sessions)) {
													echo "Video: <strong>".$sessions->Media['videoCodec']." (".$sessions->Media['width']."x".$sessions->Media['height']."p)</strong>";
												}else if ($sessions->TranscodeSession['videoDecision'] == "transcode") {
													echo "Video: <strong>Transcode (".$sessions->TranscodeSession['videoCodec'].") (".$sessions->TranscodeSession['width']."x".$sessions->TranscodeSession['height']."p)</strong>";
												}else if ($sessions->TranscodeSession['videoDecision'] == "copy") {
													echo "Video: <strong>Direct Stream (".$sessions->TranscodeSession['videoCodec'].") (".$sessions->TranscodeSession['width']."x".$sessions->TranscodeSession['height']."p)</strong>";
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
													echo "Audio: <strong>Transcode (".$sessions->TranscodeSession['audioCodec'].") (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
												}else if ($sessions->TranscodeSession['audioDecision'] == "copy") {
													echo "Audio: <strong>Direct Stream (".$sessions->TranscodeSession['audioCodec'].") (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
												}else{
													
												}

												echo "</div>";
											echo "</div>";
										
										echo "</div>";	
							echo "</div>";
						echo "</div>";
					echo "</div>";
					}	
				}else if (!isset($sessions['librarySectionID'])) {
					if ($sessions['type'] == "episode") {
                                                                              
					echo "<div class='instance'>";
						
						echo "<div class='dashboard-activity-button-info'><button type='button' class='btn btn-warning' data-toggle='collapse' data-target='#infoDetails-".$sessions->Player['machineIdentifier']."'><i class='icon-info-sign icon-white'></i></button></div>";
						echo "<div class='poster'><div class='dashboard-activity-poster-face'><a href='" .$sessions['url']. "'><img src='includes/img.php?img=".urlencode($sessions['art'])."'></img></a></div>";
							
									
							
							echo "<div class='dashboard-activity-metadata-wrapper'>";
							
											
							
								echo "<div class='dashboard-activity-instance-overlay'>";
								
									
								
									echo "<div class='dashboard-activity-metadata-progress-minutes'>";
																		
										$percentComplete = ($sessions['duration'] == 0 ? 0 : sprintf("%2d", ($sessions['viewOffset'] / $sessions['duration']) * 100));
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
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a>";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a>";
											echo "</div>";
										}
									}
								
											
								
								echo "</div>";
										echo "<div id='infoDetails-".$sessions->Player['machineIdentifier']."' class='collapse in'>";
											
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
													echo "Stream: <strong>Direct Play</strong>";
												}else{ 
													echo "Stream: <strong>Transcoding</strong>";
												}
												
												echo "<br>";

												if (!array_key_exists('TranscodeSession',$sessions)) {
													echo "Video: <strong>".$sessions->Media['videoCodec']." (".$sessions->Media['width']."x".$sessions->Media['height']."p)</strong>";
												}else if ($sessions->TranscodeSession['videoDecision'] == "transcode") {
													echo "Video: <strong>Transcode (".$sessions->TranscodeSession['videoCodec'].") (".$sessions->TranscodeSession['width']."x".$sessions->TranscodeSession['height']."p)</strong>";
												}else if ($sessions->TranscodeSession['videoDecision'] == "copy") {
													echo "Video: <strong>Direct Stream (".$sessions->TranscodeSession['videoCodec'].") (".$sessions->TranscodeSession['width']."x".$sessions->TranscodeSession['height']."p)</strong>";
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
													echo "Audio: <strong>Transcode (".$sessions->TranscodeSession['audioCodec'].") (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
												}else if ($sessions->TranscodeSession['audioDecision'] == "copy") {
													echo "Audio: <strong>Direct Stream (".$sessions->TranscodeSession['audioCodec'].") (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
												}else{
													
												}

												echo "</div>";
											echo "</div>";
										
										echo "</div>";	
							echo "</div>";
						echo "</div>";
					echo "</div>";
					}
				}else{
				}

				if ($sessions['type'] == "movie") {
					
					
						$sessionsThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$sessions['art']."&width=300&height=169"; 
					
					
					echo "<div class='instance'>";
						
						echo "<div class='dashboard-activity-button-info'><button type='button' class='btn btn-warning' data-toggle='collapse' data-target='#infoDetails-".$sessions->Player['machineIdentifier']."'><i class='icon-info-sign icon-white'></i></button></div>";
						echo "<div class='poster'><div class='dashboard-activity-poster-face'><a href='info.php?id=" .$sessions['ratingKey']. "'><img src='includes/img.php?img=".urlencode($sessionsThumbUrl)."'></img></a></div>";

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
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a>";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a>";
											echo "</div>";
										}
									}
									echo "</div>";
									echo "<div id='infoDetails-".$sessions->Player['machineIdentifier']."' class='collapse in'>";
											
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
													echo "Stream: <strong>Direct Play</strong>";
												}else{ 
													echo "Stream: <strong>Transcoding</strong>";
												}
												
												echo "<br>";

												if (!array_key_exists('TranscodeSession',$sessions)) {
													echo "Video: <strong>".$sessions->Media['videoCodec']." (".$sessions->Media['width']."x".$sessions->Media['height']."p)</strong>";
												}else if ($sessions->TranscodeSession['videoDecision'] == "transcode") {
													echo "Video: <strong>Transcode (".$sessions->TranscodeSession['videoCodec'].") (".$sessions->TranscodeSession['width']."x".$sessions->TranscodeSession['height']."p)</strong>";
												}else if ($sessions->TranscodeSession['videoDecision'] == "copy") {
													echo "Video: <strong>Direct Stream (".$sessions->TranscodeSession['videoCodec'].") (".$sessions->TranscodeSession['width']."x".$sessions->TranscodeSession['height']."p)</strong>";
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
													echo "Audio: <strong>Transcode (".$sessions->TranscodeSession['audioCodec'].") (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
												}else if ($sessions->TranscodeSession['audioDecision'] == "copy") {
													echo "Audio: <strong>Direct Stream (".$sessions->TranscodeSession['audioCodec'].") (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
												}else{
													
												}

												echo "</div>";
											echo "</div>";
										
										echo "</div>";	
								
								
							echo "</div>";
						echo "</div>";
					echo "</div>";
			
					}elseif ($sessions['type'] == "clip") {
						
					$sessionsThumbUrl = "".$sessions['art']."";                                         
					echo "<div class='instance'>";
						
						echo "<div class='dashboard-activity-button-info'><button type='button' class='btn btn-warning' data-toggle='collapse' data-target='#infoDetails-".$sessions->Player['machineIdentifier']."'><i class='icon-info-sign icon-white'></i></button></div>";
						echo "<div class='poster'><div class='dashboard-activity-poster-face'><a href='info.php?id=" .$sessions['ratingKey']. "'><img src='includes/img.php?img=".urlencode($sessionsThumbUrl)."'></img></a></div>";

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
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a>";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a>";
											echo "</div>";
										}
									}
									echo "</div>";
									echo "<div id='infoDetails-".$sessions->Player['machineIdentifier']."' class='collapse in'>";
											
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
													echo "Stream: <strong>Direct Play</strong>";
												}else{ 
													echo "Stream: <strong>Transcoding</strong>";
												}
												
												echo "<br>";

												if (!array_key_exists('TranscodeSession',$sessions)) {
													echo "Video: <strong>".$sessions->Media['videoCodec']." (".$sessions->Media['width']."x".$sessions->Media['height']."p)</strong>";
												}else if ($sessions->TranscodeSession['videoDecision'] == "transcode") {
													echo "Video: <strong>Transcode (".$sessions->TranscodeSession['videoCodec'].") (".$sessions->TranscodeSession['width']."x".$sessions->TranscodeSession['height']."p)</strong>";
												}else if ($sessions->TranscodeSession['videoDecision'] == "copy") {
													echo "Video: <strong>Direct Stream (".$sessions->TranscodeSession['videoCodec'].") (".$sessions->TranscodeSession['width']."x".$sessions->TranscodeSession['height']."p)</strong>";
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
													echo "Audio: <strong>Transcode (".$sessions->TranscodeSession['audioCodec'].") (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
												}else if ($sessions->TranscodeSession['audioDecision'] == "copy") {
													echo "Audio: <strong>Direct Stream (".$sessions->TranscodeSession['audioCodec'].") (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
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