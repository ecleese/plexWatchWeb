<?php

require_once(dirname(__FILE__) . '/../config/config.php');

$plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";

$fileContents = '';

if (!empty($plexWatch['myPlexAuthToken'])) {
	$myPlexAuthToken = $plexWatch['myPlexAuthToken'];			
	if ($fileContents = file_get_contents("".$plexWatchPmsUrl."/status/sessions?X-Plex-Token=".$plexWatch['myPlexAuthToken']."")) {
      $statusSessions = simplexml_load_string($fileContents) or die ('<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>');	
   }
}else{
	$myPlexAuthToken = '';			
	if ($fileContents = file_get_contents("".$plexWatchPmsUrl."/status/sessions")) {
      $statusSessions = simplexml_load_string($fileContents) or die ('<div class=\"alert alert-warning \">Failed to access Plex Media Server. Please check your settings.</div>');	
   }
}


	
	if ($statusSessions['size'] == '0') {
		echo "<h5><strong>Nothing is currently being watched.</strong></h5><br>";
	}else{
		// Run through each feed item
			foreach ($statusSessions->Video as $sessions) {      

				if(strstr($sessions->Player['platform'], 'Roku')) {
					$platformImage = "images/platforms/roku.png";
				}else if(strstr($sessions->Player['platform'], 'Apple TV')) {
					$platformImage = "images/platforms/appletv.png";
				}else if(strstr($sessions->Player['platform'], 'Firefox')) {
					$platformImage = "images/platforms/firefox.png";
				}else if(strstr($sessions->Player['platform'], 'Chromecast')) {
					$platformImage = "images/platforms/chromecast.png";
				}else if(strstr($sessions->Player['platform'], 'Chrome')) {
					$platformImage = "images/platforms/chrome.png";
				}else if(strstr($sessions->Player['platform'], 'Android')) {
					$platformImage = "images/platforms/android.png";
				}else if(strstr($sessions->Player['platform'], 'Nexus')) {
					$platformImage = "images/platforms/android.png";
				}else if(strstr($sessions->Player['platform'], 'iPad')) {
					$platformImage = "images/platforms/ios.png";
				}else if(strstr($sessions->Player['platform'], 'iPhone')) {
					$platformImage = "images/platforms/ios.png";
				}else if(strstr($sessions->Player['platform'], 'iOS')) {
					$platformImage = "images/platforms/ios.png";
				}else if(strstr($sessions->Player['platform'], 'Plex Home Theater')) {
					$platformImage = "images/platforms/pht.png";
				}else if(strstr($sessions->Player['platform'], 'Linux/RPi-XBMC')) {
					$platformImage = "images/platforms/xbmc.png";
				}else if(strstr($sessions->Player['platform'], 'Safari')) {
					$platformImage = "images/platforms/safari.png";
				}else if(strstr($sessions->Player['platform'], 'Internet Explorer')) {
					$platformImage = "images/platforms/ie.png";
				}else if(strstr($sessions->Player['platform'], 'Unknown Browser')) {
					$platformImage = "images/platforms/default.png";
				}else if(strstr($sessions->Player['platform'], 'Windows-XBMC')) {
					$platformImage = "images/platforms/xbmc.png";
				}else if(strstr($sessions->Player['platform'], 'Xbox')) {
					$platformImage = "images/platforms/xbox.png";
				}else if(strstr($platformXmlField->Player['platform'], 'Samsung')) {
                			$platformImage = "images/platforms/samsung.png";
				}else if(empty($sessions->Player['platform'])) {
					if(strstr($sessions->Player['title'], 'Apple')) {
						$platformImage = "images/platforms/atv.png";
					//Code below matches Samsung naming standard: [Display Technology: 2 Letters][Size: 2 digits][Generation: 1 letter][Model: 4 digits]
					}else if(preg_match("/TV [a-z][a-z]\d\d[a-z]/i",$sessions->Player['title'])) {
						$platformImage = "images/platforms/samsung.png";	
					}else{
						$platformImage = "images/platforms/default.png";
					}
				}else{
					$platformImage = "images/platforms/default.png";
				}

				
				if (isset($sessions['librarySectionID'])) {
					if ($sessions['type'] == "episode") {
                    
					
						$sessionsThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$sessions['thumb']."&width=300&height=169";
					
					
					echo "<div class='instance'>";
						
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

									
								
									echo "<div class='dashboard-activity-metadata-platform'>";
										echo "<img src='".$platformImage."'></>";
									echo "</div>";
							
									if (empty($sessions->User['title'])) {
										if ($sessions->Player['state'] == "playing") {
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a> is watching";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a> has paused";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "buffering") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a> is buffering";
											echo "</div>";
										}
																	
									}else{
																	
										if ($sessions->Player['state'] == "playing") {
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a> is watching";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a> has paused";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "buffering") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a> is buffering";
											echo "</div>";
										}
									}
								
									echo "<div class='dashboard-activity-metadata-title'>"; 
										echo "<a href='info.php?id=" .$sessions['ratingKey']. "'>".$sessions['grandparentTitle']." - \"".$sessions['title']."\"</a>";
									echo "</div>";		
								
								echo "</div>";
										echo "<div id='infoDetails-".$sessions->Player['machineIdentifier']."' class='collapse out'>";
											
											echo "<div class='dashboard-activity-info-details-overlay'>";
												echo "<div class='dashboard-activity-info-details-content'>";
												
												
												
												if ($sessions->Player['state'] == "playing") {
													echo "State: <strong>Playing</strong>";
												}else if ($sessions->Player['state'] == "paused") {
													echo "State: <strong>Paused</strong>";
												}else if ($sessions->Player['state'] == "buffering") {
													echo "State: <strong>Buffering</strong>";
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
													if ($sessions->TranscodeSession['audioCodec'] == "dca") {
														echo "Audio: <strong>Direct Stream (DTS) (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
													}else if ($sessions->Media['audioCodec'] == "ac3") {
														echo "Audio: <strong>Direct Stream (AC3) (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
													}else{
														echo "Audio: <strong>Direct Stream (".$sessions->TranscodeSession['audioCodec'].") (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
													}
												}else{
													
												}

												echo "</div>";
											echo "</div>";
										
										echo "</div>";	
							echo "</div>";
							
						echo "</div>";
						echo "<div class='dashboard-activity-button-info'><button type='button' class='btn btn-warning' data-toggle='collapse' data-target='#infoDetails-".$sessions->Player['machineIdentifier']."'><i class='icon-info-sign icon-white'></i></button></div>";
						
					echo "</div>";
					}	
				}else if (!isset($sessions['librarySectionID'])) {
					if ($sessions['type'] == "episode") {
                                                                              
					echo "<div class='instance'>";
						
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

									
								
									echo "<div class='dashboard-activity-metadata-platform'>";
										echo "<img src='".$platformImage."'></>";
									echo "</div>";
							
									if (empty($sessions->User['title'])) {
										if ($sessions->Player['state'] == "playing") {
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a> is watching";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a> has paused";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "buffering") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a> is buffering";
											echo "</div>";
										}
																	
									}else{
																	
										if ($sessions->Player['state'] == "playing") {
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a> is watching";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a> has paused";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "buffering") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a> is buffering";
											echo "</div>";
										}
									}
								
									echo "<div class='dashboard-activity-metadata-title'>"; 
										echo "<a href='info.php?id=" .$sessions['ratingKey']. "'>".$sessions['grandparentTitle']." - \"".$sessions['title']."\"</a>";
									echo "</div>";		
								
								echo "</div>";
										echo "<div id='infoDetails-".$sessions->Player['machineIdentifier']."' class='collapse out'>";
											
											echo "<div class='dashboard-activity-info-details-overlay'>";
												echo "<div class='dashboard-activity-info-details-content'>";
												
												
												
												if ($sessions->Player['state'] == "playing") {
													echo "State: <strong>Playing</strong>";
												}else if ($sessions->Player['state'] == "paused") {
													echo "State: <strong>Paused</strong>";
												}else if ($sessions->Player['state'] == "buffering") {
													echo "State: <strong>Buffering</strong>";
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
													if ($sessions->TranscodeSession['audioCodec'] == "dca") {
														echo "Audio: <strong>Direct Stream (DTS) (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
													}else if ($sessions->Media['audioCodec'] == "ac3") {
														echo "Audio: <strong>Direct Stream (AC3) (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
													}else{
														echo "Audio: <strong>Direct Stream (".$sessions->TranscodeSession['audioCodec'].") (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
													}
												}else{
													
												}

												echo "</div>";
											echo "</div>";
										
										echo "</div>";	
							echo "</div>";
						echo "</div>";

						echo "<div class='dashboard-activity-button-info'><button type='button' class='btn btn-warning' data-toggle='collapse' data-target='#infoDetails-".$sessions->Player['machineIdentifier']."'><i class='icon-info-sign icon-white'></i></button></div>";
						
					echo "</div>";
					}
				}else{
				}

				if ($sessions['type'] == "movie") {
					
					
						$sessionsThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$sessions['art']."&width=300&height=169"; 
					
					
					echo "<div class='instance'>";
						
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

									echo "<div class='dashboard-activity-metadata-platform'>";
										echo "<img src='".$platformImage."'></>";
										
									echo "</div>";
							
									if (empty($sessions->User['title'])) {
										if ($sessions->Player['state'] == "playing") {
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a> is watching";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a> has paused";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "buffering") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a> is buffering";
											echo "</div>";
										}
																	
									}else{
																	
										if ($sessions->Player['state'] == "playing") {
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a> is watching";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a> has paused";
											echo "</div>";
										
										}elseif ($sessions->Player['state'] == "buffering") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a> is buffering";
											echo "</div>";
										}
									}

									echo "<div class='dashboard-activity-metadata-title'>"; 
										echo "<a href='info.php?id=" .$sessions['ratingKey']."'>".$sessions['title']."</a>";
									echo "</div>";
								
									

								echo "</div>";
								echo "<div id='infoDetails-".$sessions->Player['machineIdentifier']."' class='collapse out'>";
											
											echo "<div class='dashboard-activity-info-details-overlay'>";
												echo "<div class='dashboard-activity-info-details-content'>";
												
												
												
												if ($sessions->Player['state'] == "playing") {
													echo "State: <strong>Playing</strong>";
												}else if ($sessions->Player['state'] == "paused") {
													echo "State: <strong>Paused</strong>";
												}else if ($sessions->Player['state'] == "buffering") {
													echo "State: <strong>Buffering</strong>";
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
													if ($sessions->TranscodeSession['audioCodec'] == "dca") {
														echo "Audio: <strong>Direct Stream (DTS) (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
													}else if ($sessions->Media['audioCodec'] == "ac3") {
														echo "Audio: <strong>Direct Stream (AC3) (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
													}else{
														echo "Audio: <strong>Direct Stream (".$sessions->TranscodeSession['audioCodec'].") (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
													}
												}else{
													
												}

												echo "</div>";
											echo "</div>";
										
										echo "</div>";	
								
								
							echo "</div>";
						echo "</div>";
						echo "<div class='dashboard-activity-button-info'><button type='button' class='btn btn-warning' data-toggle='collapse' data-target='#infoDetails-".$sessions->Player['machineIdentifier']."'><i class='icon-info-sign icon-white'></i></button></div>";
						
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
										}elseif ($sessions->Player['state'] == "buffering") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a> is buffering";
											echo "</div>";
										}
									}
									echo "</div>";
									echo "<div id='infoDetails-".$sessions->Player['machineIdentifier']."' class='collapse out'>";
											
											echo "<div class='dashboard-activity-info-details-overlay'>";
												echo "<div class='dashboard-activity-info-details-content'>";
												
												echo "<br>";
												
												if ($sessions->Player['state'] == "playing") {
													echo "State: <strong>Playing</strong>";
												}else if ($sessions->Player['state'] == "paused") {
													echo "State: <strong>Paused</strong>";
												}else if ($sessions->Player['state'] == "buffering") {
													echo "State: <strong>Buffering</strong>";
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
													if ($sessions->TranscodeSession['audioCodec'] == "dca") {
														echo "Audio: <strong>Direct Stream (DTS) (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
													}else if ($sessions->Media['audioCodec'] == "ac3") {
														echo "Audio: <strong>Direct Stream (AC3) (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
													}else{
														echo "Audio: <strong>Direct Stream (".$sessions->TranscodeSession['audioCodec'].") (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
													}
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

		foreach ($statusSessions->Track as $sessions) {
			if ($sessions['type'] == "track") {
			

				if(strstr($sessions->Player['platform'], 'Roku')) {
					$platformImage = "images/platforms/roku.png";
				}else if(strstr($sessions->Player['platform'], 'Apple TV')) {
					$platformImage = "images/platforms/appletv.png";
				}else if(strstr($sessions->Player['platform'], 'Firefox')) {
					$platformImage = "images/platforms/firefox.png";
				}else if(strstr($sessions->Player['platform'], 'Chromecast')) {
					$platformImage = "images/platforms/chromecast.png";
				}else if(strstr($sessions->Player['platform'], 'Chrome')) {
					$platformImage = "images/platforms/chrome.png";
				}else if(strstr($sessions->Player['platform'], 'Android')) {
					$platformImage = "images/platforms/android.png";
				}else if(strstr($sessions->Player['platform'], 'Nexus')) {
					$platformImage = "images/platforms/android.png";
				}else if(strstr($sessions->Player['platform'], 'iPad')) {
					$platformImage = "images/platforms/ios.png";
				}else if(strstr($sessions->Player['platform'], 'iPhone')) {
					$platformImage = "images/platforms/ios.png";
				}else if(strstr($sessions->Player['platform'], 'iOS')) {
					$platformImage = "images/platforms/ios.png";
				}else if(strstr($sessions->Player['platform'], 'Plex Home Theater')) {
					$platformImage = "images/platforms/pht.png";
				}else if(strstr($sessions->Player['platform'], 'Linux/RPi-XBMC')) {
					$platformImage = "images/platforms/xbmc.png";
				}else if(strstr($sessions->Player['platform'], 'Safari')) {
					$platformImage = "images/platforms/safari.png";
				}else if(strstr($sessions->Player['platform'], 'Internet Explorer')) {
					$platformImage = "images/platforms/ie.png";
				}else if(strstr($sessions->Player['platform'], 'Unknown Browser')) {
					$platformImage = "images/platforms/default.png";
				}else if(strstr($sessions->Player['platform'], 'Windows-XBMC')) {
					$platformImage = "images/platforms/xbmc.png";
				}else if(empty($sessions->Player['platform'])) {
					if(strstr($sessions->Player['title'], 'Apple')) {
						$platformImage = "images/platforms/atv.png";
					//Code below matches Samsung naming standard: [Display Technology: 2 Letters][Size: 2 digits][Generation: 1 letter][Model: 4 digits]
					}else if(preg_match("/TV [a-z][a-z]\d\d[a-z]/i",$sessions->Player['title'])) {
						$platformImage = "images/platforms/samsung.png";	
					}else{
						$platformImage = "images/platforms/default.png";
					}
				}

                    $sessionsThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$sessions['thumb']."&width=300&height=300"; 
					
					
					echo "<div class='instance'>";
						
						echo "<div class='poster'><div class='dashboard-activity-poster-face'><div class='art-music-face' style='background-image:url(includes/img.php?img=".urlencode($sessionsThumbUrl).")'></div></div>";

							echo "<div class='dashboard-activity-metadata-wrapper'>";

								echo "<div class='dashboard-activity-instance-overlay'>";
								
									echo "<div class='dashboard-activity-metadata-progress-minutes'>";
																		
										$percentComplete = sprintf("%2d", ($sessions['viewOffset'] / $sessions['duration']) * 100);
										if ($percentComplete >= 90) {	
											$percentComplete = 100;    
										}
																			
										echo "<div class='progress progress-warning'><div class='bar' style='width: ".$percentComplete."%'>".$percentComplete."%</div></div>";												
																			
									echo "</div>";

									echo "<div class='dashboard-activity-metadata-platform'>";
										echo "<img src='".$platformImage."'></>";
										
									echo "</div>";
							
									if (empty($sessions->User['title'])) {
										if ($sessions->Player['state'] == "playing") {
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a> is playing";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a> has paused";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "buffering") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=Local'>Local</a> is buffering";
											echo "</div>";
										}
																	
									}else{
																	
										if ($sessions->Player['state'] == "playing") {
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a> is playing";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "paused") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a> has paused";
											echo "</div>";
										}elseif ($sessions->Player['state'] == "buffering") {	 
											echo "<div class='dashboard-activity-metadata-user'>";
											echo "<a href='user.php?user=".$sessions->User['title']."'>".FriendlyName($sessions->User['title'],$sessions->Player['title'])."</a> is buffering";
											echo "</div>";
										}
									}

									echo "<div class='dashboard-activity-metadata-title'>"; 
										echo $sessions['grandparentTitle']." - ".$sessions['title'];
									echo "</div>";
								
									

								echo "</div>";
								echo "<div id='infoDetails-".$sessions->Player['machineIdentifier']."' class='collapse out'>";
											
											echo "<div class='dashboard-activity-info-details-overlay'>";
												echo "<div class='dashboard-activity-info-details-content'>";
												
												
												
												echo "Artist: <strong>".$sessions['grandparentTitle']."</strong>";
												echo "<br>";
												echo "Album: <strong>".$sessions['parentTitle']."</strong>";
												echo "<br>";


												if ($sessions->Player['state'] == "playing") {
													echo "State: <strong>Playing</strong>";
												}else if ($sessions->Player['state'] == "paused") {
													echo "State: <strong>Paused</strong>";
												}else if ($sessions->Player['state'] == "buffering") {
													echo "State: <strong>Buffering</strong>";
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
													if ($sessions->TranscodeSession['audioCodec'] == "dca") {
														echo "Audio: <strong>Direct Stream (DTS) (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
													}else if ($sessions->Media['audioCodec'] == "ac3") {
														echo "Audio: <strong>Direct Stream (AC3) (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
													}else{
														echo "Audio: <strong>Direct Stream (".$sessions->TranscodeSession['audioCodec'].") (".$sessions->TranscodeSession['audioChannels']."ch)</strong>";
													}
												}else{
													
												}

												echo "</div>";
											echo "</div>";
										
										echo "</div>";	
								
								
							echo "</div>";
						echo "</div>";
						echo "<div class='dashboard-activity-button-info'><button type='button' class='btn btn-warning' data-toggle='collapse' data-target='#infoDetails-".$sessions->Player['machineIdentifier']."'><i class='icon-info-sign icon-white'></i></button></div>";
						
					echo "</div>";
				}
			}
		

	}
	
?>
